<?php

declare(strict_types=1);

namespace App\Http\Requests\Lab;

use Illuminate\Foundation\Http\FormRequest;

class StoreLabOrderRequest extends FormRequest
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
            'priority' => ['nullable', 'string', 'in:normal,urgent,stat'],
            'clinical_indication' => ['nullable', 'string', 'max:2000'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.test_id' => ['required', 'integer', 'exists:lab_tests,id'],
            'items.*.test_name' => ['required', 'string', 'max:255'],
            'items.*.notes' => ['nullable', 'string', 'max:500'],
            'sample_type' => ['nullable', 'string', 'in:blood,urine,stool,swab,tissue,other'],
            'collection_date' => ['nullable', 'date', 'before_or_equal:now'],
        ];
    }

    public function messages(): array
    {
        return [
            'patient_id.required' => 'Patient is required.',
            'patient_id.exists' => 'Selected patient does not exist.',
            'medical_record_id.required' => 'Medical record is required.',
            'medical_record_id.exists' => 'Selected medical record does not exist.',
            'clinical_indication.max' => 'Clinical indication cannot exceed 2000 characters.',
            'notes.max' => 'Notes cannot exceed 2000 characters.',
            'items.required' => 'At least one lab test is required.',
            'items.min' => 'At least one lab test is required.',
            'items.*.test_id.required' => 'Test selection is required.',
            'items.*.test_id.exists' => 'Selected test does not exist.',
            'items.*.test_name.required' => 'Test name is required.',
            'collection_date.date' => 'Please provide a valid collection date.',
            'collection_date.before_or_equal' => 'Collection date cannot be in the future.',
        ];
    }
}
