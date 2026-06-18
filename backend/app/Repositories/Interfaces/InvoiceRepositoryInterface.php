<?php

namespace App\Repositories\Interfaces;

use App\Models\Invoice;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface InvoiceRepositoryInterface
{
    public function findById(int $id): ?Invoice;

    public function create(array $data): Invoice;

    public function update(int $id, array $data): Invoice;

    public function listByPatient(int $patientId, int $perPage = 15): LengthAwarePaginator;

    public function listByInstitution(int $institutionId, array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function getByStatus(int $institutionId, string $status, int $perPage = 15): LengthAwarePaginator;

    public function getByDateRange(int $institutionId, string $from, string $to, int $perPage = 15): LengthAwarePaginator;

    public function generateNumber(int $institutionId): string;

    public function getTotalByPeriod(int $institutionId, string $from, string $to): array;
}
