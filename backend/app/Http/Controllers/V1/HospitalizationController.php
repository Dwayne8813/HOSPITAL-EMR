<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Hospitalization;
use App\Services\Hospitalization\HospitalizationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HospitalizationController extends Controller
{
    public function __construct(
        private readonly HospitalizationService $hospitalizationService,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $query = Hospitalization::where('institution_id', auth()->user()->institution_id)
                ->with(['patient', 'doctor', 'room', 'bed']);

            if ($request->has('patient_id')) {
                $query->where('patient_id', $request->input('patient_id'));
            }

            if ($request->has('doctor_id')) {
                $query->where('doctor_id', $request->input('doctor_id'));
            }

            if ($request->has('status')) {
                $query->where('status', $request->input('status'));
            }

            $hospitalizations = $query->orderByDesc('admission_date')
                ->paginate($request->input('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $hospitalizations->items(),
                'message' => 'Hospitalizations retrieved successfully.',
                'meta' => [
                    'current_page' => $hospitalizations->currentPage(),
                    'last_page' => $hospitalizations->lastPage(),
                    'per_page' => $hospitalizations->perPage(),
                    'total' => $hospitalizations->total(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to retrieve hospitalizations.',
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'doctor_id' => ['required', 'integer', 'exists:users,id'],
            'room_id' => ['nullable', 'integer', 'exists:rooms,id'],
            'bed_id' => ['nullable', 'integer', 'exists:beds,id'],
            'admission_reason' => ['required', 'string', 'max:500'],
            'diagnosis' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'admission_date' => ['nullable', 'date'],
        ]);

        try {
            $hospitalization = $this->hospitalizationService->admit($request->all());

            return response()->json([
                'success' => true,
                'data' => $hospitalization->load(['patient', 'doctor', 'room', 'bed']),
                'message' => 'Patient admitted successfully.',
            ], 201);
        } catch (\App\Exceptions\BusinessLogicException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to admit patient.',
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $hospitalization = Hospitalization::with([
                'patient',
                'doctor',
                'room.building',
                'bed',
                'evolutions.doctor',
                'transfers',
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $hospitalization,
                'message' => 'Hospitalization retrieved successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Hospitalization not found.',
            ], 404);
        }
    }

    public function admit(Request $request): JsonResponse
    {
        return $this->store($request);
    }

    public function discharge(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'discharge_date' => ['nullable', 'date'],
            'discharge_summary' => ['nullable', 'string'],
            'final_diagnosis' => ['nullable', 'string'],
        ]);

        try {
            $hospitalization = Hospitalization::findOrFail($id);
            $discharged = $this->hospitalizationService->discharge($hospitalization, $request->all());

            return response()->json([
                'success' => true,
                'data' => $discharged,
                'message' => 'Patient discharged successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Hospitalization not found.',
            ], 404);
        }
    }

    public function transfer(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'to_bed_id' => ['required', 'integer', 'exists:beds,id'],
            'reason' => ['required', 'string', 'max:500'],
        ]);

        try {
            $hospitalization = Hospitalization::findOrFail($id);
            $transferred = $this->hospitalizationService->transfer(
                $hospitalization,
                $request->input('to_bed_id'),
                $request->input('reason')
            );

            return response()->json([
                'success' => true,
                'data' => $transferred,
                'message' => 'Patient transferred successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Hospitalization not found.',
            ], 404);
        } catch (\App\Exceptions\BusinessLogicException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function evolutions(int $id): JsonResponse
    {
        try {
            $evolutions = $this->hospitalizationService->getEvolutions($id);

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

    public function transfers(int $id): JsonResponse
    {
        try {
            $transfers = $this->hospitalizationService->getTransfers($id);

            return response()->json([
                'success' => true,
                'data' => $transfers,
                'message' => 'Transfers retrieved successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to retrieve transfers.',
            ], 500);
        }
    }
}
