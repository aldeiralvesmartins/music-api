<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:companies,slug'],
            'domain' => ['required', 'string', 'max:255', 'unique:companies,domain'],
            'type' => ['required', 'in:subdomain,custom_domain'],
            'owner_id' => ['nullable', 'string', 'exists:users,id'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
