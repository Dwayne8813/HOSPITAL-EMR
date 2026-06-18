<?php

namespace App\Repositories\Eloquent;

use App\Models\CashRegister;
use App\Repositories\Interfaces\CashRegisterRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class EloquentCashRegisterRepository implements CashRegisterRepositoryInterface
{
    public function __construct(
        protected CashRegister $model,
    ) {}

    public function findById(int $id): ?CashRegister
    {
        return $this->model->with('institution', 'opener')->find($id);
    }

    public function create(array $data): CashRegister
    {
        return $this->model->create($data);
    }

    public function open(int $id, float $openingAmount): CashRegister
    {
        $register = $this->model->findOrFail($id);
        $register->update([
            'status' => 'open',
            'opening_amount' => $openingAmount,
            'opened_at' => now(),
            'closed_at' => null,
            'closing_amount' => null,
        ]);

        return $register;
    }

    public function close(int $id, float $closingAmount): CashRegister
    {
        $register = $this->model->findOrFail($id);
        $register->update([
            'status' => 'closed',
            'closing_amount' => $closingAmount,
            'closed_at' => now(),
        ]);

        return $register;
    }

    public function getCurrent(int $institutionId): ?CashRegister
    {
        return $this->model
            ->byInstitution($institutionId)
            ->open()
            ->with('movements')
            ->first();
    }

    public function getMovements(int $id, ?string $from = null, ?string $to = null): Collection
    {
        $register = $this->model->findOrFail($id);
        $query = $register->movements()->with('performedBy');

        if ($from && $to) {
            $query->whereBetween('created_at', [$from, $to]);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function getArrears(int $id): Collection
    {
        $register = $this->model->findOrFail($id);

        return $register->arrears()->with('performedBy')->orderBy('created_at', 'desc')->get();
    }
}
