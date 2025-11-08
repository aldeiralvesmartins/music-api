<?php

namespace App\Http\Requests\Cart;

use Illuminate\Foundation\Http\FormRequest;

class UpdateShippingMethodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'provider' => 'required|string',
            'service' => 'required|string',
            'price' => 'required|numeric|min:0',
            'deadline' => 'nullable|integer|min:0',
            'additional' => 'nullable|array',
        ];
    }
}
