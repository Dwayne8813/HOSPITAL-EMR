<?php

declare(strict_types=1);

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class StoreInventoryItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:inventory_items,code,NULL,id,institution_id,' . $this->input('institution_id')],
            'type' => ['required', 'string', 'in:equipment,supplies,disposable,asset'],
            'description' => ['nullable', 'string', 'max:2000'],
            'category' => ['nullable', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:50'],
            'unit_cost' => ['nullable', 'numeric', 'min:0'],
            'current_stock' => ['nullable', 'integer', 'min:0'],
            'minimum_stock' => ['nullable', 'integer', 'min:0'],
            'maximum_stock' => ['nullable', 'integer', 'min:0'],
            'location' => ['nullable', 'string', 'max:255'],
            'supplier_id' => ['nullable', 'integer', 'exists:suppliers,id'],
            'brand' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'serial_number' => ['nullable', 'string', 'max:255'],
            'purchase_date' => ['nullable', 'date', 'before_or_equal:now'],
            'warranty_expiry' => ['nullable', 'date', 'after:now'],
            'reorder_level' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Item name is required.',
            'name.max' => 'Item name cannot exceed 255 characters.',
            'code.required' => 'Item code is required.',
            'code.unique' => 'This item code already exists.',
            'code.max' => 'Item code cannot exceed 50 characters.',
            'type.required' => 'Item type is required.',
            'type.in' => 'Invalid item type selected.',
            'unit.required' => 'Unit of measurement is required.',
            'unit.max' => 'Unit cannot exceed 50 characters.',
            'unit_cost.min' => 'Unit cost cannot be negative.',
            'current_stock.min' => 'Current stock cannot be negative.',
            'minimum_stock.min' => 'Minimum stock cannot be negative.',
            'maximum_stock.min' => 'Maximum stock cannot be negative.',
            'supplier_id.exists' => 'Selected supplier does not exist.',
            'purchase_date.date' => 'Please provide a valid purchase date.',
            'purchase_date.before_or_equal' => 'Purchase date cannot be in the future.',
            'warranty_expiry.after' => 'Warranty expiry must be in the future.',
            'reorder_level.min' => 'Reorder level cannot be negative.',
            'notes.max' => 'Notes cannot exceed 2000 characters.',
        ];
    }
}
