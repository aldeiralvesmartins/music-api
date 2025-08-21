<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Proposal;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use function Psy\debug;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $projectsQuery = Project::with(['categories', 'client']); // carrega relacionamento do cliente

        if ($request->has('category')) {
            $projectsQuery->whereHas('categories', function ($query) use ($request) {
                $query->where('categories.id', $request->category);
            });
        }
        if ($request->has('status')) {
            $projectsQuery->where('status', $request->status);
        }

        $projects = $projectsQuery->latest()->paginate(10);

        $projects->getCollection()->transform(function ($project) {
            $project->proposals_count = $project->proposals()->count();

            $client = $project->client;

            $project->client = [
                'id' => $client->id,
                'name' => $client->name,
                'avatar' => $client->photo, // ou avatar
                'rating' => $client->rating ?? null,
            ];

            return $project;
        });

        return response()->json($projects);
    }

    public function getProjectsbyClient()
    {
        $user = Auth::user();

        // Carrega projetos com categorias, cliente, propostas, freelancer e transações
        $projects = Project::with(['categories', 'client', 'proposals.freelancer', 'proposals.transactions'])
            ->where('client_id', $user->id)
            ->latest()
            ->get();

        $projects->each(function ($project) use ($user) {
            $project->proposals_count = $project->proposals()->count();
            $project->client = [
                'id' => $user->id,
                'name' => $user->name,
                'avatar' => $user->photo,
                'rating' => $user->rating ?? null,
            ];

            // Aqui traz a primeira transação encontrada entre todas as propostas
            $project->transaction = $project->proposals
                ->pluck('transactions')      // pega as transações de cada proposta
                ->flatten()                  // transforma em uma única coleção
                ->first();                   // pega a primeira transação, se houver
        });

        return response()->json($projects);
    }


    public function getProjectsInProgress()
    {
        $user = Auth::user();

        if ($user->type === 'freelancer') {
            $projects = Project::with(['categories', 'client', 'proposals.freelancer'])
                ->where('status', 'in_progress') // status do projeto
                ->whereHas('proposals', function ($query) use ($user) {
                    $query->where('freelancer_id', $user->id)
                        ->where('status', 'accepted'); // status da proposta
                })
                ->latest()
                ->get();
        } else {
            $projects = Project::with(['categories', 'client', 'proposals.freelancer'])
                ->where('client_id', $user->id)
                ->where('status', 'in_progress') // status do projeto
                ->latest()
                ->get();
        }

        $projects->each(function ($project) use ($user) {
            $project->proposals_count = $project->proposals()->count();
            $project->client = [
                'id' => $user->id,
                'name' => $user->name,
                'avatar' => $user->photo,
                'rating' => $user->rating ?? null,
            ];
        });

        return response()->json($projects);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'budget' => 'required|numeric',
            'deadline' => 'required|date',
            'categories' => 'required|array',
            'categories.*' => 'string|exists:categories,id',
        ]);

        $validated['client_id'] = Auth::id();

        // Cria o projeto
        $project = Project::create($validated);

        // Associa as categorias
        $project->categories()->sync($validated['categories']);

        return response()->json($project->load('categories'), 201);
    }

    public function show(Project $project)
    {
        $project->load('categories', 'client', 'proposals.freelancer')->loadCount('proposals');
        return $project;
    }

    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'budget' => 'sometimes|required|numeric',
            'deadline' => 'sometimes|required|date',
            'categories' => 'sometimes|array',
            'categories.*' => 'string|exists:categories,id',
        ]);

        $project->update($validated);

        // Atualiza as categorias se forem enviadas
        if ($request->has('categories')) {
            $project->categories()->sync($validated['categories']);
        }

        return response()->json($project->load('categories'));
    }

    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);
        $project->delete();
        return response()->noContent();
    }

    public function updateNextStatus(Request $request, $id)
    {
        $request->validate([
            'next_status' => 'required|string|in:PROPOSAL_ACCEPTED,WORK_STARTED,IN_PROGRESS,REVIEW,FINALIZED',
        ]);

        $project = Project::findOrFail($id);
        $user = Auth::user();

        // Define a ordem dos steps
        $stepsOrder = [
            'PROPOSAL_ACCEPTED' => 1,
            'WORK_STARTED' => 2,
            'IN_PROGRESS' => 3,
            'REVIEW' => 4,
            'FINALIZED' => 5,
        ];

        $currentStepOrder = $stepsOrder[$project->next_status];
        $nextStepOrder = $stepsOrder[$request->next_status];

        // Verifica se está tentando avançar no mesmo ou retroceder
        if ($nextStepOrder <= $currentStepOrder) {
            return response()->json([
                'message' => 'Não é permitido selecionar o mesmo step ou um anterior.',
            ], 422);
        }

        // Atualiza o próximo status
        $project->next_status = $request->next_status;

        // Se o usuário for cliente e marcar FINALIZED, altera status geral
        if ($user->type === 'client' && $request->next_status === 'FINALIZED') {
            try {
                $project->status = 'finished';

                // Pega a proposal aprovada
                $proposal = $project->proposals()->where('status', 'accepted')->first();

                if (!$proposal) {
                    return response()->json([
                        'message' => 'Nenhuma proposta aprovada encontrada para este projeto.'
                    ], 404);
                }

                // Pega o freelancer associado
                $freelancer = User::with('wallet')->find($proposal->freelancer_id);

                if (!$freelancer) {
                    return response()->json([
                        'message' => 'Freelancer não encontrado para a proposta aprovada.'
                    ], 404);
                }

                if (!$freelancer->wallet) {
                    return response()->json([
                        'message' => 'Freelancer não possui carteira cadastrada.'
                    ], 422);
                }

                // Cria a transação
                Transaction::create([
                    'amount'       => $proposal->amount,
                    'wallet_id'    => $freelancer->wallet->id,
                    'type'         => 'release',
                    'status'       => 'released',
                    'related_id'   => $proposal->id,
                    'related_type' => Proposal::class,
                ]);
            } catch (\Exception $e) {
                dd($e->getMessage());
                // Loga o erro para debugging
                Log::error('Erro ao finalizar projeto e criar transação: '.$e->getMessage(), [
                    'project_id' => $project->id,
                    'user_id' => $user->id,
                ]);

                return response()->json([
                    'message' => 'Ocorreu um erro ao processar a finalização do projeto.'
                ], 500);
            }
        }


        $project->save();

        return response()->json([
            'message' => 'Next status atualizado com sucesso!',
            'project' => $project,
        ]);
    }
}
