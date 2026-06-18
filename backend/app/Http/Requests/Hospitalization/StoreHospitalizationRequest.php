<?php

declare(strict_types=1);

namespace App\Http\Requests\Hospitalization;

use Illuminate\Foundation\Http\FormRequest;

class StoreHospitalizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'doctor_id' => ['required', 'integer', 'exists:users,id'],
            'room_id' => ['nullable', 'integer', 'exists:rooms,id'],
            'bed_id' => ['nullable', 'integer', 'exists:beds,id'],
            'admission_reason' => ['required', 'string', 'max:2000'],
            'diagnosis' => ['nullable', 'string', 'max:2000'],
            'admission_date' => ['nullable', 'date', 'before_or_equal:now'],
            'expected_discharge_date' => ['nullable', 'date', 'after:now'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'priority' => ['nullable', 'string', 'in:normal,urgent,emergency'],
            'insurance_authorization' => ['nullable', 'string', 'max:255'],
            'admission_type' => ['nullable', 'string', 'in:elective,emergency,transfer,newborn'],
        ];
    }

    public function messages(): array
    {
        return [
            'patient_id.required' => 'Patient is required.',
            'patient_id.exists' => 'Selected patient does not exist.',
            'doctor_id.required' => 'Attending doctor is required.',
            'doctor_id.exists' => 'Selected doctor does not exist.',
            'room_id.exists' => 'Selected room does not exist.',
            'bed_id.exists' => 'Selected bed does not exist.',
            'admission_reason.required' => 'Admission reason is required.',
            'admission_reason.max' => 'Admission reason cannot exceed 2000 characters.',
            'diagnosis.max' => 'Diagnosis cannot exceed 2000 characters.',
            'admission_date.date' => 'Please provide a valid admission date.',
            'admission_date.before_or_equal' => 'Admission date cannot be in the future.',
            'expected_discharge_date.after' => 'Expected discharge date must be in the future.',
            'notes.max' => 'Notes cannot exceed 5000 characters.',
            'priority.in' => 'Invalid priority selected.',
            'admission_type.in' => 'Invalid admission type selected.',
        ];
    }
}
