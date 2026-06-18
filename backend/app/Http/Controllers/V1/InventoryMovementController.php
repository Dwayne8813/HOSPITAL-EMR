<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\InventoryMovement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventoryMovementController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $query = InventoryMovement::where('institution_id', auth()->user()->institution_id)
                ->with(['inventoryItem', 'performedBy']);

            if ($request->has('inventory_item_id')) {
                $query->where('inventory_item_id', $request->input('inventory_item_id'));
            }

            if ($request->has('type')) {
                $query->where('movement_type', $request->input('type'));
            }

            if ($request->has('date_from')) {
                $query->where('movement_date', '>=', $request->input('date_from'));
            }

            if ($request->has('date_to')) {
                $query->where('movement_date', '<=', $request->input('date_to'));
            }

            $movements = $query->orderByDesc('movement_date')
                ->paginate($request->input('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $movements->items(),
                'message' => 'Inventory movements retrieved successfully.',
                'meta' => [
                    'current_page' => $movements->currentPage(),
                    'last_page' => $movements->lastPage(),
                    'per_page' => $movements->perPage(),
                    'total' => $movements->total(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to retrieve inventory movements.',
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'inventory_item_id' => ['required', 'integer', 'exists:inventory_items,id'],
            'movement_type' => ['required', 'string', 'in:entry,exit,transfer,adjustment'],
            'quantity' => ['required', 'integer'],
            'unit_cost' => ['nullable', 'numeric', 'min:0'],
            'reference' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);

        try {
            $item = \App\Models\InventoryItem::findOrFail($request->input('inventory_item_id'));

            $quantity = $request->input('quantity');
            if ($request->input('movement_type') === 'exit') {
                $quantity = -abs($quantity);
            }

            $movement = InventoryMovement::create([
                'institution_id' => auth()->user()->institution_id,
                'inventory_item_id' => $item->id,
                'movement_type' => $request->input('movement_type'),
                'quantity' => $quantity,
                'unit_cost' => $request->input('unit_cost', $item->unit_cost),
                'total_cost' => abs($quantity) * ($request->input('unit_cost', $item->unit_cost) ?? 0),
                'reference' => $request->input('reference'),
                'notes' => $request->input('notes'),
                'performed_by' => auth()->id(),
                'movement_date' => now(),
            ]);

            if ($request->input('movement_type') === 'entry') {
                $item->increment('current_stock', abs($quantity));
            } elseif ($request->input('movement_type') === 'exit') {
                $item->decrement('current_stock', abs($quantity));
            }

            return response()->json([
                'success' => true,
                'data' => $movement->load(['inventoryItem', 'performedBy']),
                'message' => 'Inventory movement recorded successfully.',
            ], 201);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Inventory item not found.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to record inventory movement.',
            ], 500);
        }
    }
}
