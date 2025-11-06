<?php

namespace App\Http\Controllers;

use App\Http\Requests\CalculateShippingRequest;
use App\Models\Product;
use App\Services\MelhorEnvioService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ShippingController extends Controller
{
    protected $shippingService;

    public function __construct(MelhorEnvioService $shippingService)
    {
        $this->shippingService = $shippingService;
    }

    /**
     * Calcula o frete para os itens do carrinho
     *
     * @param CalculateShippingRequest $request
     * @return JsonResponse
     */
    public function calcularFrete(CalculateShippingRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            
            // Buscar CEP de origem do endereço padrão do usuário autenticado
            $cepOrigem = $validated['cep_origem'] ?? null;
            
            if (!$cepOrigem && $request->user()) {
                $defaultAddress = $request->user()->defaultAddress;
                
                if ($defaultAddress && $defaultAddress->zip_code) {
                    $cepOrigem = $defaultAddress->zip_code;
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'CEP de origem não informado e usuário não possui endereço padrão cadastrado.'
                    ], 400);
                }
            } elseif (!$cepOrigem) {
                return response()->json([
                    'success' => false,
                    'message' => 'CEP de origem é obrigatório.'
                ], 400);
            }
            
            // Buscar produtos com suas especificações
            $productIds = collect($validated['items'])->pluck('id')->toArray();
            $products = Product::with('specifications')
                ->whereIn('id', $productIds)
                ->get()
                ->keyBy('id');

            // Construir array de produtos com especificações para o cálculo
            $productsData = [];
            $totalValue = 0;

            foreach ($validated['items'] as $item) {
                $product = $products->get($item['id']);
                
                if (!$product) {
                    continue;
                }

                // Extrair dimensões e peso das especificações
                $specifications = $product->specifications->keyBy('name');
                
                $weight = $this->getSpecificationValue($specifications, ['Peso', 'peso', 'weight'], 0.3);
                $height = $this->getSpecificationValue($specifications, ['Altura', 'altura', 'height'], 2);
                $width = $this->getSpecificationValue($specifications, ['Largura', 'largura', 'width'], 11);
                $length = $this->getSpecificationValue($specifications, ['Comprimento', 'comprimento', 'length'], 16);

                $productsData[] = [
                    'id' => $product->id,
                    'weight' => (float) $weight,
                    'height' => (float) $height,
                    'width' => (float) $width,
                    'length' => (float) $length,
                    'insurance_value' => (float) $product->price,
                    'quantity' => (int) $item['quantity'],
                ];

                $totalValue += $product->price * $item['quantity'];
            }

            if (empty($productsData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhum produto válido encontrado para cálculo de frete.'
                ], 400);
            }

            // Preparar dados para o serviço de frete
            $shippingData = [
                'cep_origem' => $cepOrigem,
                'cep_destino' => $validated['cep_destino'],
                'products' => $productsData,
                'valor_declarado' => $totalValue,
                'aviso_recebimento' => $validated['aviso_recebimento'] ?? false,
                'mao_propria' => $validated['mao_propria'] ?? false,
                'coleta' => $validated['coleta'] ?? false,
            ];

            $resultado = $this->shippingService->calcularFrete($shippingData);

            if (empty($resultado)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhuma opção de frete disponível para os parâmetros informados.'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'data' => $resultado,
                'message' => 'Cotações obtidas com sucesso'
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao calcular frete: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Não foi possível calcular o frete. Por favor, tente novamente mais tarde.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Extrai valor de especificação do produto
     *
     * @param \Illuminate\Support\Collection $specifications
     * @param array $possibleNames
     * @param float $default
     * @return float
     */
    private function getSpecificationValue($specifications, array $possibleNames, float $default): float
    {
        foreach ($possibleNames as $name) {
            $spec = $specifications->get($name);
            if ($spec && is_numeric($spec->value)) {
                return (float) $spec->value;
            }
        }
        
        return $default;
    }
}
