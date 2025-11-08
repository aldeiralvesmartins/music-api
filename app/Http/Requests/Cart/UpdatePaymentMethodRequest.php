<?php

namespace App\Http\Requests\Cart;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentMethodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'provider' => 'required|string',
            'method' => 'required|string|in:boleto,pix,card,credit_card',
            'related_id' => 'required|string',
            'additional' => 'nullable|array',
        ];
    }
}
