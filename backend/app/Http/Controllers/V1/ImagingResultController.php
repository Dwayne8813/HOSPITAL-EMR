<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ImagingResult;
use App\Services\Radiology\RadiologyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ImagingResultController extends Controller
{
    public function __construct(
        private readonly RadiologyService $radiologyService,
    ) {
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'imaging_order_id' => ['required', 'integer', 'exists:imaging_orders,id'],
            'findings' => ['required', 'string'],
            'impression' => ['nullable', 'string'],
            'recommendations' => ['nullable', 'string'],
            'performed_by' => ['nullable', 'integer', 'exists:users,id'],
            'file' => ['nullable', 'file', 'max:51200'],
            'dicom_file' => ['nullable', 'file', 'max:102400'],
        ]);

        try {
            $data = $request->except(['file', 'dicom_file']);

            if ($request->hasFile('file')) {
                $data['file'] = $request->file('file');
            }

            if ($request->hasFile('dicom_file')) {
                $data['dicom_file'] = $request->file('dicom_file');
            }

            $result = $this->radiologyService->addResults(
                $request->input('imaging_order_id'),
                $data
            );

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Imaging results added successfully.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to add imaging results.',
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $result = ImagingResult::with(['imagingOrder.patient', 'imagingOrder.doctor'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Imaging result retrieved successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Imaging result not found.',
            ], 404);
        }
    }
}
