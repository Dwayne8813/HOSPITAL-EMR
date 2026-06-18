<?php

namespace App\Repositories\Interfaces;

use App\Models\EmergencyTriage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface EmergencyTriageRepositoryInterface
{
    public function findById(int $id): ?EmergencyTriage;

    public function create(array $data): EmergencyTriage;

    public function update(int $id, array $data): EmergencyTriage;

    public function listByInstitution(int $institutionId, array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function getWaiting(int $institutionId, int $perPage = 15): LengthAwarePaginator;

    public function getByLevel(int $institutionId, string $level, int $perPage = 15): LengthAwarePaginator;

    public function getTodaysPatients(int $institutionId, int $perPage = 15): LengthAwarePaginator;
}
