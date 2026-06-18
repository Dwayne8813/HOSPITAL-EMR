<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\DoctorSchedule;
use App\Services\Appointment\AppointmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DoctorScheduleController extends Controller
{
    public function __construct(
        private readonly AppointmentService $appointmentService,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'doctor_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        try {
            $schedules = $this->appointmentService->getDoctorSchedule($request->input('doctor_id'));

            return response()->json([
                'success' => true,
                'data' => $schedules,
                'message' => 'Doctor schedule retrieved successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to retrieve schedule.',
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'doctor_id' => ['required', 'integer', 'exists:users,id'],
            'schedule' => ['required', 'array', 'min:1'],
            'schedule.*.day_of_week' => ['required', 'integer', 'between:1,7'],
            'schedule.*.start_time' => ['required', 'date_format:H:i'],
            'schedule.*.end_time' => ['required', 'date_format:H:i', 'after:schedule.*.start_time'],
            'schedule.*.slot_duration_minutes' => ['nullable', 'integer', 'min:5', 'max:120'],
            'schedule.*.is_active' => ['nullable', 'boolean'],
        ]);

        try {
            $schedules = $this->appointmentService->updateDoctorSchedule(
                $request->input('doctor_id'),
                $request->input('schedule')
            );

            return response()->json([
                'success' => true,
                'data' => $schedules,
                'message' => 'Doctor schedule created successfully.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to create schedule.',
            ], 500);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'day_of_week' => ['sometimes', 'integer', 'between:1,7'],
            'start_time' => ['sometimes', 'date_format:H:i'],
            'end_time' => ['sometimes', 'date_format:H:i', 'after:start_time'],
            'slot_duration_minutes' => ['nullable', 'integer', 'min:5', 'max:120'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        try {
            $schedule = DoctorSchedule::findOrFail($id);
            $schedule->update($request->all());

            return response()->json([
                'success' => true,
                'data' => $schedule->fresh(),
                'message' => 'Schedule updated successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Schedule not found.',
            ], 404);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $schedule = DoctorSchedule::findOrFail($id);
            $schedule->delete();

            return response()->json([
                'success' => true,
                'data' => null,
                'message' => 'Schedule deleted successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Schedule not found.',
            ], 404);
        }
    }
}
