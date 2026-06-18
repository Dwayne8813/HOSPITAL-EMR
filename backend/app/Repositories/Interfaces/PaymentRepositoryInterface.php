<?php

namespace App\Repositories\Interfaces;

use App\Models\Payment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PaymentRepositoryInterface
{
    public function findById(int $id): ?Payment;

    public function create(array $data): Payment;

    public function listByInvoice(int $invoiceId, int $perPage = 15): LengthAwarePaginator;

    public function listByInstitution(int $institutionId, array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function getByDateRange(int $institutionId, string $from, string $to, int $perPage = 15): LengthAwarePaginator;

    public function getTotalByPeriod(int $institutionId, string $from, string $to): array;
}
