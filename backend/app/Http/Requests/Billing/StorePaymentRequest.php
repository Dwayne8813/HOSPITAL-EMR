<?php

declare(strict_types=1);

namespace App\Http\Requests\Billing;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'invoice_id' => ['required', 'integer', 'exists:invoices,id'],
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'method' => ['required', 'string', 'in:cash,card,transfer,insurance,mixed'],
            'reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'payment_date' => ['nullable', 'date', 'before_or_equal:now'],
            'card_last_four' => ['required_if:method,card', 'nullable', 'string', 'size:4'],
            'card_type' => ['required_if:method,card', 'nullable', 'string', 'in:visa,mastercard,amex,discover,other'],
            'bank_name' => ['required_if:method,transfer', 'nullable', 'string', 'max:255'],
            'transfer_reference' => ['required_if:method,transfer', 'nullable', 'string', 'max:255'],
            'insurance_name' => ['required_if:method,insurance', 'nullable', 'string', 'max:255'],
            'insurance_claim_number' => ['required_if:method,insurance', 'nullable', 'string', 'max:255'],
            'mixed_payments' => ['required_if:method,mixed', 'nullable', 'array', 'min:1'],
            'mixed_payments.*.method' => ['required_with:mixed_payments', 'string', 'in:cash,card,transfer,insurance'],
            'mixed_payments.*.amount' => ['required_with:mixed_payments', 'numeric', 'min:0.01'],
            'receipt_number' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'invoice_id.required' => 'Invoice is required.',
            'invoice_id.exists' => 'Selected invoice does not exist.',
            'patient_id.required' => 'Patient is required.',
            'patient_id.exists' => 'Selected patient does not exist.',
            'amount.required' => 'Payment amount is required.',
            'amount.min' => 'Payment amount must be greater than 0.',
            'method.required' => 'Payment method is required.',
            'method.in' => 'Invalid payment method selected.',
            'card_last_four.required_if' => 'Last 4 digits of card are required for card payments.',
            'card_last_four.size' => 'Card last four must be exactly 4 digits.',
            'bank_name.required_if' => 'Bank name is required for transfer payments.',
            'transfer_reference.required_if' => 'Transfer reference is required for transfer payments.',
            'insurance_name.required_if' => 'Insurance name is required for insurance payments.',
            'insurance_claim_number.required_if' => 'Insurance claim number is required for insurance payments.',
            'mixed_payments.required_if' => 'Mixed payment details are required for mixed payments.',
            'mixed_payments.min' => 'At least one mixed payment entry is required.',
            'mixed_payments.*.method.required' => 'Payment method is required for each mixed payment.',
            'mixed_payments.*.amount.required' => 'Amount is required for each mixed payment.',
            'mixed_payments.*.amount.min' => 'Each mixed payment amount must be greater than 0.',
            'payment_date.date' => 'Please provide a valid payment date.',
            'payment_date.before_or_equal' => 'Payment date cannot be in the future.',
        ];
    }
}
