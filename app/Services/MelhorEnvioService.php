<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MelhorEnvioService
{
    protected string $baseUrl;
    protected string $token;
    protected string $userAgent;

    public function __construct()
    {
        $isSandbox = (bool) config('services.melhor_envio.sandbox', true);
        $this->baseUrl = 'https://www.melhorenvio.com.br';

        $this->token = (string) config('services.melhor_envio.token');
        $this->userAgent = (string) (config('services.melhor_envio.user_agent') ?? 'e-co/1.0');
    }

    /**
     * Calcula frete via API do Melhor Envio
     *
     * @param array $dados
     * @return array
     * @throws \Exception
     */
    public function calcularFrete(array $dados): array
    {
        try {

            if (empty($this->token)) {
                throw new \Exception('Token do Melhor Envio não configurado. Defina MELHOR_ENVIO_TOKEN no .env');
            }

            $cepOrigem = preg_replace('/\D/', '', (string) ($dados['cep_origem'] ?? ''));
            $cepDestino = preg_replace('/\D/', '', (string) ($dados['cep_destino'] ?? ''));



            if (strlen($cepOrigem) !== 8 || strlen($cepDestino) !== 8) {
                throw new \Exception('CEPs inválidos. Utilize 8 dígitos numéricos.');
            }

            $payload = [
                'from' => [
                    'postal_code' => $cepOrigem,
                ],
                'to' => [
                    'postal_code' => $cepDestino,
                ],
                'options' => [
                    'receipt' => (bool) ($dados['aviso_recebimento'] ?? false),
                    'own_hand' => (bool) ($dados['mao_propria'] ?? false),
                    'collect' => (bool) ($dados['coleta'] ?? false),
                ],
            ];

            // Se informado um valor declarado total (para volumes), envia em options
            if (isset($dados['valor_declarado'])) {
                $payload['options']['insurance_value'] = (float) $dados['valor_declarado'];
            }

            // Suporte a múltiplos produtos (products) - formato do carrinho
            if (!empty($dados['products']) && is_array($dados['products'])) {
                $payload['products'] = array_values(array_map(function ($p) {
                    return [
                        'width' => (float) $p['width'],
                        'height' => (float) $p['height'],
                        'length' => (float) $p['length'],
                        'weight' => (float) $p['weight'],
                        'insurance_value' => (float) $p['insurance_value'],
                        'quantity' => (int) $p['quantity'],
                    ];
                }, $dados['products']));
            }
            // Fallback: volumes (array)
            elseif (!empty($dados['volumes']) && is_array($dados['volumes'])) {
                $payload['volumes'] = array_values(array_map(function ($v) {
                    return [
                        'height' => (float) ($v['altura'] ?? $v['height'] ?? 2),
                        'width' => (float) ($v['largura'] ?? $v['width'] ?? 11),
                        'length' => (float) ($v['comprimento'] ?? $v['length'] ?? 16),
                        'weight' => (float) ($v['peso'] ?? $v['weight'] ?? 0.1),
                        'quantity' => (int) ($v['quantidade'] ?? $v['quantity'] ?? 1),
                    ];
                }, $dados['volumes']));
            }
            // Fallback: package único
            elseif (!empty($dados['package']) && is_array($dados['package'])) {
                $pkg = $dados['package'];
                $payload['volumes'] = [[
                    'height' => (float) ($pkg['altura'] ?? $pkg['height'] ?? 2),
                    'width' => (float) ($pkg['largura'] ?? $pkg['width'] ?? 11),
                    'length' => (float) ($pkg['comprimento'] ?? $pkg['length'] ?? 16),
                    'weight' => (float) ($pkg['peso'] ?? $pkg['weight'] ?? 0.1),
                    'quantity' => (int) ($pkg['quantidade'] ?? $pkg['quantity'] ?? 1),
                ]];
            }
            // Fallback: produto único (modo legado)
            else {
                $payload['products'] = [[
                    'width' => (float) ($dados['largura'] ?? 11),
                    'height' => (float) ($dados['altura'] ?? 2),
                    'length' => (float) ($dados['comprimento'] ?? 16),
                    'weight' => (float) ($dados['peso'] ?? 0.1),
                    'insurance_value' => (float) ($dados['valor_declarado'] ?? 0),
                    'quantity' => (int) ($dados['quantidade'] ?? 1),
                ]];
            }

            // Permite filtrar serviços específicos (ids do Melhor Envio), se fornecido
            if (!empty($dados['services']) && is_array($dados['services'])) {
                $payload['services'] = array_values(array_filter($dados['services']));
            }

            // Log do payload para debug
            Log::info('Melhor Envio - Payload enviado', ['payload' => $payload]);

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->token,
                'User-Agent' => $this->userAgent,
            ])->post(rtrim($this->baseUrl, '/') . '/api/v2/me/shipment/calculate', $payload);

            if (!$response->successful()) {
                $status = $response->status();
                $body = $response->json() ?? $response->body();

                debug([
                    'status' => $status,
                    'body' => $body,
                ]);

                Log::error('Melhor Envio calcularFrete falhou', [
                    'status' => $status,
                    'body' => $body,
                    'payload' => $payload,
                ]);

                // Tratamento específico para erro 422 (validação)
                if ($status === 422 && isset($body['errors'])) {
                    $errors = $body['errors'];
                    $errorMessages = [];

                    foreach ($errors as $field => $messages) {
                        if (is_array($messages)) {
                            $errorMessages[] = implode(', ', $messages);
                        }
                    }

                    $errorMessage = !empty($errorMessages)
                        ? 'Erro de validação: ' . implode('; ', $errorMessages)
                        : 'Dados inválidos para cálculo de frete.';

                    throw new \Exception($errorMessage);
                }

                throw new \Exception('Falha ao consultar frete no Melhor Envio. Código HTTP: ' . $status);
            }

            $data = $response->json();
            return $data ?? [];
        } catch (\Throwable $e) {
            debug($e->getMessage());
            Log::error('Erro ao calcular frete (Melhor Envio): ' . $e->getMessage(), [
                'exception' => $e,
                'dados' => $dados,
            ]);
            throw new \Exception('Erro ao calcular frete no Melhor Envio. Tente novamente mais tarde.');
        }
    }
}
