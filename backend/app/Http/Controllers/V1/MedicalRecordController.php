<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\MedicalRecord;
use App\Services\Clinical\ClinicalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    public function __construct(
        private readonly ClinicalService $clinicalService,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $result = $this->clinicalService->listMedicalRecords($request);

            return response()->json([
                'success' => true,
                'data' => $result->items(),
                'message' => 'Medical records retrieved successfully.',
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
                'message' => 'Failed to retrieve medical records.',
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'doctor_id' => ['required', 'integer', 'exists:users,id'],
            'type' => ['nullable', 'string', 'in:consultation,emergency,hospitalization'],
            'status' => ['nullable', 'string', 'in:draft,active,closed'],
            'chief_complaint' => ['nullable', 'string', 'max:1000'],
            'history_of_present_illness' => ['nullable', 'string'],
            'physical_examination' => ['nullable', 'string'],
            'assessment' => ['nullable', 'string'],
            'plan' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'visit_date' => ['nullable', 'date'],
            'antecedents' => ['nullable', 'array'],
            'antecedents.personal_pathological' => ['nullable', 'string'],
            'antecedents.personal_non_pathological' => ['nullable', 'string'],
            'antecedents.family' => ['nullable', 'string'],
            'antecedents.gynecological' => ['nullable', 'string'],
            'antecedents.surgical' => ['nullable', 'string'],
            'antecedents.allergies' => ['nullable', 'string'],
            'antecedents.medications' => ['nullable', 'string'],
        ]);

        try {
            $record = $this->clinicalService->createMedicalRecord($request->all());

            return response()->json([
                'success' => true,
                'data' => $record,
                'message' => 'Medical record created successfully.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to create medical record.',
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $record = $this->clinicalService->getMedicalRecord($id);

            return response()->json([
                'success' => true,
                'data' => $record,
                'message' => 'Medical record retrieved successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Medical record not found.',
            ], 404);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'type' => ['sometimes', 'string'],
            'status' => ['sometimes', 'string'],
            'chief_complaint' => ['nullable', 'string'],
            'history_of_present_illness' => ['nullable', 'string'],
            'physical_examination' => ['nullable', 'string'],
            'assessment' => ['nullable', 'string'],
            'plan' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'visit_date' => ['nullable', 'date'],
        ]);

        try {
            $record = MedicalRecord::findOrFail($id);
            $updated = $this->clinicalService->updateMedicalRecord($record, $request->all());

            return response()->json([
                'success' => true,
                'data' => $updated,
                'message' => 'Medical record updated successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Medical record not found.',
            ], 404);
        }
    }

    public function getAntecedents(int $id): JsonResponse
    {
        try {
            $antecedents = $this->clinicalService->getAntecedents($id);

            return response()->json([
                'success' => true,
                'data' => $antecedents,
                'message' => 'Antecedents retrieved successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to retrieve antecedents.',
            ], 500);
        }
    }

    public function updateAntecedents(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'personal_pathological' => ['nullable', 'string'],
            'personal_non_pathological' => ['nullable', 'string'],
            'family' => ['nullable', 'string'],
            'gynecological' => ['nullable', 'string'],
            'surgical' => ['nullable', 'string'],
            'allergies' => ['nullable', 'string'],
            'medications' => ['nullable', 'string'],
        ]);

        try {
            $antecedents = $this->clinicalService->updateAntecedents($id, $request->all());

            return response()->json([
                'success' => true,
                'data' => $antecedents,
                'message' => 'Antecedents updated successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to update antecedents.',
            ], 500);
        }
    }
}
