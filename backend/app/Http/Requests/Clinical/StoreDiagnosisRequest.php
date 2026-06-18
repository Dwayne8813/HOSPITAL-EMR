<?php

declare(strict_types=1);

namespace App\Http\Requests\Clinical;

use Illuminate\Foundation\Http\FormRequest;

class StoreDiagnosisRequest extends FormRequest
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
            'diagnoses' => ['required', 'array', 'min:1'],
            'diagnoses.*.code' => ['required', 'string', 'max:20'],
            'diagnoses.*.description' => ['required', 'string', 'max:500'],
            'diagnoses.*.type' => ['required', 'string', 'in:principal,secondary,differential'],
            'diagnoses.*.notes' => ['nullable', 'string', 'max:1000'],
            'diagnoses.*.is_confirmed' => ['nullable', 'boolean'],
            'diagnoses.*.diagnosed_date' => ['nullable', 'date', 'before_or_equal:now'],
        ];
    }

    public function messages(): array
    {
        return [
            'medical_record_id.required' => 'Medical record is required.',
            'medical_record_id.exists' => 'Selected medical record does not exist.',
            'patient_id.required' => 'Patient is required.',
            'patient_id.exists' => 'Selected patient does not exist.',
            'diagnoses.required' => 'At least one diagnosis is required.',
            'diagnoses.min' => 'At least one diagnosis is required.',
            'diagnoses.*.code.required' => 'Diagnosis code is required.',
            'diagnoses.*.description.required' => 'Diagnosis description is required.',
            'diagnoses.*.type.required' => 'Diagnosis type is required.',
            'diagnoses.*.type.in' => 'Invalid diagnosis type selected.',
            'diagnoses.*.diagnosed_date.date' => 'Please provide a valid diagnosed date.',
            'diagnoses.*.diagnosed_date.before_or_equal' => 'Diagnosed date cannot be in the future.',
        ];
    }
}
