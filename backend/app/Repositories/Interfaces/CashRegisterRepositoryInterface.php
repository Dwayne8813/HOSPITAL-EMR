<?php

namespace App\Repositories\Interfaces;

use App\Models\CashRegister;
use Illuminate\Database\Eloquent\Collection;

interface CashRegisterRepositoryInterface
{
    public function findById(int $id): ?CashRegister;

    public function create(array $data): CashRegister;

    public function open(int $id, float $openingAmount): CashRegister;

    public function close(int $id, float $closingAmount): CashRegister;

    public function getCurrent(int $institutionId): ?CashRegister;

    public function getMovements(int $id, ?string $from = null, ?string $to = null): Collection;

    public function getArrears(int $id): Collection;
}
