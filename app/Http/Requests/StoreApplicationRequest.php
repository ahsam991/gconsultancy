<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'candidate_id' => 'required|exists:candidates,id',
            'university_id' => 'required|exists:universities,id',
            'campus_id' => 'nullable|integer',
            'course_id' => 'required|exists:courses,id',
            'intake_id' => 'nullable|integer',
            'assigned_staff_id' => 'nullable|exists:users,id',
            'assigned_manager_id' => 'nullable|exists:users,id',
            'university_ref' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:50',
            'tuition_fee' => 'nullable|numeric|min:0',
            'deposit' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|size:3',
            'deposit_due_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ];
    }
}
