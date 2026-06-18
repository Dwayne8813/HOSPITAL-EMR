<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\LabOrderItem;
use App\Services\Laboratory\LaboratoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LabResultController extends Controller
{
    public function __construct(
        private readonly LaboratoryService $laboratoryService,
    ) {
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'lab_order_item_id' => ['required', 'integer', 'exists:lab_order_items,id'],
            'result_value' => ['required', 'string'],
            'reference_range' => ['nullable', 'string', 'max:100'],
            'unit' => ['nullable', 'string', 'max:50'],
            'is_abnormal' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
            'file' => ['nullable', 'file', 'max:10240'],
        ]);

        try {
            $data = $request->except('file');
            if ($request->hasFile('file')) {
                $path = $request->file('file')->store('lab-results', 'public');
                $data['file_path'] = $path;
            }

            $result = $this->laboratoryService->addResults(
                $request->input('lab_order_item_id'),
                $data
            );

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Lab results added successfully.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to add lab results.',
            ], 500);
        }
    }

    public function validateResult(Request $request): JsonResponse
    {
        $request->validate([
            'lab_order_item_id' => ['required', 'integer', 'exists:lab_order_items,id'],
        ]);

        try {
            $result = $this->laboratoryService->validateResults(
                $request->input('lab_order_item_id'),
                auth()->id()
            );

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Lab results validated successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to validate results.',
            ], 500);
        }
    }

    public function index(Request $request, int $patientId): JsonResponse
    {
        try {
            $results = $this->laboratoryService->getResults($patientId);

            return response()->json([
                'success' => true,
                'data' => $results,
                'message' => 'Lab results retrieved successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to retrieve results.',
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $item = LabOrderItem::with([
                'results',
                'labOrder.patient',
                'labOrder.doctor',
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $item,
                'message' => 'Lab result retrieved successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Lab result not found.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to retrieve lab result.',
            ], 500);
        }
    }
}
