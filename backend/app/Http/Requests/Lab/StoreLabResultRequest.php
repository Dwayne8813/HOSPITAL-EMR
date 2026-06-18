<?php

declare(strict_types=1);

namespace App\Http\Requests\Lab;

use Illuminate\Foundation\Http\FormRequest;

class StoreLabResultRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lab_order_id' => ['required', 'integer', 'exists:lab_orders,id'],
            'results' => ['required', 'array', 'min:1'],
            'results.*.test_name' => ['required', 'string', 'max:255'],
            'results.*.result_value' => ['required', 'string', 'max:500'],
            'results.*.unit' => ['nullable', 'string', 'max:50'],
            'results.*.reference_range' => ['nullable', 'string', 'max:100'],
            'results.*.is_abnormal' => ['nullable', 'boolean'],
            'results.*.notes' => ['nullable', 'string', 'max:500'],
            'general_notes' => ['nullable', 'string', 'max:5000'],
            'technical_notes' => ['nullable', 'string', 'max:2000'],
            'result_date' => ['nullable', 'date', 'before_or_equal:now'],
            'verified_by' => ['nullable', 'integer', 'exists:users,id'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'lab_order_id.required' => 'Lab order is required.',
            'lab_order_id.exists' => 'Selected lab order does not exist.',
            'results.required' => 'At least one result is required.',
            'results.min' => 'At least one result is required.',
            'results.*.test_name.required' => 'Test name is required.',
            'results.*.result_value.required' => 'Result value is required.',
            'general_notes.max' => 'General notes cannot exceed 5000 characters.',
            'technical_notes.max' => 'Technical notes cannot exceed 2000 characters.',
            'result_date.date' => 'Please provide a valid result date.',
            'result_date.before_or_equal' => 'Result date cannot be in the future.',
            'verified_by.exists' => 'Selected verifier does not exist.',
            'attachments.*.file' => 'Each attachment must be a valid file.',
            'attachments.*.max' => 'Each attachment cannot exceed 10MB.',
        ];
    }
}
