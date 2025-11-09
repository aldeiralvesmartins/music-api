<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreSettingUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'store_name' => ['sometimes', 'string', 'max:255'],
            'primary_color' => ['nullable', 'string', 'max:50'],
            'secondary_color' => ['nullable', 'string', 'max:50'],
            'background_color' => ['nullable', 'string', 'max:50'],
            'text_color' => ['nullable', 'string', 'max:50'],
            'font_family' => ['nullable', 'string', 'max:255'],
            'logo_url' => ['nullable', 'string', 'max:2048'],
            'favicon_url' => ['nullable', 'string', 'max:2048'],
            'custom_css' => ['nullable', 'string'],
            'custom_js' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];
    }
}
