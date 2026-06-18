<?php

declare(strict_types=1);

namespace App\Http\Requests\Clinical;

use Illuminate\Foundation\Http\FormRequest;

class StoreMedicalRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'type' => ['required', 'string', 'in:consultation,emergency,hospitalization,telemedicine'],
            'chief_complaint' => ['nullable', 'string', 'max:2000'],
            'notes' => ['nullable', 'string', 'max:10000'],
            'observations' => ['nullable', 'string', 'max:5000'],
            'record_date' => ['nullable', 'date', 'before_or_equal:now'],
            'appointment_id' => ['nullable', 'integer', 'exists:appointments,id'],
            'diagnoses' => ['nullable', 'array'],
            'diagnoses.*.code' => ['required_with:diagnoses', 'string', 'max:20'],
            'diagnoses.*.description' => ['required_with:diagnoses', 'string', 'max:500'],
            'diagnoses.*.type' => ['required_with:diagnoses', 'string', 'in:principal,secondary,differential'],
            'consultation' => ['nullable', 'array'],
            'consultation.anamnesis' => ['nullable', 'string', 'max:10000'],
            'consultation.physical_examination' => ['nullable', 'string', 'max:10000'],
            'consultation.assessment' => ['nullable', 'string', 'max:5000'],
            'consultation.plan' => ['nullable', 'string', 'max:5000'],
            'soap_note' => ['nullable', 'array'],
            'soap_note.subjective' => ['nullable', 'string', 'max:10000'],
            'soap_note.objective' => ['nullable', 'string', 'max:10000'],
            'soap_note.assessment' => ['nullable', 'string', 'max:5000'],
            'soap_note.plan' => ['nullable', 'string', 'max:5000'],
            'vital_signs' => ['nullable', 'array'],
            'vital_signs.temperature' => ['nullable', 'numeric', 'min:30', 'max:45'],
            'vital_signs.blood_pressure_systolic' => ['nullable', 'string', 'max:10'],
            'vital_signs.blood_pressure_diastolic' => ['nullable', 'string', 'max:10'],
            'vital_signs.heart_rate' => ['nullable', 'integer', 'min:20', 'max:300'],
            'vital_signs.respiratory_rate' => ['nullable', 'integer', 'min:5', 'max:80'],
            'vital_signs.oxygen_saturation' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'vital_signs.weight' => ['nullable', 'numeric', 'min:0', 'max:500'],
            'vital_signs.height' => ['nullable', 'numeric', 'min:0', 'max:300'],
        ];
    }

    public function messages(): array
    {
        return [
            'patient_id.required' => 'Patient is required.',
            'patient_id.exists' => 'Selected patient does not exist.',
            'type.required' => 'Record type is required.',
            'type.in' => 'Invalid record type selected.',
            'chief_complaint.max' => 'Chief complaint cannot exceed 2000 characters.',
            'notes.max' => 'Notes cannot exceed 10000 characters.',
            'record_date.date' => 'Please provide a valid date.',
            'record_date.before_or_equal' => 'Record date cannot be in the future.',
            'appointment_id.exists' => 'Selected appointment does not exist.',
            'diagnoses.*.code.required' => 'Diagnosis code is required.',
            'diagnoses.*.description.required' => 'Diagnosis description is required.',
            'diagnoses.*.type.required' => 'Diagnosis type is required.',
            'diagnoses.*.type.in' => 'Invalid diagnosis type selected.',
            'vital_signs.temperature.min' => 'Temperature seems too low.',
            'vital_signs.temperature.max' => 'Temperature seems too high.',
            'vital_signs.heart_rate.min' => 'Heart rate seems too low.',
            'vital_signs.heart_rate.max' => 'Heart rate seems too high.',
        ];
    }
}
