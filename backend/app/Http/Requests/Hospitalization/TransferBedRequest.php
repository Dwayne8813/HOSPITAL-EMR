<?php

declare(strict_types=1);

namespace App\Http\Requests\Hospitalization;

use Illuminate\Foundation\Http\FormRequest;

class TransferBedRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'hospitalization_id' => ['required', 'integer', 'exists:hospitalizations,id'],
            'new_room_id' => ['required', 'integer', 'exists:rooms,id'],
            'new_bed_id' => ['required', 'integer', 'exists:beds,id'],
            'reason' => ['required', 'string', 'max:1000'],
            'transfer_date' => ['nullable', 'date', 'before_or_equal:now'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'is_emergency_transfer' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'hospitalization_id.required' => 'Hospitalization is required.',
            'hospitalization_id.exists' => 'Selected hospitalization does not exist.',
            'new_room_id.required' => 'New room is required.',
            'new_room_id.exists' => 'Selected room does not exist.',
            'new_bed_id.required' => 'New bed is required.',
            'new_bed_id.exists' => 'Selected bed does not exist.',
            'reason.required' => 'Transfer reason is required.',
            'reason.max' => 'Transfer reason cannot exceed 1000 characters.',
            'transfer_date.date' => 'Please provide a valid transfer date.',
            'transfer_date.before_or_equal' => 'Transfer date cannot be in the future.',
            'notes.max' => 'Notes cannot exceed 2000 characters.',
        ];
    }
}
