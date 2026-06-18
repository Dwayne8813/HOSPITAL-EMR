<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Diagnosis;
use App\Services\Clinical\ClinicalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DiagnosisController extends Controller
{
    public function __construct(
        private readonly ClinicalService $clinicalService,
    ) {
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'medical_record_id' => ['required', 'integer', 'exists:medical_records,id'],
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'cie10_code_id' => ['nullable', 'integer', 'exists:cie10_codes,id'],
            'diagnosis_type' => ['nullable', 'string', 'in:primary,secondary,differential,confirmed'],
            'description' => ['required', 'string', 'max:500'],
            'notes' => ['nullable', 'string'],
            'diagnosed_by' => ['nullable', 'integer', 'exists:users,id'],
            'diagnosis_date' => ['nullable', 'date'],
        ]);

        try {
            $diagnosis = $this->clinicalService->addDiagnosis($request->all());

            return response()->json([
                'success' => true,
                'data' => $diagnosis->load(['cie10Code', 'doctor']),
                'message' => 'Diagnosis added successfully.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to add diagnosis.',
            ], 500);
        }
    }

    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'medical_record_id' => ['required', 'integer', 'exists:medical_records,id'],
        ]);

        try {
            $diagnoses = $this->clinicalService->getDiagnoses($request->input('medical_record_id'));

            return response()->json([
                'success' => true,
                'data' => $diagnoses,
                'message' => 'Diagnoses retrieved successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to retrieve diagnoses.',
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $diagnosis = Diagnosis::findOrFail($id);
            $diagnosis->delete();

            return response()->json([
                'success' => true,
                'data' => null,
                'message' => 'Diagnosis deleted successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Diagnosis not found.',
            ], 404);
        }
    }
}
