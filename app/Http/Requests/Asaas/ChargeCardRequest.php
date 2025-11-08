<?php

namespace App\Http\Requests\Asaas;

use Illuminate\Foundation\Http\FormRequest;

class ChargeCardRequest extends FormRequest
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

            'creditCard' => 'sometimes|array',
            'creditCard.holderName' => 'required_with:creditCard|string',
            'creditCard.number' => 'required_with:creditCard|string',
            'creditCard.expiryMonth' => 'required_with:creditCard|string',
            'creditCard.expiryYear' => 'required_with:creditCard|string',
            'creditCard.ccv' => 'required_with:creditCard|string',

            'creditCardHolderInfo' => 'sometimes|array',
        ];
    }
}
