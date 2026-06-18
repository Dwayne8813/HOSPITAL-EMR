<?php

namespace App\Repositories\Interfaces;

use App\Models\DoctorSchedule;
use Illuminate\Database\Eloquent\Collection;

interface DoctorScheduleRepositoryInterface
{
    public function findById(int $id): ?DoctorSchedule;

    public function create(array $data): DoctorSchedule;

    public function update(int $id, array $data): DoctorSchedule;

    public function delete(int $id): bool;

    public function listByDoctor(int $doctorId): Collection;

    public function getByDayOfWeek(int $institutionId, string $dayOfWeek): Collection;

    public function getAvailableSlots(int $doctorId, string $date): Collection;
}
