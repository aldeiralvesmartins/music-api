<?php

namespace App\Http\Requests\Asaas;

use Illuminate\Foundation\Http\FormRequest;

class CreateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client.id' => 'required|string',
        ];
    }
}
