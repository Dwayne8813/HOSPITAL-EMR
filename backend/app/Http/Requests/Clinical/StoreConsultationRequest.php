<?php

declare(strict_types=1);

namespace App\Http\Requests\Clinical;

use Illuminate\Foundation\Http\FormRequest;

class StoreConsultationRequest extends FormRequest
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
            'anamnesis' => ['nullable', 'string', 'max:10000'],
            'physical_examination' => ['nullable', 'string', 'max:10000'],
            'assessment' => ['nullable', 'string', 'max:5000'],
            'plan' => ['nullable', 'string', 'max:5000'],
            'additional_notes' => ['nullable', 'string', 'max:5000'],
            'consultation_date' => ['nullable', 'date', 'before_or_equal:now'],
            'follow_up_date' => ['nullable', 'date', 'after:now'],
            'follow_up_instructions' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'medical_record_id.required' => 'Medical record is required.',
            'medical_record_id.exists' => 'Selected medical record does not exist.',
            'patient_id.required' => 'Patient is required.',
            'patient_id.exists' => 'Selected patient does not exist.',
            'anamnesis.max' => 'Anamnesis cannot exceed 10000 characters.',
            'physical_examination.max' => 'Physical examination cannot exceed 10000 characters.',
            'assessment.max' => 'Assessment cannot exceed 5000 characters.',
            'plan.max' => 'Plan cannot exceed 5000 characters.',
            'consultation_date.date' => 'Please provide a valid consultation date.',
            'consultation_date.before_or_equal' => 'Consultation date cannot be in the future.',
            'follow_up_date.after' => 'Follow-up date must be in the future.',
        ];
    }
}
