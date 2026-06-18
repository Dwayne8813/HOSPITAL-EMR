<?php

declare(strict_types=1);

namespace App\Services\Pharmacy;

use App\DTOs\MedicationDTO;
use App\Models\Institution;
use App\Models\Medication;
use App\Models\MedicationBatch;
use App\Models\MedicationDispensation;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PharmacyService
{
    public function createMedication(array $data): Medication
    {
        return Medication::create([
            'institution_id' => auth()->user()->institution_id,
            'catalog_id' => $data['catalog_id'] ?? null,
            'category_id' => $data['category_id'] ?? null,
            'name' => $data['name'],
            'generic_name' => $data['generic_name'],
            'concentration' => $data['concentration'] ?? null,
            'pharmaceutical_form' => $data['pharmaceutical_form'] ?? null,
            'manufacturer' => $data['manufacturer'] ?? null,
            'requires_prescription' => $data['requires_prescription'] ?? false,
            'is_controlled' => $data['is_controlled'] ?? false,
            'minimum_stock' => $data['minimum_stock'] ?? 0,
            'current_stock' => $data['current_stock'] ?? 0,
            'unit_price' => $data['unit_price'] ?? 0,
            'cost_price' => $data['cost_price'] ?? 0,
            'is_active' => true,
        ]);
    }

    public function update(Medication $medication, array $data): Medication
    {
        $medication->update($data);
        return $medication->fresh();
    }

    public function listMedications(Request $request): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Medication::query()
            ->byInstitution(auth()->user()->institution_id)
            ->with(['catalog', 'category']);

        if ($request->has('search')) {
            $query->search($request->input('search'));
        }

        if ($request->has('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->has('requires_prescription')) {
            $query->where('requires_prescription', $request->boolean('requires_prescription'));
        }

        if ($request->has('is_controlled')) {
            $query->where('is_controlled', $request->boolean('is_controlled'));
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        return $query->orderBy($request->input('sort_by', 'name'), $request->input('sort_direction', 'asc'))
            ->paginate($request->input('per_page', 15));
    }

    public function searchMedications(string $term): \Illuminate\Support\Collection
    {
        return Medication::byInstitution(auth()->user()->institution_id)
            ->active()
            ->search($term)
            ->limit(20)
            ->get();
    }

    public function getLowStock(Institution $institution): \Illuminate\Database\Eloquent\Collection
    {
        return Medication::where('institution_id', $institution->id)
            ->lowStock()
            ->with(['catalog', 'category'])
            ->get();
    }

    public function getExpiringSoon(Institution $institution, int $days = 30): \Illuminate\Database\Eloquent\Collection
    {
        $expiryDate = now()->addDays($days);

        return MedicationBatch::where('institution_id', $institution->id)
            ->where('expiry_date', '<=', $expiryDate)
            ->where('expiry_date', '>=', now())
            ->where('status', 'active')
            ->where('quantity_remaining', '>', 0)
            ->with(['medication', 'supplier'])
            ->orderBy('expiry_date')
            ->get();
    }

    public function addBatch(Medication $medication, array $data): MedicationBatch
    {
        $batch = MedicationBatch::create([
            'medication_id' => $medication->id,
            'institution_id' => auth()->user()->institution_id,
            'batch_number' => $data['batch_number'],
            'quantity' => $data['quantity'],
            'quantity_remaining' => $data['quantity'],
            'expiry_date' => $data['expiry_date'],
            'manufacturing_date' => $data['manufacturing_date'] ?? null,
            'cost_price' => $data['cost_price'] ?? $medication->cost_price,
            'unit_price' => $data['unit_price'] ?? $medication->unit_price,
            'supplier_id' => $data['supplier_id'] ?? null,
            'status' => 'active',
        ]);

        $medication->increment('current_stock', $data['quantity']);

        return $batch;
    }

    public function dispense(int $prescriptionItemId, int $quantity, int $userId): MedicationDispensation
    {
        return DB::transaction(function () use ($prescriptionItemId, $quantity, $userId) {
            $prescriptionItem = \App\Models\PrescriptionItem::findOrFail($prescriptionItemId);
            $medication = $prescriptionItem->medication;

            $batches = MedicationBatch::where('medication_id', $medication->id)
                ->where('status', 'active')
                ->where('quantity_remaining', '>', 0)
                ->orderBy('expiry_date')
                ->get();

            $remainingToDispense = $quantity;
            $totalCost = 0;

            foreach ($batches as $batch) {
                if ($remainingToDispense <= 0) break;

                $dispenseFromBatch = min($remainingToDispense, $batch->quantity_remaining);

                $batch->decrement('quantity_remaining', $dispenseFromBatch);
                $remainingToDispense -= $dispenseFromBatch;
                $totalCost += $dispenseFromBatch * $batch->unit_price;

                if ($batch->quantity_remaining <= 0) {
                    $batch->update(['status' => 'depleted']);
                }
            }

            if ($remainingToDispense > 0) {
                throw new \App\Exceptions\InsufficientStockException(
                    "Insufficient stock for {$medication->name}. Missing {$remainingToDispense} units."
                );
            }

            $medication->decrement('current_stock', $quantity);

            return MedicationDispensation::create([
                'patient_id' => $prescriptionItem->prescription->patient_id,
                'medication_id' => $medication->id,
                'prescription_item_id' => $prescriptionItemId,
                'institution_id' => auth()->user()->institution_id,
                'quantity' => $quantity,
                'unit_cost' => $totalCost / $quantity,
                'total_cost' => $totalCost,
                'dispensed_by' => $userId,
                'dispensed_at' => now(),
            ]);
        });
    }

    public function createSupplier(array $data): Supplier
    {
        return Supplier::create([
            'institution_id' => auth()->user()->institution_id,
            'name' => $data['name'],
            'contact_person' => $data['contact_person'] ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'nit' => $data['nit'] ?? null,
            'status' => 'active',
        ]);
    }

    public function updateSupplier(Supplier $supplier, array $data): Supplier
    {
        $supplier->update($data);
        return $supplier->fresh();
    }

    public function listSuppliers(Institution $institution): \Illuminate\Database\Eloquent\Collection
    {
        return Supplier::where('institution_id', $institution->id)
            ->orderBy('name')
            ->get();
    }

    public function createPurchaseOrder(array $data): PurchaseOrder
    {
        return DB::transaction(function () use ($data) {
            $institutionId = auth()->user()->institution_id;
            $orderNumber = $this->generateOrderNumber($institutionId);

            $totalAmount = 0;
            foreach ($data['items'] as $item) {
                $totalAmount += $item['quantity'] * $item['unit_cost'];
            }

            $order = PurchaseOrder::create([
                'institution_id' => $institutionId,
                'supplier_id' => $data['supplier_id'],
                'order_number' => $orderNumber,
                'status' => 'pending',
                'total_amount' => $totalAmount,
                'expected_delivery' => $data['expected_delivery'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            foreach ($data['items'] as $item) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $order->id,
                    'medication_id' => $item['medication_id'],
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'total_cost' => $item['quantity'] * $item['unit_cost'],
                ]);
            }

            return $order->load(['items.medication', 'supplier']);
        });
    }

    public function receivePurchaseOrder(PurchaseOrder $order, array $items): PurchaseOrder
    {
        return DB::transaction(function () use ($order, $items) {
            $order->update([
                'status' => 'received',
                'received_at' => now(),
                'received_by' => auth()->id(),
            ]);

            foreach ($items as $itemData) {
                $medication = Medication::findOrFail($itemData['medication_id']);

                $batch = MedicationBatch::create([
                    'medication_id' => $medication->id,
                    'institution_id' => $order->institution_id,
                    'purchase_order_id' => $order->id,
                    'batch_number' => $itemData['batch_number'] ?? 'PO-' . $order->order_number,
                    'quantity' => $itemData['quantity_received'],
                    'quantity_remaining' => $itemData['quantity_received'],
                    'expiry_date' => $itemData['expiry_date'],
                    'cost_price' => $itemData['unit_cost'],
                    'supplier_id' => $order->supplier_id,
                    'status' => 'active',
                ]);

                $medication->increment('current_stock', $itemData['quantity_received']);
            }

            return $order->fresh()->load(['items.medication', 'supplier']);
        });
    }

    public function getDispensationHistory(int $patientId): \Illuminate\Database\Eloquent\Collection
    {
        return MedicationDispensation::where('patient_id', $patientId)
            ->with(['medication', 'prescriptionItem', 'dispenser'])
            ->orderByDesc('dispensed_at')
            ->get();
    }

    private function generateOrderNumber(int $institutionId): string
    {
        $prefix = 'PO';
        $year = now()->format('Y');
        $lastOrder = PurchaseOrder::where('institution_id', $institutionId)
            ->whereYear('created_at', now()->year)
            ->orderByDesc('id')
            ->value('order_number');

        if ($lastOrder) {
            $lastNumber = (int) substr($lastOrder, -6);
            $newNumber = str_pad((string) ($lastNumber + 1), 6, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '000001';
        }

        return "{$prefix}{$year}{$newNumber}";
    }
}
