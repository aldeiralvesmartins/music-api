<?php

namespace App\Http\Requests\Asaas;

use Illuminate\Foundation\Http\FormRequest;

class ChargePixRequest extends FormRequest
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
        ];
    }
}
