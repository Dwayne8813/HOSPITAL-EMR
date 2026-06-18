<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'invoice_id' => ['required', 'integer', 'exists:invoices,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'string', 'in:cash,card,transfer,insurance,mixed'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
            'received_at' => ['nullable', 'date'],
        ]);

        try {
            $invoice = Invoice::findOrFail($request->input('invoice_id'));
            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'institution_id' => auth()->user()->institution_id,
                'received_by' => auth()->id(),
                'amount' => $request->input('amount'),
                'payment_method' => $request->input('payment_method'),
                'reference_number' => $request->input('reference_number'),
                'received_at' => $request->input('received_at', now()),
                'notes' => $request->input('notes'),
                'status' => 'completed',
            ]);

            $paidTotal = $invoice->payments()->where('status', 'completed')->sum('amount');
            if ($paidTotal >= $invoice->total_amount) {
                $invoice->update(['status' => 'paid', 'paid_at' => now()]);
            } else {
                $invoice->update([
                    'paid_amount' => $paidTotal,
                    'balance' => $invoice->total_amount - $paidTotal,
                    'status' => 'partially_paid',
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $payment,
                'message' => 'Payment processed successfully.',
            ], 201);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Invoice not found.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to process payment.',
            ], 500);
        }
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $query = Payment::where('institution_id', auth()->user()->institution_id)
                ->with(['invoice', 'receivedBy']);

            if ($request->has('invoice_id')) {
                $query->where('invoice_id', $request->input('invoice_id'));
            }

            if ($request->has('method')) {
                $query->where('payment_method', $request->input('method'));
            }

            if ($request->has('date_from')) {
                $query->where('received_at', '>=', $request->input('date_from'));
            }

            if ($request->has('date_to')) {
                $query->where('received_at', '<=', $request->input('date_to'));
            }

            $payments = $query->orderByDesc('received_at')
                ->paginate($request->input('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $payments->items(),
                'message' => 'Payments retrieved successfully.',
                'meta' => [
                    'current_page' => $payments->currentPage(),
                    'last_page' => $payments->lastPage(),
                    'per_page' => $payments->perPage(),
                    'total' => $payments->total(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to retrieve payments.',
            ], 500);
        }
    }
}
