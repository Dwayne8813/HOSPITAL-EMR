<?php

namespace App\Repositories\Eloquent;

use App\Models\Appointment;
use App\Repositories\Interfaces\AppointmentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EloquentAppointmentRepository implements AppointmentRepositoryInterface
{
    public function __construct(
        protected Appointment $model,
    ) {}

    public function findById(int $id): ?Appointment
    {
        return $this->model->with('patient', 'doctor', 'specialty', 'institution')->find($id);
    }

    public function create(array $data): Appointment
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Appointment
    {
        $appointment = $this->model->findOrFail($id);
        $appointment->update($data);

        return $appointment->fresh(['patient', 'doctor', 'specialty']);
    }

    public function delete(int $id): bool
    {
        $appointment = $this->model->findOrFail($id);

        return $appointment->delete();
    }

    public function listByDoctor(int $doctorId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model
            ->byDoctor($doctorId)
            ->with('patient', 'specialty');

        if (!empty($filters['date'])) {
            $query->byDate($filters['date']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('scheduled_at')->paginate($perPage);
    }

    public function listByPatient(int $patientId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->byPatient($patientId)
            ->with('doctor', 'specialty')
            ->orderBy('scheduled_at', 'desc')
            ->paginate($perPage);
    }

    public function listByInstitution(int $institutionId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model
            ->byInstitution($institutionId)
            ->with('patient', 'doctor', 'specialty');

        if (!empty($filters['doctor_id'])) {
            $query->byDoctor($filters['doctor_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['date'])) {
            $query->byDate($filters['date']);
        }

        return $query->orderBy('scheduled_at')->paginate($perPage);
    }

    public function listByDate(int $institutionId, string $date, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->byInstitution($institutionId)
            ->byDate($date)
            ->with('patient', 'doctor', 'specialty')
            ->orderBy('scheduled_at')
            ->paginate($perPage);
    }

    public function confirm(int $id): Appointment
    {
        $appointment = $this->model->findOrFail($id);
        $appointment->update(['status' => 'confirmed']);

        return $appointment->fresh(['patient', 'doctor']);
    }

    public function cancel(int $id, ?string $reason = null): Appointment
    {
        $appointment = $this->model->findOrFail($id);
        $appointment->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
        ]);

        return $appointment->fresh(['patient', 'doctor']);
    }

    public function getAvailableSlots(int $doctorId, string $date): Collection
    {
        $schedules = \App\Models\DoctorSchedule::where('doctor_id', $doctorId)
            ->active()
            ->where('day_of_week', strtolower(now()->setDateFrom($date)->englishDayOfWeek))
            ->get();

        $bookedSlots = $this->model
            ->where('doctor_id', $doctorId)
            ->byDate($date)
            ->whereNotIn('status', ['cancelled'])
            ->pluck('scheduled_at')
            ->map(fn ($dt) => $dt->format('H:i'));

        $availableSlots = collect();
        foreach ($schedules as $schedule) {
            $start = \Carbon\Carbon::parse($date . ' ' . $schedule->start_time);
            $end = \Carbon\Carbon::parse($date . ' ' . $schedule->end_time);

            while ($start->lt($end)) {
                if (!$bookedSlots->contains($start->format('H:i'))) {
                    $availableSlots->push($start->format('H:i'));
                }
                $start->addMinutes(30);
            }
        }

        return $availableSlots;
    }

    public function getUpcoming(int $institutionId, int $limit = 10): Collection
    {
        return $this->model
            ->byInstitution($institutionId)
            ->upcoming()
            ->with('patient', 'doctor', 'specialty')
            ->orderBy('scheduled_at')
            ->limit($limit)
            ->get();
    }

    public function getByDateRange(int $institutionId, string $from, string $to, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->byInstitution($institutionId)
            ->whereBetween('scheduled_at', [$from, $to])
            ->with('patient', 'doctor', 'specialty')
            ->orderBy('scheduled_at')
            ->paginate($perPage);
    }

    public function countByStatus(int $institutionId, ?string $date = null): array
    {
        $query = $this->model->byInstitution($institutionId);

        if ($date) {
            $query->byDate($date);
        }

        return $query->select('status', \DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
    }
}
