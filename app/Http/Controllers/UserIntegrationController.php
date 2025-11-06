<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserIntegrationRequest;
use App\Models\UserIntegration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserIntegrationController extends Controller
{
    /**
     * Lista as integrações do usuário autenticado
     */
    public function index(): JsonResponse
    {
        $user = auth()->user();

        $integrations = $user->integrations()
            ->orderBy('category')
            ->orderBy('is_default', 'desc') // ← padrões primeiro
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $integrations,
        ]);
    }

    /**
     * Cria ou atualiza uma integração do usuário autenticado
     */
    public function store(StoreUserIntegrationRequest $request): JsonResponse
    {
        $userId = (string) auth()->id();

        $data = $request->validated();
        $category = $data['category'];
        $provider = $data['provider'];
        $name = $data['name'];

        // Se for marcada como padrão, remove o padrão das outras da mesma categoria
        if ($data['is_default'] ?? false) {
            UserIntegration::where('user_id', $userId)
                ->where('category', $category)
                ->update(['is_default' => false]);
        }

        $integration = UserIntegration::updateOrCreate(
            [
                'user_id' => $userId,
                'provider' => $provider,
                'name' => $name,
            ],
            [
                'category' => $category,
                'credentials' => $data['credentials'],
                'settings' => $data['settings'] ?? [
                        'sandbox' => true,
                        'user_agent' => 'e-co/1.0'
                    ],
                'is_active' => $data['is_active'] ?? true,
                'is_default' => $data['is_default'] ?? false,
                'connected_at' => now(),
                'refreshed_at' => now(),
            ]
        );

        return response()->json([
            'success' => true,
            'data' => $integration->fresh(),
            'message' => 'Integração salva com sucesso.'
        ], 201);
    }

    /**
     * Atualiza uma integração existente
     */
    public function update(StoreUserIntegrationRequest $request, string $id): JsonResponse
    {
        $integration = UserIntegration::where('user_id', auth()->id())
            ->where('id', $id)
            ->firstOrFail();

        $data = $request->validated();

        // Se for marcada como padrão, remove o padrão das outras da mesma categoria
        if ($data['is_default'] ?? false) {
            UserIntegration::where('user_id', auth()->id())
                ->where('category', $data['category'])
                ->where('id', '!=', $id)
                ->update(['is_default' => false]);
        }

        $integration->update([
            'category' => $data['category'],
            'provider' => $data['provider'],
            'name' => $data['name'],
            'credentials' => $data['credentials'],
            'settings' => $data['settings'] ?? $integration->settings,
            'is_active' => $data['is_active'] ?? $integration->is_active,
            'is_default' => $data['is_default'] ?? $integration->is_default,
            'refreshed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'data' => $integration->fresh(),
            'message' => 'Integração atualizada com sucesso.'
        ]);
    }

    /**
     * Define uma integração como padrão
     */
    public function setDefault(string $id): JsonResponse
    {
        $integration = UserIntegration::where('user_id', auth()->id())
            ->where('id', $id)
            ->firstOrFail();

        $integration->markAsDefault();

        return response()->json([
            'success' => true,
            'data' => $integration->fresh(),
            'message' => 'Integração definida como padrão com sucesso.'
        ]);
    }

    /**
     * Ativa ou desativa uma integração
     */
    public function toggle(string $id): JsonResponse
    {
        $integration = UserIntegration::where('user_id', auth()->id())
            ->where('id', $id)
            ->firstOrFail();

        $integration->update([
            'is_active' => !$integration->is_active,
            'refreshed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'data' => $integration->fresh(),
            'message' => $integration->is_active
                ? 'Integração ativada com sucesso.'
                : 'Integração desativada com sucesso.'
        ]);
    }

    /**
     * Remove (exclui) uma integração do usuário autenticado
     */
    public function destroy(string $id): JsonResponse
    {
        $integration = UserIntegration::where('user_id', auth()->id())
            ->where('id', $id)
            ->firstOrFail();

        $integration->delete();

        return response()->json([
            'success' => true,
            'message' => 'Integração removida com sucesso.'
        ]);
    }

    /**
     * Lista os provedores disponíveis por categoria
     */
    public function availableProviders(): JsonResponse
    {
        $providers = [
            'shipping' => [
                ['value' => 'melhor_envio', 'label' => 'Melhor Envio'],
                ['value' => 'correios', 'label' => 'Correios'],
                ['value' => 'jadlog', 'label' => 'Jadlog'],
                ['value' => 'azul_cargo', 'label' => 'Azul Cargo'],
            ],
            'payment' => [
                ['value' => 'mercadopago', 'label' => 'Mercado Pago'],
                ['value' => 'stripe', 'label' => 'Stripe'],
                ['value' => 'pagseguro', 'label' => 'PagSeguro'],
                ['value' => 'paypal', 'label' => 'PayPal'],
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $providers
        ]);
    }
}
