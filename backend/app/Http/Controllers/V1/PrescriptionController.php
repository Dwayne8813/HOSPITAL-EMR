<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Prescription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PrescriptionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Prescription::where('institution_id', auth()->user()->institution_id)
                ->with(['patient', 'doctor', 'items.medication']);

            if ($request->has('patient_id')) {
                $query->where('patient_id', $request->input('patient_id'));
            }

            if ($request->has('doctor_id')) {
                $query->where('doctor_id', $request->input('doctor_id'));
            }

            if ($request->has('status')) {
                $query->where('status', $request->input('status'));
            }

            $prescriptions = $query->orderByDesc('created_at')
                ->paginate($request->input('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $prescriptions->items(),
                'message' => 'Prescriptions retrieved successfully.',
                'meta' => [
                    'current_page' => $prescriptions->currentPage(),
                    'last_page' => $prescriptions->lastPage(),
                    'per_page' => $prescriptions->perPage(),
                    'total' => $prescriptions->total(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to retrieve prescriptions.',
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'doctor_id' => ['required', 'integer', 'exists:users,id'],
            'medical_record_id' => ['required', 'integer', 'exists:medical_records,id'],
            'status' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'prescription_date' => ['nullable', 'date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.medication_id' => ['required', 'integer', 'exists:medications,id'],
            'items.*.medication_name' => ['required', 'string', 'max:255'],
            'items.*.dosage' => ['required', 'string', 'max:100'],
            'items.*.frequency' => ['required', 'string', 'max:100'],
            'items.*.duration' => ['required', 'string', 'max:100'],
            'items.*.route' => ['nullable', 'string', 'max:100'],
            'items.*.quantity' => ['nullable', 'integer', 'min:1'],
            'items.*.refillable' => ['nullable', 'boolean'],
            'items.*.refill_count' => ['nullable', 'integer', 'min:0'],
            'items.*.instructions' => ['nullable', 'string'],
        ]);

        try {
            $prescription = Prescription::create([
                'institution_id' => auth()->user()->institution_id,
                'patient_id' => $request->input('patient_id'),
                'doctor_id' => $request->input('doctor_id'),
                'medical_record_id' => $request->input('medical_record_id'),
                'status' => $request->input('status', 'draft'),
                'notes' => $request->input('notes'),
                'prescription_date' => $request->input('prescription_date', now()),
            ]);

            foreach ($request->input('items') as $item) {
                $prescription->items()->create($item);
            }

            return response()->json([
                'success' => true,
                'data' => $prescription->load(['patient', 'doctor', 'items.medication']),
                'message' => 'Prescription created successfully.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to create prescription.',
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $prescription = Prescription::with([
                'patient',
                'doctor',
                'items.medication',
                'medicalRecord',
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $prescription,
                'message' => 'Prescription retrieved successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Prescription not found.',
            ], 404);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'status' => ['sometimes', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        try {
            $prescription = Prescription::findOrFail($id);
            $prescription->update($request->only(['status', 'notes']));

            return response()->json([
                'success' => true,
                'data' => $prescription->fresh()->load(['patient', 'doctor', 'items']),
                'message' => 'Prescription updated successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Prescription not found.',
            ], 404);
        }
    }

    public function pdf(int $id): JsonResponse
    {
        try {
            $prescription = Prescription::with([
                'patient',
                'doctor',
                'items.medication',
            ])->findOrFail($id);

            $pdf = \Barryvdh\DomPDF\Facades\Pdf::loadView('prescriptions.pdf', [
                'prescription' => $prescription,
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'pdf_url' => 'data:application/pdf;base64,' . base64_encode($pdf->output()),
                ],
                'message' => 'Prescription PDF generated.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Prescription not found.',
            ], 404);
        }
    }
}
