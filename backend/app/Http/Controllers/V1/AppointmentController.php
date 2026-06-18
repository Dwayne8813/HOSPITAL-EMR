<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Services\Appointment\AppointmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function __construct(
        private readonly AppointmentService $appointmentService,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $appointments = Appointment::where('institution_id', auth()->user()->institution_id)
                ->with(['patient', 'doctor', 'specialty'])
                ->orderBy('appointment_date', 'desc')
                ->paginate($request->input('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $appointments->items(),
                'message' => 'Appointments retrieved successfully.',
                'meta' => [
                    'current_page' => $appointments->currentPage(),
                    'last_page' => $appointments->lastPage(),
                    'per_page' => $appointments->perPage(),
                    'total' => $appointments->total(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to retrieve appointments.',
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'doctor_id' => ['required', 'integer', 'exists:users,id'],
            'specialty_id' => ['nullable', 'integer', 'exists:specialties,id'],
            'appointment_slot_id' => ['nullable', 'integer', 'exists:appointment_slots,id'],
            'scheduled_at' => ['required', 'date'],
            'duration_minutes' => ['nullable', 'integer', 'min:5', 'max:480'],
            'type' => ['nullable', 'string', 'in:consultation,follow_up,emergency,telemedicine'],
            'reason' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string'],
        ]);

        try {
            $appointment = $this->appointmentService->create($request->all());

            return response()->json([
                'success' => true,
                'data' => $appointment->load(['patient', 'doctor', 'specialty']),
                'message' => 'Appointment created successfully.',
            ], 201);
        } catch (\App\Exceptions\ScheduleConflictException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => $e->getMessage(),
            ], 409);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to create appointment.',
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $appointment = Appointment::with(['patient', 'doctor', 'specialty', 'reminders'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $appointment,
                'message' => 'Appointment retrieved successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Appointment not found.',
            ], 404);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'scheduled_at' => ['sometimes', 'date'],
            'doctor_id' => ['sometimes', 'integer', 'exists:users,id'],
            'specialty_id' => ['nullable', 'integer'],
            'type' => ['sometimes', 'string'],
            'reason' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'duration_minutes' => ['nullable', 'integer'],
        ]);

        try {
            $appointment = Appointment::findOrFail($id);
            $updated = $this->appointmentService->update($appointment, $request->all());

            return response()->json([
                'success' => true,
                'data' => $updated,
                'message' => 'Appointment updated successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Appointment not found.',
            ], 404);
        } catch (\App\Exceptions\ScheduleConflictException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => $e->getMessage(),
            ], 409);
        }
    }

    public function confirm(int $id): JsonResponse
    {
        try {
            $appointment = Appointment::findOrFail($id);
            $confirmed = $this->appointmentService->confirm($appointment);

            return response()->json([
                'success' => true,
                'data' => $confirmed,
                'message' => 'Appointment confirmed.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Appointment not found.',
            ], 404);
        }
    }

    public function cancel(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        try {
            $appointment = Appointment::findOrFail($id);
            $cancelled = $this->appointmentService->cancel($appointment, $request->input('reason'));

            return response()->json([
                'success' => true,
                'data' => $cancelled,
                'message' => 'Appointment cancelled.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Appointment not found.',
            ], 404);
        }
    }

    public function complete(int $id): JsonResponse
    {
        try {
            $appointment = Appointment::findOrFail($id);
            $completed = $this->appointmentService->complete($appointment);

            return response()->json([
                'success' => true,
                'data' => $completed,
                'message' => 'Appointment marked as completed.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Appointment not found.',
            ], 404);
        }
    }

    public function noShow(int $id): JsonResponse
    {
        try {
            $appointment = Appointment::findOrFail($id);
            $noShow = $this->appointmentService->noShow($appointment);

            return response()->json([
                'success' => true,
                'data' => $noShow,
                'message' => 'Appointment marked as no-show.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Appointment not found.',
            ], 404);
        }
    }

    public function availableSlots(Request $request): JsonResponse
    {
        $request->validate([
            'doctor_id' => ['required', 'integer', 'exists:users,id'],
            'date' => ['required', 'date'],
        ]);

        try {
            $slots = $this->appointmentService->getAvailableSlots(
                $request->input('doctor_id'),
                $request->input('date')
            );

            return response()->json([
                'success' => true,
                'data' => $slots,
                'message' => 'Available slots retrieved.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to retrieve available slots.',
            ], 500);
        }
    }
}
