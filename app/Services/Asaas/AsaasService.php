<?php

namespace App\Services\Asaas;

use App\Enums\FinancialMovement as FinancialMovementEnum;
use App\Enums\OccurrenceMovementEnum;
use App\Models\OccurrenceMovement;
use App\Models\Dojo;
use App\Models\FinancialMovement;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use function Psy\debug;

/**
 * Serviço de integração com a API do Asaas para geração de boletos.
 */
class AsaasService
{
    protected array $token;
    private array $url;
    private object $client;

    public function __construct()
    {
        $enverimenteUrl = getenv('ASAAS_DOMAIN');
        $this->url = [
            'criarConta' => "$enverimenteUrl/v3/accounts",
            'criarCobranca' => "$enverimenteUrl/v3/payments",
            'criarCliente' => "$enverimenteUrl/v3/customers",
            'verificarDocumentoAsaas' => "$enverimenteUrl/v3/myAccount/documents"
        ];
        $this->client = new Client();
    }

    /**
     * Obtém o status de uma cobrança no Asaas pelo ID do pagamento.
     * Caso seja informada uma api_key de subconta, ela será utilizada nos headers.
     */
    public function obterStatusCobranca(string $paymentId, ?string $apiKey = null): array
    {
        $headers = $apiKey ? $this->prepareHeadersCliente($apiKey) : $this->prepareHeaders();
        $response = $this->client->get($this->url['criarCobranca'] . "/{$paymentId}", [
            'headers' => $headers
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Criar Cobranca.
     * @return string|array
     * @throws GuzzleException
     * @throws Exception
     */
    public function criarCobranca($dados): array|string
    {
        try {
            $response = $this->client->post($this->url['criarCobranca'],
                ['headers' => $this->prepareHeaders(), 'json' => $this->prepararDadosParaGerarCobranca($dados)]);
           return json_decode($response->getBody()->getContents(), true);
        } catch (\Throwable $e) {
            $message = $e->getMessage();

            if (method_exists($e, 'getResponse') && $e->getResponse()) {
                $body = (string) $e->getResponse()->getBody();

                if ($this->isJson($body)) {
                    $errorData = json_decode($body, true);
                    if (!empty($errorData['error'])) {
                        $message = $errorData['error'];
                    } elseif (!empty($errorData['errors'][0]['description'])) {
                        $message = $errorData['errors'][0]['description'];
                    }
                } else {
                    $message = trim($body);
                }
            }

            throw new \Exception($message); // Joga para cima
        }
    }
    private function isJson($string)
    {
        json_decode($string);
        return (json_last_error() === JSON_ERROR_NONE);
    }

    /**
     * Remove Cobranca.
     * @return array|true
     * @throws GuzzleException
     * @throws Exception
     */
    public function removerCobranca($dados): bool|array
    {
        try {
            $response = $this->client->delete("{$this->url['criarCobranca']}/{$dados['payment_id']}",
                ['headers' => $this->prepareHeadersCliente($dados['api_key'])]);
            $retorno = json_decode($response->getBody()->getContents(), true);
            if (!array_key_exists('deleted', $retorno)) {
                throw new \Exception('Campo "deleted" não encontrado no retorno da API do Asaas.');
            }
            if (!$retorno['deleted']) {
                throw new \Exception('A cobrança não pôde ser cancelada pelo Asaas.');
            }
            unset( $dados['api_key']);
            return $this->trataRetornoRemoveCobranca($dados);
        } catch (RequestException $e) {
            return false;
        }
    }

    /**
     * Cria Cliente.
     *
     * @return string|true
     * @throws GuzzleException
     * @throws Exception
     */
    public function criarCliente($dados): bool|string
    {
        try {
            $response = $this->client->post($this->url['criarCliente'],
                ['headers' => $this->prepareHeaders(), 'json' => $this->prepararDadosParaGerarCliente($dados)]);
            $retorno = json_decode($response->getBody()->getContents(), true);

            if (!empty($retorno['id']))
                $this->CriaVinculoCliente($dados, $retorno['id']);
            return $retorno['id'];
        } catch (RequestException $e) {
            $message = $e->getMessage();

            if (method_exists($e, 'getResponse') && $e->getResponse()) {
                $body = (string) $e->getResponse()->getBody();

                if ($this->isJson($body)) {
                    $errorData = json_decode($body, true);
                    if (!empty($errorData['error'])) {
                        $message = $errorData['error'];
                    } elseif (!empty($errorData['errors'][0]['description'])) {
                        $message = $errorData['errors'][0]['description'];
                    }
                } else {
                    $message = trim($body);
                }
            }

            throw new \Exception($message); // Joga para cima
        }
    }

    /**
     * Cria SubConta.
     *
     * @return array|true
     * @throws GuzzleException
     * @throws Exception
     */
    public function CriarSubConta(): bool|array
    {
        try {
            $response = $this->client->post($this->url['criarConta'],
                ['headers' => $this->prepareHeaders(), 'json' => $this->prepararDadosParaGerarSubConta()]);
            $retorno = json_decode($response->getBody()->getContents(), true);
            return $this->trataRetorno($retorno);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $body = (string)$response->getBody();
                $errorData = json_decode($body, true);
                if (json_last_error() === JSON_ERROR_NONE && isset($errorData['errors'][0]['description'])) {
                    throw new Exception($errorData['errors'][0]['description'],);
                } else {
                    throw new Exception('Ocorreu um erro ao processar sua solicitação. Tente novamente.');
                }
            } else {
                throw new Exception('Erro ao se comunicar com o servidor.');
            }
        }
    }

    /**
     * Verificar Documento Asaas.
     *
     * @return array|true
     * @throws GuzzleException
     * @throws Exception
     */
    public function verificarDocumentoAsaas($contaApiKey): bool|array
    {
        try {
            $response = $this->client->get($this->url['verificarDocumentoAsaas'],
                ['headers' => $this->prepareHeadersCliente($contaApiKey)]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            return false;
        }
    }

    /**
     * @param $dados
     * @return array
     * @throws Exception
     */
    private function prepararDadosParaGerarSubConta(): array
    {
        $user = User::with('address')->find(auth()->user()->id);
        $phoneNumber = optional($user->phones->first())->number ?? '';
        $cpfCnpj = $this->removerCaracteresNaoNumericos($user['taxpayer']);
        $cep = $this->removerCaracteresNaoNumericos($user->address->zip_code);
        $numeroTelefone = $this->removerCaracteresNaoNumericos($phoneNumber);
        $bairro = $this->removerAcentos($user->address->neighborhood);
        $addressNumber = $this->removerAcentos($user->address->number);
        $complement = $this->removerAcentos($user->address->complement ?? '');
        $address = $this->removerAcentos($user->address->street);
        $valor = 15000;
        $companyType = 'LIMITED';
        $url = getenv('URL_WEBHOOK');
        return [
            'name' => $user['name'],
            'email' => $user['email'],
            'cpfCnpj' => $cpfCnpj,
            'birthDate' => date('Y-m-d', strtotime($user->foundation_date)) ?? null,
            'companyType' => $companyType,
            'phone' => $numeroTelefone,
            'mobilePhone' => $numeroTelefone,
            'address' => "$address, $bairro",
            'addressNumber' => $addressNumber,
            'complement' => $complement,
            'province' => $bairro,
            'postalCode' => $cep,
            'incomeValue' => $valor,
            'webhooks' => [
                [
                    'name' => "Webhook para {$user['name']}",
                    'url' => $url,
                    'email' => 'suporte@cenfit.com.br',
                    'sendType' => 'SEQUENTIALLY',
                    'interrupted' => false,
                    'enabled' => true,
                    'apiVersion' => 3,
                    'authToken' => '5tLxsL6uoN',
                    'events' => ['PAYMENT_CREATED', 'PAYMENT_UPDATED', 'PAYMENT_OVERDUE', 'PAYMENT_RECEIVED','PAYMENT_DELETED']
                ]
            ]
        ];
    }

    /**
     * @param $dados
     * @return array
     * @throws Exception
     */
    private function prepararDadosParaGerarCobranca($dados): array
    {
        $customerId = $this->getCustomerId($dados['client']['id']);

        if (!$customerId) {

            $customerId = $this->criarCliente($dados);
        }

        return array_merge([
            'customer'          => $customerId,
            'billingType'   => $this->getBillingType($dados['methodId']),
            'dueDate'           => Carbon::now()->addDays(5)->toDateString(),
            'value'             => $dados['amount'],
            'description'       => $dados['description'] ?? null,
            'externalReference' => $dados['related_id'],
            'postalService'     => false
        ], $this->regrasDeDescontoMultaJurosSplitParaPagamento($dados));
    }

    /**
     * @param $dados
     * @return array
     * @throws Exception
     */
    private function prepararDadosParaGerarCliente($dados): array
    {
        $cliente = User::find($dados['client']['id']);

        $documentoPagador = $this->removerCaracteresNaoNumericos($cliente->taxpayer);
//        $cep = $this->removerCaracteresNaoNumericos($cliente->address->zip_code);
//        $bairro = $this->removerAcentos($cliente->address->neighborhood);
//        $addressNumber = $this->removerAcentos($cliente->address->number);
//        $complement = $this->removerAcentos($cliente->address->complement);
//        $address = $this->removerAcentos($cliente->address->street);
        return [
            'name' => $cliente->name,
            'email' => $cliente->email,
            'cpfCnpj' => $documentoPagador,
//            'postalCode' => $cep,
//            'address' => $address,
//            'addressNumber' => $addressNumber,
//            'complement' => $complement,
//            'province' => $bairro,
            'externalReference' => $documentoPagador,
            'notificationDisabled' => true
        ];
    }

    /**
     * @param $dados
     * @return array
     * @throws Exception
     */
    private function trataRetorno($dados): array
    {
        return [
            'name' => 'AG:' . $dados['accountNumber']['agency'] . ' C:' . $dados['accountNumber']['account'] . '-' . $dados['accountNumber']['accountDigit'],
            'account_number' => $dados['accountNumber']['account'],
            'account_digit' => $dados['accountNumber']['accountDigit'],
            'agency' => $dados['accountNumber']['agency'],
            'wallet_id' => $dados['walletId'],
            'api_key' => $dados['apiKey'],
            'status' => true,
            'dojo_id' => auth()->user()->dojo_id,
        ];
    }

    /**
     * Trata o retorno da cobrança bancária.
     * @param array $dados
     * @return bool|string URL do boleto em caso de sucesso, false em caso de erro.
     * @throws Exception
     */
    private function trataRetornoCobranca(array $dados): bool|string
    {
        try {
            $movement = FinancialMovement::find($dados['financial_movement_id']);
            if (!$movement) {
                Log::warning("Movimentação financeira com ID {$dados['financial_movement_id']} não encontrada.");
            } else {
                $movement->boleto_link = $dados['bankSlipUrl'];
                $movement->status = FinancialMovementEnum::PendingEntry;
                $movement->save();
            }
            $ocorrencia = OccurrenceMovement::create([
                'situation' => OccurrenceMovementEnum::SITUATION_PENDING_ENTRY,
                'document_number' => $dados['invoiceNumber'],
                'our_number' => $dados['nossoNumero'],
                'financial_movement_id' => $dados['financial_movement_id'],
                'type' => OccurrenceMovementEnum::TYPE_REMESSA,
                'digitable_line' => $dados['identificationField'] ?? null,
                'payment_id' => $dados['id'],
                'boleto_info_link' => $dados['invoiceUrl'],
                'boleto_link' => $dados['bankSlipUrl'],
                'customer_id' => $dados['customer'],
                'dojo_id' => auth()->user()->dojo_id,
            ]);
            if (!empty($ocorrencia))
                return $dados['bankSlipUrl'];
        } catch (\Exception $e) {
            return false;
        }
        return false;
    }

    /**
     * Trata o retorno da cobrança baixad.
     * @param array $dados
     * @return bool|string URL do boleto em caso de sucesso, false em caso de erro.
     * @throws Exception
     */
    private function trataRetornoRemoveCobranca($dados): bool|string
    {
        try {
            $movement = FinancialMovement::find($dados['financial_movement_id']);
            if (!$movement) {
                Log::warning("Movimentação financeira com ID {$dados['financial_movement_id']} não encontrada.");
            } else {
                $movement->boleto_link = $dados['bankSlipUrl'];
                $movement->status = FinancialMovementEnum::PendingLow;
                $movement->save();
            }
            $ocorrencia = OccurrenceMovement::create([
                'situation' => OccurrenceMovementEnum::SITUATION_PENDING_LOW,
                'document_number' => $dados['document_number'],
                'our_number' => $dados['our_number'],
                'financial_movement_id' => $dados['financial_movement_id'],
                'type' => OccurrenceMovementEnum::TYPE_REMESSA,
                'digitable_line' => $dados['digitable_line'] ?? null,
                'payment_id' => $dados['payment_id'],
                'boleto_info_link' => $dados['boleto_info_link'],
                'boleto_link' => $dados['boleto_link'],
                'customer_id' => $dados['customer_id'],
                'dojo_id' => auth()->user()->dojo_id,
            ]);
            if (!empty($ocorrencia))
                return true;
        } catch (\Exception $e) {
            debug($e->getMessage());
            Log::error("Erro ao tratar retorno de cobrança: " . $e->getMessage());
            return false;
        }
        return false;
    }

    /**
     * @param $dados
     * @param $customerId
     * @throws Exception
     */
    private function CriaVinculoCliente($dados, $customerId): void
    {
        $responsavel = User::find($dados['client']['id']);
        if ($responsavel) {
            $responsavel->customer_id = $customerId;
            $responsavel->save();
        } else {
            Log::warning("Aluno com ID {$dados['client']['id']} não encontrado.");
        }
    }

    /**
     * Obtém o customer_id de um usuário pelo ID.
     *
     * @param  int|string  $saleableId
     * @return string|false
     */
    private function getCustomerId(int|string $saleableId): string|false
    {
        return User::whereKey($saleableId)->value('customer_id') ?: false;
    }

    /**
     * @param $dados
     * @return array
     * @throws \DateMalformedStringException
     */
    private function regrasDeDescontoMultaJurosSplitParaPagamento($dados): array
    {
        $dadosCobranca = [];
//        $movimentacaoRenegociacao = json_decode($dados['rateio'][0]['movimentacao_renegociacao'], true) ?? [];
//        foreach ($movimentacaoRenegociacao as $index => $rateio) {
//            if (!empty($rateio['chave']))
//                $dadosCobranca['split'][$index] = [
//                    "externalReference" => (string)$rateio['id'],
//                    "walletId" => $rateio['chave'],
//                    "fixedValue" => $rateio['valor'],
//                ];
//        }
        if (!empty($dados['desconto']) && $dados['desconto'] > 0) {
            $dataVencimento = new DateTime($dados['dataVencimento']);
            $dataDesconto = new DateTime($dados['data_desconto']);
            $diasAntesVencimento = $dataVencimento->diff($dataDesconto)->days;
            $dadosCobranca['discount'] = [
                'value' => $dados['desconto'],
                'dueDateLimitDays' => $diasAntesVencimento
            ];
        }
        if (!empty($dados['multa_em_percentual']) && $dados['multa_em_percentual'] > '0.00') {
            $dadosCobranca['fine'] = [
                'value' => $dados['multa_em_percentual']
            ];
        }
        if (!empty($dados['mora_dia_em_percentual']) && $dados['mora_dia_em_percentual'] > '0.00') {
            $dadosCobranca['interest'] = [
                'value' => $dados['mora_dia_em_percentual']
            ];
        }

        if (!empty($dados['installments'])) {
            $dadosCobranca['installmentCount']  = $dados['installments'];
            $dadosCobranca['totalValue']  = $dados['amount'];
        }

        return $dadosCobranca;
    }

    /**
     * Monta os headers necessários para requisição.
     * @return array
     */
    private function prepareHeaders(): array
    {
        return [
            'access_token' => getenv('ASAAS_KEY'),
            'Content-Type' => 'application/json'
        ];
    }

    /**
     * Monta os headers necessários para requisição.
     */
    private function prepareHeadersCliente($apiKey): array
    {
        return [
            'access_token' => $apiKey,
            'Content-Type' => 'application/json'
        ];
    }

    /**
     * Remove caracteres não numéricos de um CEP.
     * @param string $string
     * @return string
     */
    public function removerCaracteresNaoNumericos(string $string): string
    {
        return preg_replace('/\D/', '', $string);
    }

    /**
     * Remove os acentos de um texto.
     * @param ?string $texto
     * @return string
     */
    public function removerAcentos(?string $texto): string
    {
        if (is_null($texto)) {
            return '';
        }
        return strtr($texto, [
            'á' => 'a', 'à' => 'a', 'ã' => 'a', 'â' => 'a', 'ä' => 'a',
            'Á' => 'A', 'À' => 'A', 'Ã' => 'A', 'Â' => 'A', 'Ä' => 'A',
            'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
            'É' => 'E', 'È' => 'E', 'Ê' => 'E', 'Ë' => 'E',
            'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i',
            'Í' => 'I', 'Ì' => 'I', 'Î' => 'I', 'Ï' => 'I',
            'ó' => 'o', 'ò' => 'o', 'õ' => 'o', 'ô' => 'o', 'ö' => 'o',
            'Ó' => 'O', 'Ò' => 'O', 'Õ' => 'O', 'Ô' => 'O', 'Ö' => 'O',
            'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u',
            'Ú' => 'U', 'Ù' => 'U', 'Û' => 'U', 'Ü' => 'U',
            'ç' => 'c', 'Ç' => 'C'
        ]);
    }

    private function getBillingType(int|string $methodId): string
    {
        return match ($methodId) {
            2 => 'BOLETO',
            3 => 'CREDIT_CARD',
            1 => 'PIX',
            default => throw new InvalidArgumentException("Método de pagamento inválido: {$methodId}"),
        };
    }

}
