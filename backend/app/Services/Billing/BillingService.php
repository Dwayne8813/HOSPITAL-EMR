<?php

declare(strict_types=1);

namespace App\Services\Billing;

use App\DTOs\InvoiceDTO;
use App\DTOs\PaymentDTO;
use App\Models\CashArrear;
use App\Models\CashMovement;
use App\Models\CashRegister;
use App\Models\Institution;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BillingService
{
    public function createInvoice(array $data): Invoice
    {
        return DB::transaction(function () use ($data) {
            $institutionId = auth()->user()->institution_id;
            $invoiceNumber = $this->generateInvoiceNumber($institutionId);

            $subtotal = 0;
            $taxAmount = 0;
            $discountAmount = 0;

            if (isset($data['items'])) {
                foreach ($data['items'] as $item) {
                    $itemTotal = $item['quantity'] * $item['unit_price'];
                    $itemDiscount = $item['discount'] ?? 0;
                    $itemTax = ($itemTotal - $itemDiscount) * (($item['tax_rate'] ?? 0) / 100);

                    $subtotal += $itemTotal;
                    $discountAmount += $itemDiscount;
                    $taxAmount += $itemTax;
                }
            }

            $totalAmount = $subtotal - $discountAmount + $taxAmount;

            $invoice = Invoice::create([
                'institution_id' => $institutionId,
                'patient_id' => $data['patient_id'],
                'created_by' => auth()->id(),
                'invoice_number' => $invoiceNumber,
                'type' => $data['type'] ?? 'consultation',
                'status' => 'pending',
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'paid_amount' => 0,
                'balance' => $totalAmount,
                'currency' => $data['currency'] ?? 'USD',
                'notes' => $data['notes'] ?? null,
                'due_date' => $data['due_date'] ?? now()->addDays(30)->toDateString(),
                'issued_at' => now(),
            ]);

            if (isset($data['items'])) {
                foreach ($data['items'] as $item) {
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'service_catalog_id' => $item['service_catalog_id'] ?? null,
                        'description' => $item['description'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'total_price' => ($item['quantity'] * $item['unit_price']) - ($item['discount'] ?? 0),
                        'discount' => $item['discount'] ?? 0,
                        'tax_rate' => $item['tax_rate'] ?? 0,
                    ]);
                }
            }

            return $invoice->load(['items', 'patient', 'creator']);
        });
    }

    public function getInvoice(int $id): Invoice
    {
        return Invoice::with([
            'items.serviceCatalog',
            'patient',
            'creator',
            'payments',
            'insuranceClaim',
        ])->findOrFail($id);
    }

    public function listInvoices(Request $request): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Invoice::query()
            ->byInstitution(auth()->user()->institution_id)
            ->with(['patient', 'creator']);

        if ($request->has('patient_id')) {
            $query->byPatient($request->input('patient_id'));
        }

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->has('date_from')) {
            $query->where('issued_at', '>=', $request->input('date_from'));
        }

        if ($request->has('date_to')) {
            $query->where('issued_at', '<=', $request->input('date_to'));
        }

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'LIKE', "%{$search}%")
                  ->orWhereHas('patient', fn ($pq) => $pq->search($search));
            });
        }

        return $query->orderByDesc('issued_at')
            ->paginate($request->input('per_page', 15));
    }

    public function addPayment(Invoice $invoice, array $data): Payment
    {
        return DB::transaction(function () use ($invoice, $data) {
            $paymentNumber = $this->generatePaymentNumber($invoice->institution_id);

            $payment = Payment::create([
                'institution_id' => $invoice->institution_id,
                'invoice_id' => $invoice->id,
                'patient_id' => $invoice->patient_id,
                'payment_number' => $paymentNumber,
                'amount' => $data['amount'],
                'method' => $data['method'],
                'status' => 'completed',
                'reference' => $data['reference'] ?? null,
                'notes' => $data['notes'] ?? null,
                'received_by' => auth()->id(),
                'payment_date' => $data['payment_date'] ?? now()->toDateTimeString(),
            ]);

            $invoice->increment('paid_amount', $data['amount']);
            $invoice->update([
                'balance' => $invoice->total_amount - $invoice->fresh()->paid_amount,
                'status' => $invoice->fresh()->paid_amount >= $invoice->total_amount ? 'paid' : 'partially_paid',
            ]);

            if ($invoice->fresh()->paid_amount >= $invoice->total_amount) {
                $invoice->update('paid_at', now());
            }

            return $payment->load(['invoice', 'patient']);
        });
    }

    public function getCashRegister(Institution $institution): ?CashRegister
    {
        return CashRegister::where('institution_id', $institution->id)
            ->where('status', 'open')
            ->with(['movements', 'cashier'])
            ->first();
    }

    public function openCashRegister(Institution $institution, int $userId, float $initialAmount): CashRegister
    {
        $existing = $this->getCashRegister($institution);
        if ($existing) {
            throw new \App\Exceptions\BusinessLogicException('A cash register is already open.');
        }

        return CashRegister::create([
            'institution_id' => $institution->id,
            'user_id' => $userId,
            'initial_amount' => $initialAmount,
            'status' => 'open',
            'opened_at' => now(),
        ]);
    }

    public function closeCashRegister(int $registerId, int $userId, float $actualAmount): CashRegister
    {
        return DB::transaction(function () use ($registerId, $userId, $actualAmount) {
            $register = CashRegister::findOrFail($registerId);

            $movements = CashMovement::where('cash_register_id', $registerId)->get();
            $totalInflows = $movements->where('type', 'inflow')->sum('amount');
            $totalOutflows = $movements->where('type', 'outflow')->sum('amount');
            $expectedAmount = $register->initial_amount + $totalInflows - $totalOutflows;
            $difference = $actualAmount - $expectedAmount;

            $register->update([
                'final_amount' => $actualAmount,
                'expected_amount' => $expectedAmount,
                'difference' => $difference,
                'status' => 'closed',
                'closed_at' => now(),
                'closed_by' => $userId,
            ]);

            if (abs($difference) > 0.01) {
                CashArrear::create([
                    'cash_register_id' => $registerId,
                    'institution_id' => $register->institution_id,
                    'expected_amount' => $expectedAmount,
                    'actual_amount' => $actualAmount,
                    'difference' => $difference,
                    'type' => $difference > 0 ? 'surplus' : 'shortage',
                    'performed_by' => $userId,
                    'notes' => 'Cash register closing discrepancy.',
                ]);
            }

            return $register->fresh();
        });
    }

    public function addCashMovement(int $registerId, string $type, float $amount, string $description): CashMovement
    {
        $register = CashRegister::findOrFail($registerId);

        if ($register->status !== 'open') {
            throw new \App\Exceptions\BusinessLogicException('Cash register is not open.');
        }

        return CashMovement::create([
            'cash_register_id' => $registerId,
            'institution_id' => $register->institution_id,
            'type' => $type,
            'amount' => $amount,
            'description' => $description,
            'performed_by' => auth()->id(),
            'movement_date' => now(),
        ]);
    }

    public function getArrears(int $registerId): array
    {
        $register = CashRegister::findOrFail($registerId);
        $movements = CashMovement::where('cash_register_id', $registerId)->get();

        $totalInflows = $movements->where('type', 'inflow')->sum('amount');
        $totalOutflows = $movements->where('type', 'outflow')->sum('amount');
        $expectedAmount = $register->initial_amount + $totalInflows - $totalOutflows;

        return [
            'initial_amount' => $register->initial_amount,
            'total_inflows' => $totalInflows,
            'total_outflows' => $totalOutflows,
            'expected_amount' => $expectedAmount,
            'actual_amount' => $register->final_amount,
            'difference' => $register->difference,
        ];
    }

    public function getStats(Institution $institution): array
    {
        $startOfMonth = now()->startOfMonth();
        $startOfYear = now()->startOfYear();

        return [
            'total_revenue' => Invoice::where('institution_id', $institution->id)
                ->where('status', 'paid')
                ->sum('total_amount'),
            'monthly_revenue' => Invoice::where('institution_id', $institution->id)
                ->where('status', 'paid')
                ->where('paid_at', '>=', $startOfMonth)
                ->sum('total_amount'),
            'pending_amount' => Invoice::where('institution_id', $institution->id)
                ->where('status', '!=', 'paid')
                ->sum('balance'),
            'invoices_this_month' => Invoice::where('institution_id', $institution->id)
                ->where('created_at', '>=', $startOfMonth)
                ->count(),
            'invoices_paid_this_month' => Invoice::where('institution_id', $institution->id)
                ->where('status', 'paid')
                ->where('paid_at', '>=', $startOfMonth)
                ->count(),
            'overdue_invoices' => Invoice::where('institution_id', $institution->id)
                ->overdue()
                ->count(),
        ];
    }

    public function getDailyRevenue(Institution $institution, ?string $date = null): array
    {
        $date = $date ?? now()->toDateString();

        $payments = Payment::where('institution_id', $institution->id)
            ->whereDate('payment_date', $date)
            ->where('status', 'completed')
            ->get();

        return [
            'date' => $date,
            'total_received' => $payments->sum('amount'),
            'payment_count' => $payments->count(),
            'by_method' => $payments->groupBy('method')
                ->map(fn ($m) => ['count' => $m->count(), 'total' => $m->sum('amount')])
                ->toArray(),
        ];
    }

    public function getMonthlyRevenue(Institution $institution, int $year, int $month): array
    {
        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        $dailyRevenue = Payment::where('institution_id', $institution->id)
            ->whereBetween('payment_date', [$startOfMonth, $endOfMonth])
            ->where('status', 'completed')
            ->selectRaw('DATE(payment_date) as date, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('date')
            ->get();

        $totalRevenue = $dailyRevenue->sum('total');

        return [
            'year' => $year,
            'month' => $month,
            'total_revenue' => $totalRevenue,
            'daily_breakdown' => $dailyRevenue,
            'average_daily' => $totalRevenue / $endOfMonth->day,
        ];
    }

    private function generateInvoiceNumber(int $institutionId): string
    {
        $prefix = 'INV';
        $year = now()->format('Y');
        $lastInvoice = Invoice::where('institution_id', $institutionId)
            ->whereYear('created_at', now()->year)
            ->orderByDesc('id')
            ->value('invoice_number');

        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice, -6);
            $newNumber = str_pad((string) ($lastNumber + 1), 6, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '000001';
        }

        return "{$prefix}{$year}{$newNumber}";
    }

    private function generatePaymentNumber(int $institutionId): string
    {
        $prefix = 'PAY';
        $year = now()->format('Y');
        $lastPayment = Payment::where('institution_id', $institutionId)
            ->whereYear('created_at', now()->year)
            ->orderByDesc('id')
            ->value('payment_number');

        if ($lastPayment) {
            $lastNumber = (int) substr($lastPayment, -6);
            $newNumber = str_pad((string) ($lastNumber + 1), 6, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '000001';
        }

        return "{$prefix}{$year}{$newNumber}";
    }
}
