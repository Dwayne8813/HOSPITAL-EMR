<?php

declare(strict_types=1);

namespace App\Http\Requests\Clinical;

use Illuminate\Foundation\Http\FormRequest;

class StoreVitalSignRequest extends FormRequest
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
            'temperature' => ['nullable', 'numeric', 'min:30', 'max:45'],
            'blood_pressure_systolic' => ['nullable', 'string', 'max:10'],
            'blood_pressure_diastolic' => ['nullable', 'string', 'max:10'],
            'heart_rate' => ['nullable', 'integer', 'min:20', 'max:300'],
            'respiratory_rate' => ['nullable', 'integer', 'min:5', 'max:80'],
            'oxygen_saturation' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'weight' => ['nullable', 'numeric', 'min:0', 'max:500'],
            'height' => ['nullable', 'numeric', 'min:0', 'max:300'],
            'bmi' => ['nullable', 'numeric', 'min:5', 'max:100'],
            'pain_level' => ['nullable', 'integer', 'min:0', 'max:10'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'recorded_at' => ['nullable', 'date', 'before_or_equal:now'],
        ];
    }

    public function messages(): array
    {
        return [
            'medical_record_id.required' => 'Medical record is required.',
            'medical_record_id.exists' => 'Selected medical record does not exist.',
            'patient_id.required' => 'Patient is required.',
            'patient_id.exists' => 'Selected patient does not exist.',
            'temperature.min' => 'Temperature seems too low.',
            'temperature.max' => 'Temperature seems too high.',
            'heart_rate.min' => 'Heart rate seems too low.',
            'heart_rate.max' => 'Heart rate seems too high.',
            'respiratory_rate.min' => 'Respiratory rate seems too low.',
            'respiratory_rate.max' => 'Respiratory rate seems too high.',
            'oxygen_saturation.min' => 'Oxygen saturation cannot be below 0%.',
            'oxygen_saturation.max' => 'Oxygen saturation cannot exceed 100%.',
            'weight.min' => 'Weight cannot be negative.',
            'height.min' => 'Height cannot be negative.',
            'pain_level.min' => 'Pain level cannot be below 0.',
            'pain_level.max' => 'Pain level cannot exceed 10.',
            'recorded_at.date' => 'Please provide a valid date.',
            'recorded_at.before_or_equal' => 'Recording date cannot be in the future.',
        ];
    }
}
