<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CalculateShippingRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'cep_origem' => 'nullable|string|regex:/^\d{5}-?\d{3}$/',
            'cep_destino' => 'required|string|regex:/^\d{5}-?\d{3}$/',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|string|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'aviso_recebimento' => 'nullable|boolean',
            'mao_propria' => 'nullable|boolean',
            'coleta' => 'nullable|boolean',
        ];
    }

    public function messages()
    {
        return [
            'cep_origem.regex' => 'O CEP de origem deve estar no formato válido (00000-000 ou 00000000).',
            'cep_destino.required' => 'O CEP de destino é obrigatório.',
            'cep_destino.regex' => 'O CEP de destino deve estar no formato válido (00000-000 ou 00000000).',
            'items.required' => 'É necessário informar pelo menos um item.',
            'items.*.id.required' => 'O ID do produto é obrigatório.',
            'items.*.id.exists' => 'Produto não encontrado.',
            'items.*.quantity.required' => 'A quantidade é obrigatória.',
            'items.*.quantity.min' => 'A quantidade mínima é 1.',
        ];
    }
}
