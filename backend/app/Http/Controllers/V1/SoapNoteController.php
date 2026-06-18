<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\SoapNote;
use App\Services\Clinical\ClinicalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SoapNoteController extends Controller
{
    public function __construct(
        private readonly ClinicalService $clinicalService,
    ) {
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'medical_record_id' => ['required', 'integer', 'exists:medical_records,id'],
            'subjective' => ['nullable', 'string'],
            'objective' => ['nullable', 'string'],
            'assessment' => ['nullable', 'string'],
            'plan' => ['nullable', 'string'],
            'doctor_id' => ['nullable', 'integer', 'exists:users,id'],
            'note_date' => ['nullable', 'date'],
        ]);

        try {
            $soapNote = $this->clinicalService->createSoapNote($request->all());

            return response()->json([
                'success' => true,
                'data' => $soapNote,
                'message' => 'SOAP note created successfully.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to create SOAP note.',
            ], 500);
        }
    }

    public function show(int $medicalRecordId): JsonResponse
    {
        try {
            $soapNote = $this->clinicalService->getSoapNote($medicalRecordId);

            return response()->json([
                'success' => true,
                'data' => $soapNote,
                'message' => $soapNote ? 'SOAP note retrieved.' : 'No SOAP note found.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to retrieve SOAP note.',
            ], 500);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'subjective' => ['nullable', 'string'],
            'objective' => ['nullable', 'string'],
            'assessment' => ['nullable', 'string'],
            'plan' => ['nullable', 'string'],
        ]);

        try {
            $soapNote = SoapNote::findOrFail($id);
            $soapNote->update($request->all());

            return response()->json([
                'success' => true,
                'data' => $soapNote->fresh(),
                'message' => 'SOAP note updated successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'SOAP note not found.',
            ], 404);
        }
    }
}
