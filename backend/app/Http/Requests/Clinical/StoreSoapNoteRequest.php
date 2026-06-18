<?php

declare(strict_types=1);

namespace App\Http\Requests\Clinical;

use Illuminate\Foundation\Http\FormRequest;

class StoreSoapNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'medical_record_id' => ['required', 'integer', 'exists:medical_records,id'],
            'subjective' => ['nullable', 'string', 'max:10000'],
            'objective' => ['nullable', 'string', 'max:10000'],
            'assessment' => ['nullable', 'string', 'max:5000'],
            'plan' => ['nullable', 'string', 'max:5000'],
            'note_date' => ['nullable', 'date', 'before_or_equal:now'],
        ];
    }

    public function messages(): array
    {
        return [
            'medical_record_id.required' => 'Medical record is required.',
            'medical_record_id.exists' => 'Selected medical record does not exist.',
            'subjective.max' => 'Subjective notes cannot exceed 10000 characters.',
            'objective.max' => 'Objective notes cannot exceed 10000 characters.',
            'assessment.max' => 'Assessment cannot exceed 5000 characters.',
            'plan.max' => 'Plan cannot exceed 5000 characters.',
            'note_date.date' => 'Please provide a valid note date.',
            'note_date.before_or_equal' => 'Note date cannot be in the future.',
        ];
    }
}
