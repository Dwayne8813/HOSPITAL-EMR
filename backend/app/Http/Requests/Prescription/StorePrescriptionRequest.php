<?php

declare(strict_types=1);

namespace App\Http\Requests\Prescription;

use Illuminate\Foundation\Http\FormRequest;

class StorePrescriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'medical_record_id' => ['required', 'integer', 'exists:medical_records,id'],
            'prescription_date' => ['nullable', 'date', 'before_or_equal:now'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'valid_until' => ['nullable', 'date', 'after:now'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.medication_id' => ['required', 'integer', 'exists:medications,id'],
            'items.*.medication_name' => ['required', 'string', 'max:255'],
            'items.*.dosage' => ['required', 'string', 'max:100'],
            'items.*.frequency' => ['required', 'string', 'max:100'],
            'items.*.duration' => ['required', 'string', 'max:100'],
            'items.*.route' => ['nullable', 'string', 'in:oral,iv,im,sc,topical,inhalation,rectal,other'],
            'items.*.quantity' => ['nullable', 'integer', 'min:1'],
            'items.*.refillable' => ['nullable', 'boolean'],
            'items.*.refill_count' => ['nullable', 'integer', 'min:0', 'max:12'],
            'items.*.instructions' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'patient_id.required' => 'Patient is required.',
            'patient_id.exists' => 'Selected patient does not exist.',
            'medical_record_id.required' => 'Medical record is required.',
            'medical_record_id.exists' => 'Selected medical record does not exist.',
            'prescription_date.date' => 'Please provide a valid prescription date.',
            'prescription_date.before_or_equal' => 'Prescription date cannot be in the future.',
            'valid_until.after' => 'Valid until date must be in the future.',
            'items.required' => 'At least one medication item is required.',
            'items.min' => 'At least one medication item is required.',
            'items.*.medication_id.required' => 'Medication is required.',
            'items.*.medication_id.exists' => 'Selected medication does not exist.',
            'items.*.medication_name.required' => 'Medication name is required.',
            'items.*.dosage.required' => 'Dosage is required.',
            'items.*.frequency.required' => 'Frequency is required.',
            'items.*.duration.required' => 'Duration is required.',
        ];
    }
}
