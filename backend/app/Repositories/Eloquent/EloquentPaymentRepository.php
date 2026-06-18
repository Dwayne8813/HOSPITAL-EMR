<?php

namespace App\Repositories\Eloquent;

use App\Models\Payment;
use App\Repositories\Interfaces\PaymentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentPaymentRepository implements PaymentRepositoryInterface
{
    public function __construct(
        protected Payment $model,
    ) {}

    public function findById(int $id): ?Payment
    {
        return $this->model->with('invoice', 'institution', 'receivedBy', 'paymentMethod')->find($id);
    }

    public function create(array $data): Payment
    {
        return $this->model->create($data);
    }

    public function listByInvoice(int $invoiceId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->where('invoice_id', $invoiceId)
            ->with('receivedBy', 'paymentMethod')
            ->orderBy('payment_date', 'desc')
            ->paginate($perPage);
    }

    public function listByInstitution(int $institutionId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model
            ->byInstitution($institutionId)
            ->with('invoice.patient', 'receivedBy', 'paymentMethod');

        if (!empty($filters['payment_method_id'])) {
            $query->where('payment_method_id', $filters['payment_method_id']);
        }

        if (!empty($filters['invoice_id'])) {
            $query->where('invoice_id', $filters['invoice_id']);
        }

        return $query->orderBy('payment_date', 'desc')->paginate($perPage);
    }

    public function getByDateRange(int $institutionId, string $from, string $to, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->byInstitution($institutionId)
            ->whereBetween('payment_date', [$from, $to])
            ->with('invoice.patient', 'receivedBy')
            ->orderBy('payment_date', 'desc')
            ->paginate($perPage);
    }

    public function getTotalByPeriod(int $institutionId, string $from, string $to): array
    {
        $payments = $this->model
            ->byInstitution($institutionId)
            ->whereBetween('payment_date', [$from, $to])
            ->get();

        return [
            'total_payments' => $payments->count(),
            'total_amount' => (float) $payments->sum('amount'),
            'by_method' => $payments->groupBy('payment_method_id')->map(fn ($items) => [
                'count' => $items->count(),
                'total' => (float) $items->sum('amount'),
            ])->toArray(),
        ];
    }
}
