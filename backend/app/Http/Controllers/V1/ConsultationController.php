<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\Clinical\ClinicalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConsultationController extends Controller
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
            'doctor_id' => ['required', 'integer', 'exists:users,id'],
            'anamnesis' => ['nullable', 'string'],
            'physical_examination' => ['nullable', 'string'],
            'assessment' => ['nullable', 'string'],
            'plan' => ['nullable', 'string'],
            'additional_notes' => ['nullable', 'string'],
            'consultation_date' => ['nullable', 'date'],
        ]);

        try {
            $consultation = $this->clinicalService->createConsultation($request->all());

            return response()->json([
                'success' => true,
                'data' => $consultation,
                'message' => 'Consultation created successfully.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to create consultation.',
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $consultation = $this->clinicalService->getConsultation($id);

            return response()->json([
                'success' => true,
                'data' => $consultation,
                'message' => 'Consultation retrieved successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Consultation not found.',
            ], 404);
        }
    }
}
