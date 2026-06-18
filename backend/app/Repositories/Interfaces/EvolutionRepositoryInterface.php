<?php

namespace App\Repositories\Interfaces;

use App\Models\Evolution;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface EvolutionRepositoryInterface
{
    public function findById(int $id): ?Evolution;

    public function create(array $data): Evolution;

    public function listByMedicalRecord(int $medicalRecordId): Collection;

    public function getByDoctor(int $doctorId, int $perPage = 15): LengthAwarePaginator;

    public function getByDateRange(int $institutionId, string $from, string $to, int $perPage = 15): LengthAwarePaginator;
}
