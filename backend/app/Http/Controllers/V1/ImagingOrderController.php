<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ImagingOrder;
use App\Services\Radiology\RadiologyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ImagingOrderController extends Controller
{
    public function __construct(
        private readonly RadiologyService $radiologyService,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $result = $this->radiologyService->listImagingOrders($request);

            return response()->json([
                'success' => true,
                'data' => $result->items(),
                'message' => 'Imaging orders retrieved successfully.',
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
                'message' => 'Failed to retrieve imaging orders.',
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'doctor_id' => ['required', 'integer', 'exists:users,id'],
            'medical_record_id' => ['nullable', 'integer', 'exists:medical_records,id'],
            'imaging_type' => ['required', 'string', 'max:100'],
            'body_region' => ['required', 'string', 'max:100'],
            'clinical_indication' => ['nullable', 'string', 'max:500'],
            'priority' => ['nullable', 'string', 'in:normal,urgent,stat'],
            'notes' => ['nullable', 'string'],
        ]);

        try {
            $order = $this->radiologyService->createImagingOrder($request->all());

            return response()->json([
                'success' => true,
                'data' => $order,
                'message' => 'Imaging order created successfully.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to create imaging order.',
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $order = $this->radiologyService->getImagingOrder($id);

            return response()->json([
                'success' => true,
                'data' => $order,
                'message' => 'Imaging order retrieved successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Imaging order not found.',
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
            $order = ImagingOrder::findOrFail($id);
            $order->update($request->all());

            return response()->json([
                'success' => true,
                'data' => $order->fresh()->load(['patient', 'doctor']),
                'message' => 'Imaging order updated successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Imaging order not found.',
            ], 404);
        }
    }
}
