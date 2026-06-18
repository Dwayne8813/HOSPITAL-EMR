<?php

declare(strict_types=1);

namespace App\Http\Requests\Clinical;

use Illuminate\Foundation\Http\FormRequest;

class StoreEvolutionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'medical_record_id' => ['required', 'integer', 'exists:medical_records,id'],
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'evolution_type' => ['required', 'string', 'in:progress,note,interconsultation,discharge'],
            'description' => ['required', 'string', 'max:10000'],
            'clinical_findings' => ['nullable', 'string', 'max:5000'],
            'treatment_plan' => ['nullable', 'string', 'max:5000'],
            'medications' => ['nullable', 'array'],
            'medications.*.name' => ['required_with:medications', 'string', 'max:255'],
            'medications.*.dosage' => ['required_with:medications', 'string', 'max:100'],
            'medications.*.frequency' => ['required_with:medications', 'string', 'max:100'],
            'evolution_date' => ['nullable', 'date', 'before_or_equal:now'],
            'next_review_date' => ['nullable', 'date', 'after:now'],
            'is_critical' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'medical_record_id.required' => 'Medical record is required.',
            'medical_record_id.exists' => 'Selected medical record does not exist.',
            'patient_id.required' => 'Patient is required.',
            'patient_id.exists' => 'Selected patient does not exist.',
            'evolution_type.required' => 'Evolution type is required.',
            'evolution_type.in' => 'Invalid evolution type selected.',
            'description.required' => 'Description is required.',
            'description.max' => 'Description cannot exceed 10000 characters.',
            'evolution_date.date' => 'Please provide a valid evolution date.',
            'evolution_date.before_or_equal' => 'Evolution date cannot be in the future.',
            'next_review_date.after' => 'Next review date must be in the future.',
        ];
    }
}
