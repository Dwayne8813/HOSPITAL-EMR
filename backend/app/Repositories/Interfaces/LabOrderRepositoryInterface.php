<?php

namespace App\Repositories\Interfaces;

use App\Models\LabOrder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface LabOrderRepositoryInterface
{
    public function findById(int $id): ?LabOrder;

    public function create(array $data): LabOrder;

    public function update(int $id, array $data): LabOrder;

    public function listByPatient(int $patientId, int $perPage = 15): LengthAwarePaginator;

    public function listByInstitution(int $institutionId, array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function getWithItems(int $id): ?LabOrder;

    public function getByStatus(int $institutionId, string $status, int $perPage = 15): LengthAwarePaginator;

    public function getByDateRange(int $institutionId, string $from, string $to, int $perPage = 15): LengthAwarePaginator;
}
