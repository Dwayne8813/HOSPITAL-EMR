<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Invoice::with(['patient', 'payments'])
                ->where('institution_id', auth()->user()->institution_id);

            if ($request->has('payment_status')) {
                $query->where('payment_status', $request->input('payment_status'));
            }

            $perPage = $request->input('per_page', 15);
            $result = $query->orderByDesc('created_at')->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $result->items(),
                'message' => 'Invoices retrieved successfully.',
                'meta' => [
                    'current_page' => $result->currentPage(),
                    'last_page' => $result->lastPage(),
                    'per_page' => $result->perPage(),
                    'total' => $result->total(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to retrieve invoices.',
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'notes' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.service_catalog_id' => ['nullable', 'integer'],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.discount' => ['nullable', 'numeric', 'min:0'],
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $institutionId = auth()->user()->institution_id;
                $lastInvoice = Invoice::where('institution_id', $institutionId)->max('id') ?? 0;
                $invoiceNumber = 'FAC-' . str_pad((string)($lastInvoice + 1), 6, '0', STR_PAD_LEFT);

                $subtotal = 0;
                $discountAmount = 0;

                foreach ($request->items as $item) {
                    $itemTotal = $item['quantity'] * $item['unit_price'];
                    $itemDiscount = $item['discount'] ?? 0;
                    $subtotal += $itemTotal;
                    $discountAmount += $itemDiscount;
                }

                $totalAmount = $subtotal - $discountAmount;

                $invoice = Invoice::create([
                    'institution_id' => $institutionId,
                    'patient_id' => $request->patient_id,
                    'created_by' => auth()->id(),
                    'invoice_number' => $invoiceNumber,
                    'invoice_date' => now()->toDateString(),
                    'due_date' => $request->due_date ?? now()->addDays(30)->toDateString(),
                    'subtotal' => $subtotal,
                    'tax_amount' => 0,
                    'discount' => $discountAmount,
                    'total_amount' => $totalAmount,
                    'payment_status' => 'pending',
                    'notes' => $request->notes,
                ]);

                foreach ($request->items as $item) {
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'service_catalog_id' => $item['service_catalog_id'] ?? null,
                        'description' => $item['description'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'discount' => $item['discount'] ?? 0,
                        'subtotal' => ($item['quantity'] * $item['unit_price']) - ($item['discount'] ?? 0),
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'data' => $invoice->load(['items', 'patient']),
                    'message' => 'Invoice created successfully.',
                ], 201);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to create invoice: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $invoice = Invoice::with(['items', 'patient', 'payments'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $invoice,
                'message' => 'Invoice retrieved successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Invoice not found.',
            ], 404);
        }
    }
}
