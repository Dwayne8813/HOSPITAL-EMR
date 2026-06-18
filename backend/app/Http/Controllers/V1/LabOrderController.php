<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\LabOrder;
use App\Services\Laboratory\LaboratoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LabOrderController extends Controller
{
    public function __construct(
        private readonly LaboratoryService $laboratoryService,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $result = $this->laboratoryService->listLabOrders($request);

            return response()->json([
                'success' => true,
                'data' => $result->items(),
                'message' => 'Lab orders retrieved successfully.',
                'meta' => [
                    'current_page' => $result->currentPage(),
                    'last_page' => $result->lastPage(),
                    'per_page' => $result->perPage(),
                    'total' => $result->total(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to retrieve lab orders.',
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'medical_record_id' => ['required', 'integer', 'exists:medical_records,id'],
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'doctor_id' => ['required', 'integer', 'exists:users,id'],
            'priority' => ['nullable', 'string', 'in:normal,urgent,stat'],
            'clinical_reason' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.test_name' => ['required', 'string', 'max:255'],
            'items.*.test_code' => ['nullable', 'string', 'max:50'],
            'items.*.notes' => ['nullable', 'string'],
        ]);

        try {
            $order = $this->laboratoryService->createLabOrder($request->all());

            return response()->json([
                'success' => true,
                'data' => $order,
                'message' => 'Lab order created successfully.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to create lab order.',
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $order = $this->laboratoryService->getLabOrder($id);

            return response()->json([
                'success' => true,
                'data' => $order,
                'message' => 'Lab order retrieved successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Lab order not found.',
            ], 404);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'status' => ['sometimes', 'string'],
            'priority' => ['sometimes', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        try {
            $order = LabOrder::findOrFail($id);
            $order->update($request->all());

            return response()->json([
                'success' => true,
                'data' => $order->fresh()->load(['items', 'patient', 'doctor']),
                'message' => 'Lab order updated successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Lab order not found.',
            ], 404);
        }
    }
}
