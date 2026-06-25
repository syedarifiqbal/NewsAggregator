<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePreferenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'preferred_sources' => ['nullable', 'array'],
            'preferred_sources.*' => ['string'],
            'preferred_categories' => ['nullable', 'array'],
            'preferred_categories.*' => ['string'],
            'preferred_authors' => ['nullable', 'array'],
            'preferred_authors.*' => ['string'],
        ];
    }
}
