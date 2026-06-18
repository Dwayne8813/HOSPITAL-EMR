<?php

namespace App\Repositories\Interfaces;

use App\Models\Institution;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface InstitutionRepositoryInterface
{
    public function findById(int $id): ?Institution;

    public function findBySlug(string $slug): ?Institution;

    public function create(array $data): Institution;

    public function update(int $id, array $data): Institution;

    public function delete(int $id): bool;

    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function search(string $query, int $perPage = 15): LengthAwarePaginator;
}
