<?php

namespace App\Repositories\Eloquent;

use App\Models\DoctorSchedule;
use App\Repositories\Interfaces\DoctorScheduleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class EloquentDoctorScheduleRepository implements DoctorScheduleRepositoryInterface
{
    public function __construct(
        protected DoctorSchedule $model,
    ) {}

    public function findById(int $id): ?DoctorSchedule
    {
        return $this->model->with('doctor', 'institution')->find($id);
    }

    public function create(array $data): DoctorSchedule
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): DoctorSchedule
    {
        $schedule = $this->model->findOrFail($id);
        $schedule->update($data);

        return $schedule;
    }

    public function delete(int $id): bool
    {
        $schedule = $this->model->findOrFail($id);

        return $schedule->delete();
    }

    public function listByDoctor(int $doctorId): Collection
    {
        return $this->model
            ->byDoctor($doctorId)
            ->active()
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();
    }

    public function getByDayOfWeek(int $institutionId, string $dayOfWeek): Collection
    {
        return $this->model
            ->byInstitution($institutionId)
            ->byDay($dayOfWeek)
            ->active()
            ->with('doctor')
            ->get();
    }

    public function getAvailableSlots(int $doctorId, string $date): Collection
    {
        $dayOfWeek = strtolower(\Carbon\Carbon::parse($date)->englishDayOfWeek);

        $schedules = $this->model
            ->where('doctor_id', $doctorId)
            ->active()
            ->where('day_of_week', $dayOfWeek)
            ->get();

        $bookedSlots = \App\Models\Appointment::where('doctor_id', $doctorId)
            ->whereDate('scheduled_at', $date)
            ->whereNotIn('status', ['cancelled'])
            ->pluck('scheduled_at')
            ->map(fn ($dt) => $dt->format('H:i'));

        $slots = collect();
        foreach ($schedules as $schedule) {
            $start = \Carbon\Carbon::parse($date . ' ' . $schedule->start_time);
            $end = \Carbon\Carbon::parse($date . ' ' . $schedule->end_time);
            $breakStart = $schedule->break_start ? \Carbon\Carbon::parse($date . ' ' . $schedule->break_start) : null;
            $breakEnd = $schedule->break_end ? \Carbon\Carbon::parse($date . ' ' . $schedule->break_end) : null;

            while ($start->lt($end)) {
                $isBreak = $breakStart && $breakEnd && $start->gte($breakStart) && $start->lt($breakEnd);
                $slots->push([
                    'time' => $start->format('H:i'),
                    'available' => !$isBreak && !$bookedSlots->contains($start->format('H:i')),
                    'is_break' => $isBreak,
                ]);
                $start->addMinutes(30);
            }
        }

        return $slots;
    }
}
