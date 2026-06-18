<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\Clinical\ClinicalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VitalSignController extends Controller
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
            'temperature' => ['nullable', 'numeric', 'min:30', 'max:45'],
            'blood_pressure_systolic' => ['nullable', 'string', 'max:10'],
            'blood_pressure_diastolic' => ['nullable', 'string', 'max:10'],
            'heart_rate' => ['nullable', 'integer', 'min:20', 'max:300'],
            'respiratory_rate' => ['nullable', 'integer', 'min:5', 'max:60'],
            'oxygen_saturation' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'height' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'recorded_at' => ['nullable', 'date'],
        ]);

        try {
            $vitalSign = $this->clinicalService->addVitalSign($request->all());

            return response()->json([
                'success' => true,
                'data' => $vitalSign,
                'message' => 'Vital signs recorded successfully.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to record vital signs.',
            ], 500);
        }
    }

    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:200'],
        ]);

        try {
            $vitalSigns = $this->clinicalService->getVitalSigns(
                $request->input('patient_id'),
                $request->input('limit', 50)
            );

            return response()->json([
                'success' => true,
                'data' => $vitalSigns,
                'message' => 'Vital signs retrieved successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to retrieve vital signs.',
            ], 500);
        }
    }
}
