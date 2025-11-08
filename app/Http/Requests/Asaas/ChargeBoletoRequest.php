<?php

namespace App\Http\Requests\Asaas;

use Illuminate\Foundation\Http\FormRequest;

class ChargeBoletoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client.id' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'related_id' => 'required',
            'description' => 'nullable|string',
            'installments' => 'nullable|integer|min:1',
            'desconto' => 'nullable|numeric|min:0',
            'dataVencimento' => 'nullable|date',
            'data_desconto' => 'nullable|date',
            'multa_em_percentual' => 'nullable|numeric|min:0',
            'mora_dia_em_percentual' => 'nullable|numeric|min:0',
        ];
    }
}
