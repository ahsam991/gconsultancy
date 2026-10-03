<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUniversityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'country_id' => 'nullable|exists:countries,id',
            'website' => 'nullable|url|max:255',
            'partner_status' => 'nullable|string|max:100',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'agreement_start' => 'nullable|date',
            'agreement_end' => 'nullable|date|after_or_equal:agreement_start',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
            'active' => 'nullable|boolean',
            'featured' => 'nullable|boolean',
        ];
    }
}
