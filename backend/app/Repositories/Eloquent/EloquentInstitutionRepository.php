<?php

namespace App\Repositories\Eloquent;

use App\Models\Institution;
use App\Repositories\Interfaces\InstitutionRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentInstitutionRepository implements InstitutionRepositoryInterface
{
    public function __construct(
        protected Institution $model,
    ) {}

    public function findById(int $id): ?Institution
    {
        return $this->model->find($id);
    }

    public function findBySlug(string $slug): ?Institution
    {
        return $this->model->where('slug', $slug)->first();
    }

    public function create(array $data): Institution
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Institution
    {
        $institution = $this->model->findOrFail($id);
        $institution->update($data);

        return $institution;
    }

    public function delete(int $id): bool
    {
        $institution = $this->model->findOrFail($id);

        return $institution->delete();
    }

    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->query();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('name')->paginate($perPage);
    }

    public function search(string $query, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('nit', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%");
            })
            ->orderBy('name')
            ->paginate($perPage);
    }
}
