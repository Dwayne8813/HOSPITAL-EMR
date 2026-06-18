<?php

declare(strict_types=1);

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class StoreInventoryMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'inventory_item_id' => ['required', 'integer', 'exists:inventory_items,id'],
            'movement_type' => ['required', 'string', 'in:entry,exit,transfer,adjustment'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit_cost' => ['nullable', 'numeric', 'min:0'],
            'total_cost' => ['nullable', 'numeric', 'min:0'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'from_location' => ['required_if:movement_type,transfer', 'nullable', 'string', 'max:255'],
            'to_location' => ['required_if:movement_type,transfer,entry', 'nullable', 'string', 'max:255'],
            'destination_institution_id' => ['required_if:movement_type,transfer', 'nullable', 'integer', 'exists:institutions,id'],
            'supplier_id' => ['required_if:movement_type,entry', 'nullable', 'integer', 'exists:suppliers,id'],
            'batch_number' => ['nullable', 'string', 'max:100'],
            'expiry_date' => ['nullable', 'date', 'after:now'],
            'reason' => ['nullable', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'movement_date' => ['nullable', 'date', 'before_or_equal:now'],
        ];
    }

    public function messages(): array
    {
        return [
            'inventory_item_id.required' => 'Inventory item is required.',
            'inventory_item_id.exists' => 'Selected inventory item does not exist.',
            'movement_type.required' => 'Movement type is required.',
            'movement_type.in' => 'Invalid movement type selected.',
            'quantity.required' => 'Quantity is required.',
            'quantity.min' => 'Quantity must be at least 1.',
            'unit_cost.min' => 'Unit cost cannot be negative.',
            'total_cost.min' => 'Total cost cannot be negative.',
            'reference_number.max' => 'Reference number cannot exceed 100 characters.',
            'from_location.required_if' => 'Source location is required for transfers.',
            'to_location.required_if' => 'Destination location is required for entries and transfers.',
            'destination_institution_id.required_if' => 'Destination institution is required for transfers.',
            'destination_institution_id.exists' => 'Selected destination institution does not exist.',
            'supplier_id.required_if' => 'Supplier is required for stock entries.',
            'supplier_id.exists' => 'Selected supplier does not exist.',
            'expiry_date.after' => 'Expiry date must be in the future.',
            'reason.max' => 'Reason cannot exceed 1000 characters.',
            'notes.max' => 'Notes cannot exceed 2000 characters.',
            'movement_date.date' => 'Please provide a valid movement date.',
            'movement_date.before_or_equal' => 'Movement date cannot be in the future.',
        ];
    }
}
