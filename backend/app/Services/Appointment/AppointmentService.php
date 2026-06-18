<?php

declare(strict_types=1);

namespace App\Services\Appointment;

use App\DTOs\AppointmentDTO;
use App\Models\Appointment;
use App\Models\AppointmentSlot;
use App\Models\DoctorSchedule;
use App\Models\Institution;
use App\Repositories\Interfaces\AppointmentRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AppointmentService
{
    public function __construct(
        private readonly AppointmentRepositoryInterface $appointmentRepository,
    ) {
    }

    public function create(array $data): Appointment
    {
        $institutionId = auth()->user()->institution_id;

        $this->checkConflicts(
            $institutionId,
            $data['doctor_id'],
            $data['scheduled_at'],
            $data['duration_minutes'] ?? 30
        );

        $scheduledAt = Carbon::parse($data['scheduled_at']);
        $durationMinutes = $data['duration_minutes'] ?? 30;
        $endTime = $scheduledAt->copy()->addMinutes($durationMinutes);

        return Appointment::create([
            'institution_id' => $institutionId,
            'patient_id' => $data['patient_id'],
            'doctor_id' => $data['doctor_id'],
            'specialty_id' => $data['specialty_id'] ?? null,
            'appointment_slot_id' => $data['appointment_slot_id'] ?? null,
            'appointment_date' => $scheduledAt->toDateString(),
            'start_time' => $scheduledAt->toTimeString(),
            'end_time' => $endTime->toTimeString(),
            'status' => 'scheduled',
            'reason' => $data['reason'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);
    }

    public function update(Appointment $appointment, array $data): Appointment
    {
        if (isset($data['scheduled_at']) || isset($data['doctor_id'])) {
            $doctorId = $data['doctor_id'] ?? $appointment->doctor_id;
            $scheduledAt = $data['scheduled_at'] ?? $appointment->scheduled_at;
            $duration = $data['duration_minutes'] ?? 30;

            $this->checkConflicts(
                $appointment->institution_id,
                $doctorId,
                $scheduledAt,
                $duration,
                $appointment->id
            );
        }

        $appointment->update($data);
        return $appointment->fresh()->load(['patient', 'doctor', 'specialty']);
    }

    public function cancel(Appointment $appointment, string $reason): Appointment
    {
        $appointment->update([
            'status' => 'cancelled',
            'cancellation_reason' => $reason,
            'cancelled_at' => now(),
        ]);

        return $appointment->fresh();
    }

    public function confirm(Appointment $appointment): Appointment
    {
        $appointment->update(['status' => 'confirmed']);
        return $appointment->fresh();
    }

    public function complete(Appointment $appointment): Appointment
    {
        $appointment->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return $appointment->fresh();
    }

    public function noShow(Appointment $appointment): Appointment
    {
        $appointment->update([
            'status' => 'no_show',
            'no_show' => true,
        ]);

        return $appointment->fresh();
    }

    public function list(Request $request): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Appointment::query()
            ->byInstitution(auth()->user()->institution_id)
            ->with(['patient', 'doctor', 'specialty']);

        if ($request->has('doctor_id')) {
            $query->byDoctor($request->input('doctor_id'));
        }

        if ($request->has('patient_id')) {
            $query->byPatient($request->input('patient_id'));
        }

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('date')) {
            $query->byDate($request->input('date'));
        }

        if ($request->has('date_from')) {
            $query->where('scheduled_at', '>=', $request->input('date_from'));
        }

        if ($request->has('date_to')) {
            $query->where('scheduled_at', '<=', $request->input('date_to'));
        }

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('patient', fn ($pq) => $pq->search($search))
                  ->orWhereHas('doctor', fn ($dq) => $dq->where('name', 'LIKE', "%{$search}%"));
            });
        }

        $sortBy = $request->input('sort_by', 'scheduled_at');
        $sortDirection = $request->input('sort_direction', 'desc');

        return $query->orderBy($sortBy, $sortDirection)
            ->paginate($request->input('per_page', 15));
    }

    public function getAvailableSlots(int $doctorId, string $date): array
    {
        $dayOfWeek = Carbon::parse($date)->dayOfWeekIso;

        $schedule = DoctorSchedule::where('doctor_id', $doctorId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->first();

        if (!$schedule) {
            return [];
        }

        $bookedSlots = Appointment::where('doctor_id', $doctorId)
            ->whereDate('scheduled_at', $date)
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->pluck('scheduled_at')
            ->map(fn ($dt) => Carbon::parse($dt)->format('H:i'))
            ->toArray();

        $slots = [];
        $start = Carbon::parse($schedule->start_time);
        $end = Carbon::parse($schedule->end_time);
        $slotDuration = $schedule->slot_duration_minutes ?? 30;

        while ($start->lt($end)) {
            $slotTime = $start->format('H:i');
            $slots[] = [
                'time' => $slotTime,
                'available' => !in_array($slotTime, $bookedSlots),
            ];
            $start->addMinutes($slotDuration);
        }

        return $slots;
    }

    public function getDoctorSchedule(int $doctorId): \Illuminate\Database\Eloquent\Collection
    {
        return DoctorSchedule::where('doctor_id', $doctorId)
            ->orderBy('day_of_week')
            ->get();
    }

    public function updateDoctorSchedule(int $doctorId, array $scheduleData): \Illuminate\Support\Collection
    {
        return DB::transaction(function () use ($doctorId, $scheduleData) {
            DoctorSchedule::where('doctor_id', $doctorId)->delete();

            $schedules = collect();
            foreach ($scheduleData as $dayData) {
                $schedules->push(DoctorSchedule::create([
                    'doctor_id' => $doctorId,
                    'institution_id' => auth()->user()->institution_id,
                    'day_of_week' => $dayData['day_of_week'],
                    'start_time' => $dayData['start_time'],
                    'end_time' => $dayData['end_time'],
                    'slot_duration_minutes' => $dayData['slot_duration_minutes'] ?? 30,
                    'is_active' => $dayData['is_active'] ?? true,
                ]));
            }

            return $schedules;
        });
    }

    public function getUpcoming(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        return Appointment::where('doctor_id', $userId)
            ->upcoming()
            ->with(['patient', 'doctor', 'specialty'])
            ->orderBy('scheduled_at')
            ->get();
    }

    public function getCalendarView(int $doctorId, string $startDate, string $endDate): \Illuminate\Database\Eloquent\Collection
    {
        return Appointment::where('doctor_id', $doctorId)
            ->where('scheduled_at', '>=', $startDate)
            ->where('scheduled_at', '<=', $endDate)
            ->whereNotIn('status', ['cancelled'])
            ->with(['patient', 'specialty'])
            ->orderBy('scheduled_at')
            ->get();
    }

    public function getStats(Institution $institution): array
    {
        $today = now()->toDateString();
        $startOfMonth = now()->startOfMonth();

        return [
            'today_total' => Appointment::where('institution_id', $institution->id)
                ->byDate($today)->count(),
            'today_completed' => Appointment::where('institution_id', $institution->id)
                ->byDate($today)->where('status', 'completed')->count(),
            'today_pending' => Appointment::where('institution_id', $institution->id)
                ->byDate($today)->whereIn('status', ['scheduled', 'confirmed'])->count(),
            'today_cancelled' => Appointment::where('institution_id', $institution->id)
                ->byDate($today)->where('status', 'cancelled')->count(),
            'today_no_show' => Appointment::where('institution_id', $institution->id)
                ->byDate($today)->where('status', 'no_show')->count(),
            'month_total' => Appointment::where('institution_id', $institution->id)
                ->where('created_at', '>=', $startOfMonth)->count(),
            'status_distribution' => Appointment::where('institution_id', $institution->id)
                ->where('created_at', '>=', $startOfMonth)
                ->selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray(),
        ];
    }

    private function checkConflicts(int $institutionId, int $doctorId, string $scheduledAt, int $durationMinutes, ?int $excludeId = null): void
    {
        $start = Carbon::parse($scheduledAt);
        $end = $start->copy()->addMinutes($durationMinutes);

        $conflict = Appointment::where('institution_id', $institutionId)
            ->where('doctor_id', $doctorId)
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('scheduled_at', [$start, $end->copy()->subMinute()])
                    ->orWhere(function ($q) use ($start, $end) {
                        $q->where('scheduled_at', '<=', $start)
                          ->whereRaw("DATE_ADD(scheduled_at, INTERVAL 30 MINUTE) > ?", [$start]);
                    });
            });

        if ($excludeId) {
            $conflict->where('id', '!=', $excludeId);
        }

        if ($conflict->exists()) {
            throw new \App\Exceptions\ScheduleConflictException(
                'The doctor has a scheduling conflict at this time.'
            );
        }
    }
}
