<?php

declare(strict_types=1);

namespace App\Http\Requests\Patient;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $patientId = $this->route('patient')?->id ?? $this->route('patient');

        return [
            'first_name' => ['sometimes', 'required', 'string', 'max:255'],
            'last_name' => ['sometimes', 'required', 'string', 'max:255'],
            'document_type' => ['sometimes', 'required', 'string', 'in:cedula,pasaporte,tarjeta_identidad,ruc'],
            'document_number' => [
                'sometimes',
                'required',
                'string',
                'max:20',
                "unique:patients,document_number,{$patientId},id,institution_id," . $this->input('institution_id'),
            ],
            'gender' => ['sometimes', 'required', 'string', 'in:M,F,O'],
            'date_of_birth' => ['sometimes', 'required', 'date', 'before:today', 'after:1900-01-01'],
            'civil_status' => ['sometimes', 'required', 'string', 'in:single,married,divorced,widowed,cohabiting'],
            'blood_type' => ['sometimes', 'required', 'string', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'phone' => ['sometimes', 'required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'zip_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:100'],
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:20'],
            'insurance_provider' => ['nullable', 'string', 'max:255'],
            'insurance_number' => ['nullable', 'string', 'max:100'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'status' => ['sometimes', 'string', 'in:active,inactive,deceased'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'allergies' => ['nullable', 'array'],
            'allergies.*.id' => ['nullable', 'integer'],
            'allergies.*.name' => ['required_with:allergies', 'string', 'max:255'],
            'allergies.*.severity' => ['required_with:allergies', 'string', 'in:mild,moderate,severe'],
            'allergies.*.reaction' => ['nullable', 'string', 'max:500'],
            'medical_history' => ['nullable', 'array'],
            'medical_history.*.id' => ['nullable', 'integer'],
            'medical_history.*.condition' => ['required_with:medical_history', 'string', 'max:255'],
            'medical_history.*.diagnosed_date' => ['nullable', 'date'],
            'medical_history.*.is_chronic' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'document_type.required' => 'Document type is required.',
            'document_type.in' => 'Invalid document type selected.',
            'document_number.required' => 'Document number is required.',
            'document_number.unique' => 'This document number is already registered.',
            'gender.required' => 'Gender is required.',
            'gender.in' => 'Invalid gender selected.',
            'date_of_birth.required' => 'Date of birth is required.',
            'date_of_birth.date' => 'Please provide a valid date.',
            'date_of_birth.before' => 'Date of birth must be in the past.',
            'civil_status.required' => 'Civil status is required.',
            'civil_status.in' => 'Invalid civil status selected.',
            'blood_type.required' => 'Blood type is required.',
            'blood_type.in' => 'Invalid blood type selected.',
            'phone.required' => 'Phone number is required.',
            'email.email' => 'Please provide a valid email address.',
            'status.in' => 'Invalid patient status selected.',
        ];
    }
}
