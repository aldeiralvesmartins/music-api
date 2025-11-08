<?php

namespace App\Http\Requests\Asaas;

use Illuminate\Foundation\Http\FormRequest;

class CancelChargeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'api_key' => 'required|string',
            'financial_movement_id' => 'nullable',
            'document_number' => 'nullable|string',
            'our_number' => 'nullable|string',
            'digitable_line' => 'nullable|string',
            'boleto_info_link' => 'nullable|string',
            'boleto_link' => 'nullable|string',
            'customer_id' => 'nullable|string',
            'bankSlipUrl' => 'nullable|string',
        ];
    }
}
