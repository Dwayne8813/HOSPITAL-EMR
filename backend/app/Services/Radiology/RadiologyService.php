<?php

declare(strict_types=1);

namespace App\Services\Radiology;

use App\DTOs\ImagingOrderDTO;
use App\Models\ImagingOrder;
use App\Models\ImagingResult;
use App\Models\Institution;
use App\Models\RadiologyTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RadiologyService
{
    public function createImagingOrder(array $data): ImagingOrder
    {
        $institutionId = auth()->user()->institution_id;
        $orderNumber = $this->generateOrderNumber($institutionId);

        return ImagingOrder::create([
            'institution_id' => $institutionId,
            'medical_record_id' => $data['medical_record_id'] ?? null,
            'patient_id' => $data['patient_id'],
            'doctor_id' => $data['doctor_id'],
            'order_number' => $orderNumber,
            'imaging_type' => $data['imaging_type'],
            'body_region' => $data['body_region'],
            'clinical_indication' => $data['clinical_indication'] ?? null,
            'priority' => $data['priority'] ?? 'normal',
            'status' => 'ordered',
            'notes' => $data['notes'] ?? null,
            'ordered_at' => now(),
        ]);
    }

    public function getImagingOrder(int $id): ImagingOrder
    {
        return ImagingOrder::with([
            'results',
            'patient',
            'doctor',
            'medicalRecord',
        ])->findOrFail($id);
    }

    public function listImagingOrders(Request $request): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = ImagingOrder::query()
            ->byInstitution(auth()->user()->institution_id)
            ->with(['patient', 'doctor']);

        if ($request->has('patient_id')) {
            $query->where('patient_id', $request->input('patient_id'));
        }

        if ($request->has('doctor_id')) {
            $query->where('doctor_id', $request->input('doctor_id'));
        }

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('imaging_type')) {
            $query->where('imaging_type', $request->input('imaging_type'));
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

    public function addResults(int $imagingOrderId, array $data): ImagingResult
    {
        $order = ImagingOrder::findOrFail($imagingOrderId);

        $resultData = [
            'imaging_order_id' => $order->id,
            'institution_id' => $order->institution_id,
            'findings' => $data['findings'],
            'impression' => $data['impression'] ?? null,
            'recommendations' => $data['recommendations'] ?? null,
            'performed_by' => $data['performed_by'] ?? auth()->id(),
            'performed_at' => $data['performed_at'] ?? now(),
        ];

        if (isset($data['file'])) {
            $path = $data['file']->store('imaging-results/' . $order->id, 'public');
            $resultData['file_path'] = $path;
            $resultData['file_name'] = $data['file']->getClientOriginalName();
        }

        if (isset($data['dicom_file'])) {
            $path = $data['dicom_file']->store('imaging-dicom/' . $order->id, 'public');
            $resultData['dicom_file_path'] = $path;
        }

        $result = ImagingResult::create($resultData);

        $order->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return $result->load(['imagingOrder']);
    }

    public function getTemplates(Institution $institution): \Illuminate\Database\Eloquent\Collection
    {
        return RadiologyTemplate::where('institution_id', $institution->id)
            ->orderBy('name')
            ->get();
    }

    public function createTemplate(array $data): RadiologyTemplate
    {
        return RadiologyTemplate::create([
            'institution_id' => auth()->user()->institution_id,
            'name' => $data['name'],
            'imaging_type' => $data['imaging_type'],
            'body_region' => $data['body_region'] ?? null,
            'template_content' => $data['template_content'],
            'created_by' => auth()->id(),
        ]);
    }

    private function generateOrderNumber(int $institutionId): string
    {
        $prefix = 'RAD';
        $year = now()->format('Y');
        $lastOrder = ImagingOrder::where('institution_id', $institutionId)
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
