<?php

namespace App\Repositories\Interfaces;

use App\Models\SurgerySchedule;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface SurgeryScheduleRepositoryInterface
{
    public function findById(int $id): ?SurgerySchedule;

    public function create(array $data): SurgerySchedule;

    public function update(int $id, array $data): SurgerySchedule;

    public function listByDoctor(int $doctorId, int $perPage = 15): LengthAwarePaginator;

    public function listByInstitution(int $institutionId, array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function getByDateRange(int $institutionId, string $from, string $to, int $perPage = 15): LengthAwarePaginator;

    public function getByOR(int $operatingRoomId, string $date): LengthAwarePaginator;

    public function getWithTeam(int $id): ?SurgerySchedule;
}
