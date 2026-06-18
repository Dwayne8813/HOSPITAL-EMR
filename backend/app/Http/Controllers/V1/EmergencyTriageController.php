<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\EmergencyTriage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmergencyTriageController extends Controller
{

    public function index(Request $request): JsonResponse
    {
        try {
            $query = EmergencyTriage::where('institution_id', auth()->user()->institution_id)
                ->with(['patient', 'doctor']);

            if ($request->has('status')) {
                $query->where('status', $request->input('status'));
            }

            if ($request->has('triage_level')) {
                $query->where('triage_level', $request->input('triage_level'));
            }

            if ($request->has('date')) {
                $query->whereDate('arrival_date', $request->input('date'));
            }

            $triages = $query->orderByDesc('arrival_date')
                ->paginate($request->input('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $triages->items(),
                'message' => 'Emergency triages retrieved successfully.',
                'meta' => [
                    'current_page' => $triages->currentPage(),
                    'last_page' => $triages->lastPage(),
                    'per_page' => $triages->perPage(),
                    'total' => $triages->total(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to retrieve emergency triages.',
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'doctor_id' => ['nullable', 'integer', 'exists:users,id'],
            'triage_level' => ['required', 'string', 'in:I,II,III,IV,V'],
            'triage_score' => ['required', 'integer', 'min:0', 'max:100'],
            'chief_complaint' => ['required', 'string', 'max:500'],
            'arrival_date' => ['nullable', 'date'],
        ]);

        try {
            $triage = EmergencyTriage::create([
                'institution_id' => auth()->user()->institution_id,
                'patient_id' => $request->input('patient_id'),
                'doctor_id' => $request->input('doctor_id'),
                'triage_level' => $request->input('triage_level'),
                'triage_score' => $request->input('triage_score'),
                'chief_complaint' => $request->input('chief_complaint'),
                'arrival_date' => $request->input('arrival_date', now()),
                'status' => 'waiting',
            ]);

            return response()->json([
                'success' => true,
                'data' => $triage->load(['patient', 'doctor']),
                'message' => 'Triage entry created successfully.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to create triage entry.',
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $triage = EmergencyTriage::with([
                'patient',
                'doctor',
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $triage,
                'message' => 'Triage entry retrieved successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Triage entry not found.',
            ], 404);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'triage_level' => ['sometimes', 'string', 'in:I,II,III,IV,V'],
            'doctor_id' => ['nullable', 'integer'],
            'status' => ['sometimes', 'string'],
        ]);

        try {
            $triage = EmergencyTriage::findOrFail($id);
            $triage->update($request->only(['triage_level', 'doctor_id', 'status']));

            return response()->json([
                'success' => true,
                'data' => $triage->fresh()->load(['patient', 'doctor']),
                'message' => 'Triage entry updated successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Triage entry not found.',
            ], 404);
        }
    }

    public function waiting(Request $request): JsonResponse
    {
        try {
            $waiting = EmergencyTriage::where('institution_id', auth()->user()->institution_id)
                ->where('status', 'waiting')
                ->with(['patient', 'doctor'])
                ->orderBy('triage_level')
                ->orderBy('arrival_date')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $waiting,
                'message' => 'Waiting patients retrieved successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to retrieve waiting patients.',
            ], 500);
        }
    }

    public function triageStats(Request $request): JsonResponse
    {
        try {
            $institutionId = auth()->user()->institution_id;
            $today = now()->toDateString();

            $levels = EmergencyTriage::where('institution_id', $institutionId)
                ->whereDate('arrival_date', $today)
                ->selectRaw('triage_level, count(*) as count')
                ->groupBy('triage_level')
                ->pluck('count', 'triage_level')
                ->toArray();

            $statusCounts = EmergencyTriage::where('institution_id', $institutionId)
                ->whereDate('arrival_date', $today)
                ->selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            $stats = [
                'total_today' => EmergencyTriage::where('institution_id', $institutionId)
                    ->whereDate('arrival_date', $today)->count(),
                'by_level' => $levels,
                'by_status' => $statusCounts,
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Triage statistics retrieved successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to retrieve triage statistics.',
            ], 500);
        }
    }
}
