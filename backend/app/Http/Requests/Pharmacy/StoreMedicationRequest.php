<?php

declare(strict_types=1);

namespace App\Http\Requests\Pharmacy;

use Illuminate\Foundation\Http\FormRequest;

class StoreMedicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'generic_name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'presentation' => ['nullable', 'string', 'max:255'],
            'manufacturer' => ['nullable', 'string', 'max:255'],
            'active_ingredient' => ['nullable', 'string', 'max:255'],
            'concentration' => ['nullable', 'string', 'max:100'],
            'administration_route' => ['nullable', 'string', 'in:oral,iv,im,sc,topical,inhalation,rectal,ophthalmic,other'],
            'unit_price' => ['nullable', 'numeric', 'min:0'],
            'stock_quantity' => ['nullable', 'integer', 'min:0'],
            'minimum_stock' => ['nullable', 'integer', 'min:0'],
            'unit' => ['nullable', 'string', 'max:50'],
            'requires_prescription' => ['nullable', 'boolean'],
            'is_controlled' => ['nullable', 'boolean'],
            'controlled_substance_level' => ['nullable', 'string', 'in:I,II,III,IV,V'],
            'contraindications' => ['nullable', 'string', 'max:2000'],
            'side_effects' => ['nullable', 'string', 'max:2000'],
            'storage_conditions' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'batch_number' => ['nullable', 'string', 'max:100'],
            'expiry_date' => ['nullable', 'date', 'after:now'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Medication name is required.',
            'name.max' => 'Medication name cannot exceed 255 characters.',
            'generic_name.required' => 'Generic name is required.',
            'generic_name.max' => 'Generic name cannot exceed 255 characters.',
            'unit_price.min' => 'Unit price cannot be negative.',
            'stock_quantity.min' => 'Stock quantity cannot be negative.',
            'minimum_stock.min' => 'Minimum stock cannot be negative.',
            'administration_route.in' => 'Invalid administration route selected.',
            'controlled_substance_level.in' => 'Invalid controlled substance level selected.',
            'expiry_date.after' => 'Expiry date must be in the future.',
        ];
    }
}
