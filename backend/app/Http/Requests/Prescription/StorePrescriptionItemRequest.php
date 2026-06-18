<?php

declare(strict_types=1);

namespace App\Http\Requests\Prescription;

use Illuminate\Foundation\Http\FormRequest;

class StorePrescriptionItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'prescription_id' => ['required', 'integer', 'exists:prescriptions,id'],
            'medication_id' => ['required', 'integer', 'exists:medications,id'],
            'medication_name' => ['required', 'string', 'max:255'],
            'dosage' => ['required', 'string', 'max:100'],
            'frequency' => ['required', 'string', 'max:100'],
            'duration' => ['required', 'string', 'max:100'],
            'route' => ['nullable', 'string', 'in:oral,iv,im,sc,topical,inhalation,rectal,other'],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'refillable' => ['nullable', 'boolean'],
            'refill_count' => ['nullable', 'integer', 'min:0', 'max:12'],
            'instructions' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'prescription_id.required' => 'Prescription is required.',
            'prescription_id.exists' => 'Selected prescription does not exist.',
            'medication_id.required' => 'Medication is required.',
            'medication_id.exists' => 'Selected medication does not exist.',
            'medication_name.required' => 'Medication name is required.',
            'dosage.required' => 'Dosage is required.',
            'dosage.max' => 'Dosage cannot exceed 100 characters.',
            'frequency.required' => 'Frequency is required.',
            'frequency.max' => 'Frequency cannot exceed 100 characters.',
            'duration.required' => 'Duration is required.',
            'duration.max' => 'Duration cannot exceed 100 characters.',
            'route.in' => 'Invalid administration route selected.',
            'quantity.min' => 'Quantity must be at least 1.',
            'refill_count.min' => 'Refill count cannot be negative.',
            'refill_count.max' => 'Refill count cannot exceed 12.',
            'instructions.max' => 'Instructions cannot exceed 500 characters.',
        ];
    }
}
