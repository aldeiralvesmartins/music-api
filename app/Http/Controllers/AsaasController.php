<?php

namespace App\Http\Controllers;

use App\Services\Asaas\AsaasService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Asaas\ChargeBoletoRequest;
use App\Http\Requests\Asaas\ChargePixRequest;
use App\Http\Requests\Asaas\ChargeCardRequest;
use App\Http\Requests\Asaas\CreateCustomerRequest;
use App\Http\Requests\Asaas\CancelChargeRequest;
use App\Http\Requests\Asaas\GetChargeStatusRequest;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;

class AsaasController extends Controller
{
    public function __construct(private readonly AsaasService $asaas)
    {
    }

    /**
     * Gera cobrança via BOLETO.
     */
    public function chargeBoleto(ChargeBoletoRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Evita duplicidade por referência externa (pedido ainda não existe)
        $dup = $this->guardExternalRefDuplication($data['related_id']);
        if ($dup instanceof JsonResponse) return $dup;

        $payload = array_merge($data, ['methodId' => 2]);
        $result = $this->asaas->criarCobranca($payload);

        $this->persistPaymentFromAsaas($result, null);
        return response()->json($result);
    }

    /**
     * Gera cobrança via PIX.
     */
    public function chargePix(ChargePixRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Evita duplicidade por referência externa (pedido ainda não existe)
        $dup = $this->guardExternalRefDuplication($data['related_id']);
        if ($dup instanceof JsonResponse) return $dup;

        $payload = array_merge($data, ['methodId' => 1]);
        $result = $this->asaas->criarCobranca($payload);
        $this->persistPaymentFromAsaas($result, null);
        return response()->json($result);
    }

    /**
     * Gera cobrança via Cartão de Crédito.
     * Observação: Para cartão, a API do Asaas requer dados do cartão e do portador.
     * Caso enviados no body, serão repassados conforme documentado pela API.
     */
    public function chargeCard(ChargeCardRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Evita duplicidade por referência externa (pedido ainda não existe)
        $dup = $this->guardExternalRefDuplication($data['related_id']);
        if ($dup instanceof JsonResponse) return $dup;

        // Passa adiante os campos de cartão caso existam; o service poderá utilizá-los.
        $payload = array_merge($data, ['methodId' => 3]);
        $result = $this->asaas->criarCobranca($payload);
        $this->persistPaymentFromAsaas($result, null);
        return response()->json($result);
    }

    /**
     * Cria um cliente no Asaas e vincula ao usuário (client.id).
     */
    public function createCustomer(CreateCustomerRequest $request): JsonResponse
    {
        $data = $request->validated();
        $result = $this->asaas->criarCliente($data);
        return response()->json(['customer_id' => $result]);
    }

    /**
     * Cancela uma cobrança no Asaas.
     */
    public function cancelCharge(CancelChargeRequest $request, string $paymentId): JsonResponse
    {
        $data = $request->validated();

        $result = $this->asaas->removerCobranca(array_merge($data, [
            'payment_id' => $paymentId,
        ]));

        return response()->json(['deleted' => (bool)$result]);
    }

    /**
     * (Opcional) Webhook do Asaas.
     * Caso necessário, crie a lógica de processamento aqui.
     */
    public function webhook(Request $request): JsonResponse
    {
        // Você pode registrar logs ou atualizar entidades conforme o evento recebido
        return response()->json(['status' => 'ok']);
    }

    /**
     * Consulta o status de uma cobrança pelo ID do pagamento.
     * api_key pode ser enviada como query param ou body para subcontas.
     */
    public function getChargeStatus(GetChargeStatusRequest $request, string $paymentId): JsonResponse
    {
        $apiKey = $request->query('api_key', $request->input('api_key'));
        $result = $this->asaas->obterStatusCobranca($paymentId, $apiKey);
        return response()->json($result);
    }

    /**
     * Mapeia e persiste o pagamento retornado pelo Asaas.
     */
    private function persistPaymentFromAsaas(array $asaas, ?string $orderId = null): void
    {
        if (empty($asaas['id'])) {
            return;
        }

        $map = [
            'user_id' => Auth::id(),
            'asaas_id' => $asaas['id'] ?? null,
            'object' => $asaas['object'] ?? null,
            'date_created' => $asaas['dateCreated'] ?? null,
            'customer' => $asaas['customer'] ?? null,
            'installment' => $asaas['installment'] ?? null,
            'checkout_session' => $asaas['checkoutSession'] ?? null,
            'payment_link' => $asaas['paymentLink'] ?? null,
            'value' => $asaas['value'] ?? null,
            'net_value' => $asaas['netValue'] ?? null,
            'original_value' => $asaas['originalValue'] ?? null,
            'interest_value' => $asaas['interestValue'] ?? null,
            'description' => $asaas['description'] ?? null,
            'billing_type' => $asaas['billingType'] ?? null,
            'can_be_paid_after_due_date' => $asaas['canBePaidAfterDueDate'] ?? false,
            'pix_transaction' => $asaas['pixTransaction'] ?? null,
            'status' => $asaas['status'] ?? null,
            'due_date' => $asaas['dueDate'] ?? null,
            'original_due_date' => $asaas['originalDueDate'] ?? null,
            'payment_date' => $asaas['paymentDate'] ?? null,
            'client_payment_date' => $asaas['clientPaymentDate'] ?? null,
            'installment_number' => $asaas['installmentNumber'] ?? null,
            'invoice_url' => $asaas['invoiceUrl'] ?? null,
            'invoice_number' => $asaas['invoiceNumber'] ?? null,
            'external_reference' => $asaas['externalReference'] ?? null,
            'deleted' => $asaas['deleted'] ?? false,
            'anticipated' => $asaas['anticipated'] ?? false,
            'anticipable' => $asaas['anticipable'] ?? false,
            'credit_date' => $asaas['creditDate'] ?? null,
            'estimated_credit_date' => $asaas['estimatedCreditDate'] ?? null,
            'transaction_receipt_url' => $asaas['transactionReceiptUrl'] ?? null,
            'nosso_numero' => $asaas['nossoNumero'] ?? null,
            'bank_slip_url' => $asaas['bankSlipUrl'] ?? null,
            'last_invoice_viewed_date' => $asaas['lastInvoiceViewedDate'] ?? null,
            'last_bank_slip_viewed_date' => $asaas['lastBankSlipViewedDate'] ?? null,
            'discount' => $asaas['discount'] ?? null,
            'fine' => $asaas['fine'] ?? null,
            'interest' => $asaas['interest'] ?? null,
            'postal_service' => $asaas['postalService'] ?? false,
            'escrow' => $asaas['escrow'] ?? null,
            'refunds' => $asaas['refunds'] ?? null,
            'raw' => $asaas,
        ];

        // Vincula o pedido se informado (no fluxo atual, ainda não existe)
        $map['order_id'] = $orderId;

        Payment::updateOrCreate(
            ['asaas_id' => $asaas['id']],
            $map
        );
    }

    /**
     * Impede duplicidade de cobrança ativa usando a referência externa (related_id),
     * já que o pedido ainda não existe neste estágio do fluxo.
     */
    private function guardExternalRefDuplication(string $externalReference): JsonResponse|null
    {
        $payment = Payment::where('external_reference', $externalReference)
            ->where('deleted', false)
            ->latest()
            ->first();

        if (!$payment) {
            return null; // não existe, pode criar
        }

        $status = strtoupper((string)$payment->status);

        // Se já está pago/confirmado, bloquear nova cobrança
        if (in_array($status, ['CONFIRMED', 'RECEIVED', 'RECEIVED_IN_CASH'])) {
            return response()->json(['message' => 'Já existe uma cobrança concluída para esta referência'], 409);
        }

        // Se está pendente (ou vencido, ainda recebível), devolve o corpo já existente
        if (in_array($status, ['PENDING', 'OVERDUE'])) {
            // Preferir payload bruto salvo; caso não exista, remontar
            $body = $payment->raw ?: [
                'object' => 'payment',
                'id' => $payment->asaas_id,
                'dateCreated' => optional($payment->date_created)->format('Y-m-d'),
                'customer' => $payment->customer,
                'installment' => $payment->installment,
                'checkoutSession' => $payment->checkout_session,
                'paymentLink' => $payment->payment_link,
                'value' => (float)$payment->value,
                'netValue' => (float)$payment->net_value,
                'originalValue' => $payment->original_value ? (float)$payment->original_value : null,
                'interestValue' => $payment->interest_value ? (float)$payment->interest_value : null,
                'description' => $payment->description,
                'billingType' => $payment->billing_type,
                'canBePaidAfterDueDate' => (bool)$payment->can_be_paid_after_due_date,
                'pixTransaction' => $payment->pix_transaction,
                'status' => $payment->status,
                'dueDate' => optional($payment->due_date)->format('Y-m-d'),
                'originalDueDate' => optional($payment->original_due_date)->format('Y-m-d'),
                'paymentDate' => optional($payment->payment_date)->format('Y-m-d'),
                'clientPaymentDate' => optional($payment->client_payment_date)->format('Y-m-d'),
                'installmentNumber' => $payment->installment_number,
                'invoiceUrl' => $payment->invoice_url,
                'invoiceNumber' => $payment->invoice_number,
                'externalReference' => $payment->external_reference,
                'deleted' => (bool)$payment->deleted,
                'anticipated' => (bool)$payment->anticipated,
                'anticipable' => (bool)$payment->anticipable,
                'creditDate' => optional($payment->credit_date)->format('Y-m-d'),
                'estimatedCreditDate' => optional($payment->estimated_credit_date)->format('Y-m-d'),
                'transactionReceiptUrl' => $payment->transaction_receipt_url,
                'nossoNumero' => $payment->nosso_numero,
                'bankSlipUrl' => $payment->bank_slip_url,
                'lastInvoiceViewedDate' => optional($payment->last_invoice_viewed_date)->toIso8601String(),
                'lastBankSlipViewedDate' => optional($payment->last_bank_slip_viewed_date)->toIso8601String(),
                'discount' => $payment->discount,
                'fine' => $payment->fine,
                'interest' => $payment->interest,
                'postalService' => (bool)$payment->postal_service,
                'escrow' => $payment->escrow,
                'refunds' => $payment->refunds,
            ];
            return response()->json($body);
        }

        // Caso cancelado/deletado/etc, permitir nova cobrança
        return null;
    }
}
