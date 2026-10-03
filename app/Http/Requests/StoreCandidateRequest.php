<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCandidateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('candidate')?->id ?? $this->route('candidate');
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:candidates,email,'.($id ?? 'NULL').',id',
            'phone' => 'required|string|max:50|unique:candidates,phone,'.($id ?? 'NULL').',id',
            'passport_no' => 'nullable|string|max:50',
            'gender' => 'nullable|in:male,female,other,Male,Female,Other',
            'dob' => 'nullable|date',
            'nationality' => 'nullable|string|max:100',
            'preferred_country' => 'nullable|string|max:100',
            'preferred_study_level' => 'nullable|string|max:100',
            'referral_source' => 'nullable|string|max:255',
            'current_address' => 'nullable|string',
            'permanent_address' => 'nullable|string',
            'uen_code' => 'nullable|string|max:100',
            'education_agent' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:50',
            'assigned_staff_id' => 'nullable|exists:users,id',
            'assigned_manager_id' => 'nullable|exists:users,id',
            'user_id' => 'nullable|exists:users,id',
        ];
    }
}
