<?php

namespace App\Http\Controllers;

use App\Services\CorreiosService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RastreamentoController extends Controller
{
    /**
     * @var CorreiosService
     */
    protected $correiosService;

    /**
     * Construtor
     *
     * @param CorreiosService $correiosService
     */
    public function __construct(CorreiosService $correiosService)
    {
        $this->correiosService = $correiosService;
    }

    /**
     * Rastreia um objeto nos Correios
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function rastrear(Request $request): JsonResponse
    {
        $request->validate([
            'codigo' => 'required|string|min:13|max:13',
        ]);

        try {
            $resultado = $this->correiosService->rastrearObjeto($request->input('codigo'));
            
            if ($resultado['success']) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'codigo' => $resultado['codigo'],
                        'entregue' => $resultado['entregue'],
                        'eventos' => $resultado['eventos']
                    ],
                    'message' => $resultado['mensagem']
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => $resultado['mensagem'],
                'error' => $resultado['erro'] ?? null,
                'code' => $resultado['codigo'] ?? null
            ], 400);
            
        } catch (\Exception $e) {
            Log::error('Erro ao processar rastreamento: ' . $e->getMessage(), [
                'exception' => $e,
                'codigo' => $request->input('codigo')
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro inesperado ao processar o rastreamento.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
