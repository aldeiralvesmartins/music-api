<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Proposal;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Asaas\AsaasService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use function Psy\debug;

class PaymentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'proposal_id' => 'required|exists:proposals,id',
            'amount' => 'required|numeric',
            'fee' => 'nullable|numeric'
        ]);

        $validated['status'] = 'held';

        return Payment::create($validated);
    }

    public function release($id)
    {
        $payment = Payment::findOrFail($id);
        if ($payment->status !== 'held') return response(['error' => 'Pagamento já liberado ou inválido'], 400);

        $freelancerId = $payment->proposal->freelancer_id;
        $wallet = Wallet::firstOrCreate(['user_id' => $freelancerId]);
        $wallet->increment('balance', $payment->amount - $payment->fee);

        $payment->update(['status' => 'released']);

        return $payment;
    }

    public function deposit(Request $request)
    {
        $request->validate([
            'proposalId' => 'required|string|exists:proposals,id',
            'methodId'   => 'required|integer',
            'installments'   => 'nullable|integer',
            'amount'     => 'required|numeric|min:0.01',
        ]);

        $proposal = Proposal::findOrFail($request->proposalId);

        // Verifica permissão do cliente
        if ($proposal->project->client_id !== Auth::id()) {
            return response()->json([
                'error' => 'Você não tem permissão para pagar esta proposta.'
            ], 403);
        }

        // Verifica se já existe transação pendente
        $transaction = Transaction::where('related_id', $proposal->id)
            ->where('related_type', Proposal::class)
            ->first();

        if ($transaction?->invoice_url) {
            return response()->json([
                'message' => 'Depósito pendente já existe.',
                'balance' => $transaction->invoice_url,
            ], 200);
        }

        // Busca a wallet do cliente
        $wallet = Wallet::where('user_id', Auth::id())->firstOrFail();

        $description = "Depósito pendente para proposta {$proposal->id}";

        // Dados para a API do Asaas
        $dados = [
            'client'     => $proposal->project->client->toArray(),
            'amount'     => (float) $request->input('amount'),
            'methodId'   => $request->input('methodId'),
            'related_id' => $proposal->id,
            'description'=> $description,
        ];
        if ($request->has('installments')) {
            $dados['installments'] = $request->input('installments');
        }

        DB::beginTransaction();

        try {
            $serviceAsaas = (new AsaasService())->criarCobranca($dados);

            // Cria transação
            Transaction::create([
                'wallet_id'               => $wallet->id,
                'type'                    => 'deposit',
                'status'                  => 'pending',
                'amount'                  => $dados['amount'],
                'related_id'              => $proposal->id,
                'related_type'            => Proposal::class,
                'payment_id'              => $serviceAsaas['id'],
                'description'             => $description,
                'due_date'                => $serviceAsaas['dueDate'],
                'original_due_date'       => $serviceAsaas['originalDueDate'],
                'invoice_url'             => $serviceAsaas['invoiceUrl'],
                'invoice_number'          => $serviceAsaas['invoiceNumber'],
                'transaction_receipt_url' => $serviceAsaas['transactionReceiptUrl'],
                'nosso_numero'            => $serviceAsaas['nossoNumero'],
                'bank_slip_url'           => $serviceAsaas['bankSlipUrl'],
            ]);

            $proposal->status = 'waiting_payment';
            $proposal->save();

            // 3. Rejeita todas as outras propostas do mesmo projeto
            Proposal::where('project_id', $proposal->project_id)
                ->where('id', '!=', $proposal->id)
                ->update(['status' => 'rejected']);

            $proposal->project->update(['status' => 'waiting_payment']);
            DB::commit();

            return response()->json([
                'message' => 'Depósito pendente realizado com sucesso.',
                'balance' => $serviceAsaas['invoiceUrl'],
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'error' => 'Erro ao processar o depósito: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function depositAndLock(Request $request)
    {
        $request->validate([
            'proposalId' => 'required|string|exists:proposals,id',
            'methodId' => 'required|integer',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $proposal = Proposal::findOrFail($request->proposalId);

        if ($proposal->project->client_id !== Auth::id()) {
            return response()->json(['error' => 'Você não tem permissão para pagar esta proposta.'], 403);
        }

        // Busca a wallet do cliente
        $wallet = Wallet::where('user_id', Auth::id())->firstOrFail();

        // Verifica se saldo disponível é suficiente (balance - bloqueado)
        $lockedAmount = Transaction::where('wallet_id', $wallet->id)
            ->selectRaw("SUM(CASE WHEN type = 'lock' THEN amount ELSE 0 END) -
                         SUM(CASE WHEN type IN ('release', 'unlock') THEN amount ELSE 0 END) AS locked_amount")
            ->value('locked_amount') ?? 0;

        $availableBalance = $wallet->balance - $lockedAmount;

        if ($request->amount > $availableBalance) {
            return response()->json(['error' => 'Saldo insuficiente para realizar o depósito.'], 400);
        }

        DB::beginTransaction();

        try {


            Transaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'deposit',
                'amount' => $request->amount,
                'related_id' => $proposal->id,
                'related_type' => 'proposal',
                'description' => "Depósito para proposta {$proposal->id}",
            ]);

            // Cria transação de bloqueio
            Transaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'lock',
                'amount' => $request->amount,
                'related_id' => $proposal->id,
                'related_type' => 'proposal',
                'description' => "Bloqueio de valor para proposta {$proposal->id}",
            ]);

            // Atualiza saldo da wallet (saldo disponível diminui)
            $wallet->balance -= $request->amount;
            $wallet->save();

            // Atualiza status da proposta para refletir pagamento do depósito (opcional)
            $proposal->deposit_amount = $request->amount;
            $proposal->deposit_status = 'paid';
            $proposal->save();

            DB::commit();

            return response()->json([
                'message' => 'Depósito e bloqueio realizados com sucesso.',
                'balance' => $wallet->balance,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'error' => 'Erro ao processar o depósito: ' . $e->getMessage(),
            ], 500);
        }
    }
}
