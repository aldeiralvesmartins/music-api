<?php

namespace App\Services;

use Correios\Correios;
use Illuminate\Support\Facades\Log;

class CorreiosService
{
    const SERVICES = [
        '04014' => 'SEDEX',
        '04510' => 'PAC',
        '04782' => 'SEDEX 10',
        '04790' => 'SEDEX Hoje'
    ];

    protected $useRealApi = true;
    protected $timeout = 5; // seconds
    protected $codigoEmpresa;
    protected $senha;

    /**
     * Construtor
     */
    public function __construct()
    {
        // As credenciais não são mais obrigatórias para consultas básicas de frete
        $this->useRealApi = true;
    }

    /**
     * Calcula o frete para os dados fornecidos
     *
     * @param array $dados Dados para cálculo do frete
     * @return array
     * @throws \Exception
     */
    public function calcularFrete(array $dados)
    {
        try {
            $correios = new Correios(
                username: 'user',
                password: 'password',
                postcard: 'postcard',
                isTestMode: false
            );


          $result =  $correios->price()->get(
                serviceCodes:['04162'],
                products:[
                    ['weight' => 300]
                ],
                originCep:'71930000',
                destinyCep:'05336010'
            );

            debug($result);
            return $result;

        } catch (\Exception $e) {
            debug($e->getMessage());
            Log::error('Error calculating shipping with Correios: ' . $e->getMessage(), [
                'exception' => $e,
                'dados' => $dados
            ]);

            throw new \Exception('Erro ao calcular frete com os Correios. Por favor, tente novamente mais tarde.');
        }
    }
}
