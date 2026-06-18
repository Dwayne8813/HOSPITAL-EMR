<?php

namespace App\Repositories\Interfaces;

use App\Models\AuditLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AuditLogRepositoryInterface
{
    public function create(array $data): AuditLog;

    public function listByInstitution(int $institutionId, array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function listByUser(int $userId, int $perPage = 15): LengthAwarePaginator;

    public function listByModel(string $modelType, ?int $modelId = null, int $perPage = 15): LengthAwarePaginator;

    public function getByDateRange(int $institutionId, string $from, string $to, int $perPage = 15): LengthAwarePaginator;

    public function getByAction(int $institutionId, string $action, int $perPage = 15): LengthAwarePaginator;
}
