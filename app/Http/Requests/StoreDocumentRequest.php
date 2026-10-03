<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'candidate_id' => 'required|exists:candidates,id',
            'application_id' => 'nullable|exists:applications,id',
            'document_type_id' => 'nullable|exists:document_types,id',
            'type' => 'nullable|string|max:100',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
            'notes' => 'nullable|string',
        ];
    }
}
