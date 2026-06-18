<?php

namespace App\Repositories\Interfaces;

use App\Models\Hospitalization;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface HospitalizationRepositoryInterface
{
    public function findById(int $id): ?Hospitalization;

    public function create(array $data): Hospitalization;

    public function update(int $id, array $data): Hospitalization;

    public function listByPatient(int $patientId, int $perPage = 15): LengthAwarePaginator;

    public function listByInstitution(int $institutionId, array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function admit(array $data): Hospitalization;

    public function discharge(int $id, array $data): Hospitalization;

    public function transfer(int $id, int $newBedId, ?string $reason = null): Hospitalization;

    public function getActive(int $institutionId, int $perPage = 15): LengthAwarePaginator;

    public function getByDateRange(int $institutionId, string $from, string $to, int $perPage = 15): LengthAwarePaginator;
}
