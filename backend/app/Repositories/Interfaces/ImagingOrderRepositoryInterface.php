<?php

namespace App\Repositories\Interfaces;

use App\Models\ImagingOrder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ImagingOrderRepositoryInterface
{
    public function findById(int $id): ?ImagingOrder;

    public function create(array $data): ImagingOrder;

    public function update(int $id, array $data): ImagingOrder;

    public function listByPatient(int $patientId, int $perPage = 15): LengthAwarePaginator;

    public function listByInstitution(int $institutionId, array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function getByStatus(int $institutionId, string $status, int $perPage = 15): LengthAwarePaginator;
}
