<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserIntegrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category' => 'required|string|in:shipping,payment',
            'provider' => 'required|string',
            'name' => 'required|string|max:255', // ← NOVO
            'credentials' => 'required|array',
            'credentials.token' => 'required|string',
            'credentials.refresh_token' => 'nullable|string',
            'settings' => 'nullable|array',
            'settings.sandbox' => 'nullable|boolean',
            'settings.user_agent' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'is_default' => 'nullable|boolean', // ← NOVO
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'category.required' => 'A categoria é obrigatória.',
            'category.in' => 'A categoria deve ser "shipping" ou "payment".',
            'provider.required' => 'O provedor é obrigatório.',
            'name.required' => 'O nome da integração é obrigatório.', // ← NOVO
            'name.max' => 'O nome não pode ter mais de 255 caracteres.', // ← NOVO
            'credentials.required' => 'As credenciais são obrigatórias.',
            'credentials.token.required' => 'O token é obrigatório.',
        ];
    }
}
