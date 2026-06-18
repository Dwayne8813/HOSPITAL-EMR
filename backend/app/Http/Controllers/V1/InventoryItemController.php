<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventoryItemController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $query = InventoryItem::where('institution_id', auth()->user()->institution_id)
                ->with(['supplier']);

            if ($request->has('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('code', 'LIKE', "%{$search}%");
                });
            }

            if ($request->has('type')) {
                $query->where('type', $request->input('type'));
            }

            if ($request->has('status')) {
                $query->where('status', $request->input('status'));
            }

            $items = $query->orderBy('name')
                ->paginate($request->input('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $items->items(),
                'message' => 'Inventory items retrieved successfully.',
                'meta' => [
                    'current_page' => $items->currentPage(),
                    'last_page' => $items->lastPage(),
                    'per_page' => $items->perPage(),
                    'total' => $items->total(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to retrieve inventory items.',
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:inventory_items,code'],
            'type' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'category' => ['nullable', 'string', 'max:100'],
            'unit' => ['nullable', 'string', 'max:50'],
            'unit_cost' => ['nullable', 'numeric', 'min:0'],
            'current_stock' => ['nullable', 'integer', 'min:0'],
            'minimum_stock' => ['nullable', 'integer', 'min:0'],
            'maximum_stock' => ['nullable', 'integer', 'min:0'],
            'location' => ['nullable', 'string', 'max:255'],
            'supplier_id' => ['nullable', 'integer', 'exists:suppliers,id'],
        ]);

        try {
            $item = InventoryItem::create([
                'institution_id' => auth()->user()->institution_id,
                ...$request->all(),
                'status' => 'active',
            ]);

            return response()->json([
                'success' => true,
                'data' => $item,
                'message' => 'Inventory item created successfully.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to create inventory item.',
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $item = InventoryItem::with(['supplier', 'movements'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $item,
                'message' => 'Inventory item retrieved successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Inventory item not found.',
            ], 404);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'type' => ['sometimes', 'string'],
            'description' => ['nullable', 'string'],
            'category' => ['nullable', 'string'],
            'unit' => ['nullable', 'string'],
            'unit_cost' => ['sometimes', 'numeric', 'min:0'],
            'current_stock' => ['sometimes', 'integer', 'min:0'],
            'minimum_stock' => ['sometimes', 'integer', 'min:0'],
            'maximum_stock' => ['sometimes', 'integer', 'min:0'],
            'location' => ['nullable', 'string'],
            'supplier_id' => ['nullable', 'integer'],
            'status' => ['sometimes', 'string'],
        ]);

        try {
            $item = InventoryItem::findOrFail($id);
            $item->update($request->all());

            return response()->json([
                'success' => true,
                'data' => $item->fresh(),
                'message' => 'Inventory item updated successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Inventory item not found.',
            ], 404);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $item = InventoryItem::findOrFail($id);
            $item->update(['status' => 'inactive']);

            return response()->json([
                'success' => true,
                'data' => null,
                'message' => 'Inventory item deactivated successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Inventory item not found.',
            ], 404);
        }
    }
}
