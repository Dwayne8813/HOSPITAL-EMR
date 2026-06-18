<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Services\Pharmacy\PharmacyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    public function __construct(
        private readonly PharmacyService $pharmacyService,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $query = PurchaseOrder::where('institution_id', auth()->user()->institution_id)
                ->with(['supplier', 'items.medication']);

            if ($request->has('status')) {
                $query->where('status', $request->input('status'));
            }

            if ($request->has('supplier_id')) {
                $query->where('supplier_id', $request->input('supplier_id'));
            }

            $orders = $query->orderByDesc('created_at')
                ->paginate($request->input('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $orders->items(),
                'message' => 'Purchase orders retrieved successfully.',
                'meta' => [
                    'current_page' => $orders->currentPage(),
                    'last_page' => $orders->lastPage(),
                    'per_page' => $orders->perPage(),
                    'total' => $orders->total(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to retrieve purchase orders.',
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'supplier_id' => ['required', 'integer', 'exists:suppliers,id'],
            'expected_delivery' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.medication_id' => ['required', 'integer', 'exists:medications,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_cost' => ['required', 'numeric', 'min:0'],
        ]);

        try {
            $order = $this->pharmacyService->createPurchaseOrder($request->all());

            return response()->json([
                'success' => true,
                'data' => $order,
                'message' => 'Purchase order created successfully.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to create purchase order.',
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $order = PurchaseOrder::with(['supplier', 'items.medication', 'creator'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $order,
                'message' => 'Purchase order retrieved successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Purchase order not found.',
            ], 404);
        }
    }

    public function receive(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.medication_id' => ['required', 'integer', 'exists:medications,id'],
            'items.*.quantity_received' => ['required', 'integer', 'min:1'],
            'items.*.unit_cost' => ['required', 'numeric', 'min:0'],
            'items.*.batch_number' => ['nullable', 'string', 'max:100'],
            'items.*.expiry_date' => ['required', 'date', 'after:today'],
        ]);

        try {
            $order = PurchaseOrder::findOrFail($id);
            $received = $this->pharmacyService->receivePurchaseOrder($order, $request->input('items'));

            return response()->json([
                'success' => true,
                'data' => $received,
                'message' => 'Purchase order received successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Purchase order not found.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to receive purchase order.',
            ], 500);
        }
    }
}
