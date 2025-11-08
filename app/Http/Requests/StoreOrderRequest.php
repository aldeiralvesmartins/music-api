<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determina se o usuário está autorizado a fazer esta requisição.
     */
    public function authorize()
    {
        return true; // ou use regras de permissão se quiser
    }

    /**
     * Define as regras de validação da requisição.
     */
    public function rules()
    {
        return [
            'address_id' => 'required|exists:addresses,id',
            'payment_method' => 'required|string|in:pix,cartao,dinheiro,credit_card,boleto',
        ];
    }
}
