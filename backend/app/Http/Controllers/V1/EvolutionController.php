<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\Clinical\ClinicalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EvolutionController extends Controller
{
    public function __construct(
        private readonly ClinicalService $clinicalService,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'medical_record_id' => ['required', 'integer', 'exists:medical_records,id'],
        ]);

        try {
            $evolutions = $this->clinicalService->getEvolutions($request->input('medical_record_id'));

            return response()->json([
                'success' => true,
                'data' => $evolutions,
                'message' => 'Evolutions retrieved successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to retrieve evolutions.',
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'medical_record_id' => ['required', 'integer', 'exists:medical_records,id'],
            'doctor_id' => ['nullable', 'integer', 'exists:users,id'],
            'evolution_type' => ['nullable', 'string', 'in:clinical,nursing,surgical'],
            'subjective' => ['nullable', 'string'],
            'objective' => ['nullable', 'string'],
            'assessment' => ['nullable', 'string'],
            'plan' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'evolution_date' => ['nullable', 'date'],
        ]);

        try {
            $evolution = $this->clinicalService->createEvolution($request->all());

            return response()->json([
                'success' => true,
                'data' => $evolution,
                'message' => 'Evolution entry created successfully.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to create evolution entry.',
            ], 500);
        }
    }
}
