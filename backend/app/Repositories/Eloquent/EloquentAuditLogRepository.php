<?php

namespace App\Repositories\Eloquent;

use App\Models\AuditLog;
use App\Repositories\Interfaces\AuditLogRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentAuditLogRepository implements AuditLogRepositoryInterface
{
    public function __construct(
        protected AuditLog $model,
    ) {}

    public function create(array $data): AuditLog
    {
        return $this->model->create($data);
    }

    public function listByInstitution(int $institutionId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->byInstitution($institutionId)->with('user');

        if (!empty($filters['action'])) {
            $query->where('action', $filters['action']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['model_type'])) {
            $query->where('model_type', $filters['model_type']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function listByUser(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->byUser($userId)
            ->with('institution')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function listByModel(string $modelType, ?int $modelId = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->byModel($modelType)->with('user', 'institution');

        if ($modelId) {
            $query->where('model_id', $modelId);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getByDateRange(int $institutionId, string $from, string $to, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->byInstitution($institutionId)
            ->whereBetween('created_at', [$from, $to])
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getByAction(int $institutionId, string $action, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->byInstitution($institutionId)
            ->byAction($action)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}
