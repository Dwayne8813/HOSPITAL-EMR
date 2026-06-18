<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\SurgerySchedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SurgeryScheduleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $query = SurgerySchedule::where('institution_id', auth()->user()->institution_id)
                ->with(['patient', 'doctor', 'operatingRoom']);

            if ($request->has('doctor_id')) {
                $query->where('doctor_id', $request->input('doctor_id'));
            }

            if ($request->has('status')) {
                $query->where('status', $request->input('status'));
            }

            if ($request->has('date_from')) {
                $query->where('scheduled_at', '>=', $request->input('date_from'));
            }

            if ($request->has('date_to')) {
                $query->where('scheduled_at', '<=', $request->input('date_to'));
            }

            $surgeries = $query->orderBy('scheduled_at')
                ->paginate($request->input('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $surgeries->items(),
                'message' => 'Surgery schedules retrieved successfully.',
                'meta' => [
                    'current_page' => $surgeries->currentPage(),
                    'last_page' => $surgeries->lastPage(),
                    'per_page' => $surgeries->perPage(),
                    'total' => $surgeries->total(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to retrieve surgery schedules.',
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'doctor_id' => ['required', 'integer', 'exists:users,id'],
            'operating_room_id' => ['nullable', 'integer', 'exists:operating_rooms,id'],
            'procedure_name' => ['required', 'string', 'max:255'],
            'procedure_code' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'scheduled_at' => ['required', 'date'],
            'estimated_duration_minutes' => ['nullable', 'integer', 'min:15'],
            'notes' => ['nullable', 'string'],
        ]);

        try {
            $surgery = SurgerySchedule::create([
                'institution_id' => auth()->user()->institution_id,
                ...$request->all(),
                'status' => 'scheduled',
            ]);

            return response()->json([
                'success' => true,
                'data' => $surgery->load(['patient', 'doctor', 'operatingRoom']),
                'message' => 'Surgery scheduled successfully.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to schedule surgery.',
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $surgery = SurgerySchedule::with([
                'patient',
                'doctor',
                'operatingRoom',
                'team.user',
                'materials',
                'report',
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $surgery,
                'message' => 'Surgery schedule retrieved successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Surgery schedule not found.',
            ], 404);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'operating_room_id' => ['nullable', 'integer'],
            'scheduled_at' => ['sometimes', 'date'],
            'estimated_duration_minutes' => ['nullable', 'integer'],
            'status' => ['sometimes', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        try {
            $surgery = SurgerySchedule::findOrFail($id);
            $surgery->update($request->all());

            return response()->json([
                'success' => true,
                'data' => $surgery->fresh()->load(['patient', 'doctor', 'operatingRoom']),
                'message' => 'Surgery schedule updated successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Surgery schedule not found.',
            ], 404);
        }
    }
}
