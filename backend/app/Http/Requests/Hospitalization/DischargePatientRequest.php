<?php

declare(strict_types=1);

namespace App\Http\Requests\Hospitalization;

use Illuminate\Foundation\Http\FormRequest;

class DischargePatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'hospitalization_id' => ['required', 'integer', 'exists:hospitalizations,id'],
            'discharge_date' => ['nullable', 'date', 'before_or_equal:now'],
            'discharge_type' => ['required', 'string', 'in:normal,voluntary,transfer,against_medical_advice,deceased'],
            'discharge_summary' => ['required', 'string', 'max:10000'],
            'final_diagnosis' => ['required', 'string', 'max:2000'],
            'treatment_performed' => ['nullable', 'string', 'max:5000'],
            'condition_at_discharge' => ['required', 'string', 'in:stable,improved,unchanged,worsened,deceased'],
            'discharge_instructions' => ['nullable', 'string', 'max:5000'],
            'follow_up_date' => ['nullable', 'date', 'after:now'],
            'follow_up_instructions' => ['nullable', 'string', 'max:2000'],
            'medications_at_discharge' => ['nullable', 'array'],
            'medications_at_discharge.*.name' => ['required_with:medications_at_discharge', 'string', 'max:255'],
            'medications_at_discharge.*.dosage' => ['required_with:medications_at_discharge', 'string', 'max:100'],
            'medications_at_discharge.*.frequency' => ['required_with:medications_at_discharge', 'string', 'max:100'],
            'medications_at_discharge.*.duration' => ['required_with:medications_at_discharge', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'hospitalization_id.required' => 'Hospitalization is required.',
            'hospitalization_id.exists' => 'Selected hospitalization does not exist.',
            'discharge_date.date' => 'Please provide a valid discharge date.',
            'discharge_date.before_or_equal' => 'Discharge date cannot be in the future.',
            'discharge_type.required' => 'Discharge type is required.',
            'discharge_type.in' => 'Invalid discharge type selected.',
            'discharge_summary.required' => 'Discharge summary is required.',
            'discharge_summary.max' => 'Discharge summary cannot exceed 10000 characters.',
            'final_diagnosis.required' => 'Final diagnosis is required.',
            'final_diagnosis.max' => 'Final diagnosis cannot exceed 2000 characters.',
            'treatment_performed.max' => 'Treatment performed cannot exceed 5000 characters.',
            'condition_at_discharge.required' => 'Patient condition at discharge is required.',
            'condition_at_discharge.in' => 'Invalid discharge condition selected.',
            'discharge_instructions.max' => 'Discharge instructions cannot exceed 5000 characters.',
            'follow_up_date.after' => 'Follow-up date must be in the future.',
            'follow_up_instructions.max' => 'Follow-up instructions cannot exceed 2000 characters.',
            'notes.max' => 'Notes cannot exceed 5000 characters.',
        ];
    }
}
