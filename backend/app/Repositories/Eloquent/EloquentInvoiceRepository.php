<?php

namespace App\Repositories\Eloquent;

use App\Models\Invoice;
use App\Repositories\Interfaces\InvoiceRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentInvoiceRepository implements InvoiceRepositoryInterface
{
    public function __construct(
        protected Invoice $model,
    ) {}

    public function findById(int $id): ?Invoice
    {
        return $this->model->with('institution', 'patient', 'creator', 'items', 'payments')->find($id);
    }

    public function create(array $data): Invoice
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Invoice
    {
        $invoice = $this->model->findOrFail($id);
        $invoice->update($data);

        return $invoice;
    }

    public function listByPatient(int $patientId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->byPatient($patientId)
            ->with('items', 'payments')
            ->orderBy('issued_at', 'desc')
            ->paginate($perPage);
    }

    public function listByInstitution(int $institutionId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model
            ->byInstitution($institutionId)
            ->with('patient', 'creator');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['patient_id'])) {
            $query->where('patient_id', $filters['patient_id']);
        }

        return $query->orderBy('issued_at', 'desc')->paginate($perPage);
    }

    public function getByStatus(int $institutionId, string $status, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->byInstitution($institutionId)
            ->where('status', $status)
            ->with('patient')
            ->orderBy('issued_at', 'desc')
            ->paginate($perPage);
    }

    public function getByDateRange(int $institutionId, string $from, string $to, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->byInstitution($institutionId)
            ->whereBetween('issued_at', [$from, $to])
            ->with('patient')
            ->orderBy('issued_at', 'desc')
            ->paginate($perPage);
    }

    public function generateNumber(int $institutionId): string
    {
        $year = now()->format('Y');
        $lastInvoice = $this->model
            ->byInstitution($institutionId)
            ->whereYear('issued_at', $year)
            ->orderBy('invoice_number', 'desc')
            ->first();

        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->invoice_number, -5);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return 'INV-' . $year . '-' . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }

    public function getTotalByPeriod(int $institutionId, string $from, string $to): array
    {
        $invoices = $this->model
            ->byInstitution($institutionId)
            ->whereBetween('issued_at', [$from, $to])
            ->get();

        return [
            'total_invoices' => $invoices->count(),
            'total_amount' => (float) $invoices->sum('total_amount'),
            'total_paid' => (float) $invoices->sum('paid_amount'),
            'total_pending' => (float) $invoices->sum('balance'),
            'by_status' => $invoices->groupBy('status')->map(fn ($items) => [
                'count' => $items->count(),
                'total' => (float) $items->sum('total_amount'),
            ])->toArray(),
        ];
    }
}
