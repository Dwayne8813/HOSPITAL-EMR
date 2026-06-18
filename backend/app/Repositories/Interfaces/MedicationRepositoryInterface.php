<?php

namespace App\Repositories\Interfaces;

use App\Models\Medication;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface MedicationRepositoryInterface
{
    public function findById(int $id): ?Medication;

    public function create(array $data): Medication;

    public function update(int $id, array $data): Medication;

    public function list(int $institutionId, array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function search(int $institutionId, string $query, int $perPage = 15): LengthAwarePaginator;

    public function getLowStock(int $institutionId): Collection;

    public function getExpiring(int $institutionId, int $days = 30): Collection;

    public function updateStock(int $id, int $quantity, string $type): Medication;
}
