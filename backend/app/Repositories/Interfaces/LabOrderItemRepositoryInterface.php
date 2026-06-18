<?php

namespace App\Repositories\Interfaces;

use App\Models\LabOrderItem;
use Illuminate\Database\Eloquent\Collection;

interface LabOrderItemRepositoryInterface
{
    public function findById(int $id): ?LabOrderItem;

    public function create(array $data): LabOrderItem;

    public function update(int $id, array $data): LabOrderItem;

    public function listByLabOrder(int $labOrderId): Collection;

    public function addResults(int $id, array $results): LabOrderItem;

    public function validate(int $id, int $validatedBy, ?string $notes = null): LabOrderItem;
}
