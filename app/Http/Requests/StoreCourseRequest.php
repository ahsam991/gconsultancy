<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'university_id' => 'required|exists:universities,id',
            'campus_id' => 'nullable|integer',
            'subject_id' => 'nullable|integer',
            'study_level_id' => 'nullable|integer',
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:100',
            'study_mode' => 'nullable|string|max:100',
            'duration_years' => 'nullable|integer|min:0|max:10',
            'tuition_fee' => 'required|numeric|min:0',
            'currency' => 'nullable|string|size:3',
            'deposit' => 'nullable|numeric|min:0',
            'ielts_req' => 'nullable|numeric|min:0|max:9',
            'toefl_req' => 'nullable|numeric|min:0|max:120',
            'gpa_req' => 'nullable|numeric|min:0|max:5',
            'intake' => 'nullable|string|max:255',
            'intake_start' => 'nullable|date',
            'intake_end' => 'nullable|date',
            'url' => 'nullable|url|max:500',
            'active' => 'nullable|boolean',
            'featured' => 'nullable|boolean',
            'description' => 'nullable|string',
        ];
    }
}
