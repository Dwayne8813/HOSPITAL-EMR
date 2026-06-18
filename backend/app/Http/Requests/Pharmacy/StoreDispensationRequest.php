<?php

declare(strict_types=1);

namespace App\Http\Requests\Pharmacy;

use Illuminate\Foundation\Http\FormRequest;

class StoreDispensationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'prescription_id' => ['required', 'integer', 'exists:prescriptions,id'],
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.medication_id' => ['required', 'integer', 'exists:medications,id'],
            'items.*.quantity_dispensed' => ['required', 'integer', 'min:1'],
            'items.*.batch_number' => ['nullable', 'string', 'max:100'],
            'items.*.expiry_date' => ['nullable', 'date', 'after:now'],
            'items.*.unit_price' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'prescription_id.required' => 'Prescription is required.',
            'prescription_id.exists' => 'Selected prescription does not exist.',
            'patient_id.required' => 'Patient is required.',
            'patient_id.exists' => 'Selected patient does not exist.',
            'items.required' => 'At least one medication item is required.',
            'items.min' => 'At least one medication item is required.',
            'items.*.medication_id.required' => 'Medication is required.',
            'items.*.medication_id.exists' => 'Selected medication does not exist.',
            'items.*.quantity_dispensed.required' => 'Quantity dispensed is required.',
            'items.*.quantity_dispensed.min' => 'Quantity dispensed must be at least 1.',
            'items.*.expiry_date.after' => 'Expiry date must be in the future.',
            'items.*.unit_price.min' => 'Unit price cannot be negative.',
            'notes.max' => 'Notes cannot exceed 2000 characters.',
        ];
    }
}
