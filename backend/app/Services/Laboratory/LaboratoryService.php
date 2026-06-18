<?php

declare(strict_types=1);

namespace App\Services\Laboratory;

use App\DTOs\LabOrderDTO;
use App\Models\Institution;
use App\Models\LabOrder;
use App\Models\LabOrderItem;
use App\Models\LabResult;
use Illuminate\Http\Request;

class LaboratoryService
{
    public function createLabOrder(array $data): LabOrder
    {
        $institutionId = auth()->user()->institution_id;
        $orderNumber = $this->generateOrderNumber($institutionId);

        $order = LabOrder::create([
            'institution_id' => $institutionId,
            'medical_record_id' => $data['medical_record_id'],
            'patient_id' => $data['patient_id'],
            'doctor_id' => $data['doctor_id'],
            'order_number' => $orderNumber,
            'status' => 'pending',
            'priority' => $data['priority'] ?? 'normal',
            'clinical_reason' => $data['clinical_reason'] ?? null,
            'notes' => $data['notes'] ?? null,
            'ordered_at' => now(),
        ]);

        if (isset($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $item) {
                LabOrderItem::create([
                    'lab_order_id' => $order->id,
                    'institution_id' => $institutionId,
                    'test_name' => $item['test_name'],
                    'test_code' => $item['test_code'] ?? null,
                    'status' => 'pending',
                    'notes' => $item['notes'] ?? null,
                ]);
            }
        }

        return $order->load(['items', 'patient', 'doctor', 'medicalRecord']);
    }

    public function getLabOrder(int $id): LabOrder
    {
        return LabOrder::with([
            'items.results',
            'patient',
            'doctor',
            'medicalRecord',
            'specimen',
        ])->findOrFail($id);
    }

    public function listLabOrders(Request $request): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = LabOrder::query()
            ->byInstitution(auth()->user()->institution_id)
            ->with(['patient', 'doctor', 'items']);

        if ($request->has('patient_id')) {
            $query->byPatient($request->input('patient_id'));
        }

        if ($request->has('doctor_id')) {
            $query->where('doctor_id', $request->input('doctor_id'));
        }

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('priority')) {
            $query->where('priority', $request->input('priority'));
        }

        if ($request->has('date_from')) {
            $query->where('ordered_at', '>=', $request->input('date_from'));
        }

        if ($request->has('date_to')) {
            $query->where('ordered_at', '<=', $request->input('date_to'));
        }

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'LIKE', "%{$search}%")
                  ->orWhereHas('patient', fn ($pq) => $pq->search($search));
            });
        }

        return $query->orderByDesc('ordered_at')
            ->paginate($request->input('per_page', 15));
    }

    public function addResults(int $labOrderItemId, array $results): LabOrderItem
    {
        $item = LabOrderItem::findOrFail($labOrderItemId);

        LabResult::create([
            'lab_order_item_id' => $item->id,
            'institution_id' => $item->institution_id,
            'result_value' => $results['result_value'],
            'reference_range' => $results['reference_range'] ?? null,
            'unit' => $results['unit'] ?? null,
            'is_abnormal' => $results['is_abnormal'] ?? false,
            'notes' => $results['notes'] ?? null,
            'file_path' => $results['file_path'] ?? null,
            'recorded_by' => auth()->id(),
        ]);

        $item->update(['status' => 'completed']);

        $allItemsCompleted = LabOrderItem::where('lab_order_id', $item->lab_order_id)
            ->where('id', '!=', $item->id)
            ->where('status', '!=', 'completed')
            ->count() === 0;

        if ($allItemsCompleted) {
            $item->labOrder()->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        }

        return $item->fresh()->load(['results', 'labOrder']);
    }

    public function validateResults(int $labOrderItemId, int $userId): LabOrderItem
    {
        $item = LabOrderItem::findOrFail($labOrderItemId);

        $item->update([
            'status' => 'validated',
            'validated_by' => $userId,
            'validated_at' => now(),
        ]);

        return $item->fresh()->load(['results', 'labOrder']);
    }

    public function getResults(int $patientId): \Illuminate\Database\Eloquent\Collection
    {
        return LabOrder::where('patient_id', $patientId)
            ->with(['items.results', 'doctor'])
            ->orderByDesc('order_date')
            ->get();
    }

    public function getPendingOrders(Institution $institution): \Illuminate\Database\Eloquent\Collection
    {
        return LabOrder::where('institution_id', $institution->id)
            ->where('status', 'pending')
            ->with(['patient', 'doctor', 'items'])
            ->orderBy('order_date')
            ->get();
    }

    public function getStats(Institution $institution): array
    {
        $today = now()->toDateString();

        return [
            'pending_today' => LabOrder::where('institution_id', $institution->id)
                ->whereDate('ordered_at', $today)
                ->where('status', 'pending')
                ->count(),
            'completed_today' => LabOrder::where('institution_id', $institution->id)
                ->whereDate('completed_at', $today)
                ->where('status', 'completed')
                ->count(),
            'total_this_month' => LabOrder::where('institution_id', $institution->id)
                ->where('ordered_at', '>=', now()->startOfMonth())
                ->count(),
            'urgent_pending' => LabOrder::where('institution_id', $institution->id)
                ->where('status', 'pending')
                ->where('priority', 'urgent')
                ->count(),
        ];
    }

    private function generateOrderNumber(int $institutionId): string
    {
        $prefix = 'LAB';
        $year = now()->format('Y');
        $lastOrder = LabOrder::where('institution_id', $institutionId)
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
