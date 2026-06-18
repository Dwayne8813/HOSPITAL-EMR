<?php

namespace App\Repositories\Interfaces;

use App\Models\PurchaseOrder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PurchaseOrderRepositoryInterface
{
    public function findById(int $id): ?PurchaseOrder;

    public function create(array $data): PurchaseOrder;

    public function update(int $id, array $data): PurchaseOrder;

    public function list(int $institutionId, array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function getByStatus(int $institutionId, string $status, int $perPage = 15): LengthAwarePaginator;

    public function receiveOrder(int $id): PurchaseOrder;
}
