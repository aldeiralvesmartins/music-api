<?php

namespace App\Http\Requests\Asaas;

use Illuminate\Foundation\Http\FormRequest;

class GetChargeStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'api_key' => 'nullable|string',
        ];
    }
}
