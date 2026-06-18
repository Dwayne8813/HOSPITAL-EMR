<?php

namespace App\Repositories\Eloquent;

use App\Models\Prescription;
use App\Repositories\Interfaces\PrescriptionRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentPrescriptionRepository implements PrescriptionRepositoryInterface
{
    public function __construct(
        protected Prescription $model,
    ) {}

    public function findById(int $id): ?Prescription
    {
        return $this->model->with('patient', 'doctor', 'medicalRecord', 'institution')->find($id);
    }

    public function create(array $data): Prescription
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Prescription
    {
        $prescription = $this->model->findOrFail($id);
        $prescription->update($data);

        return $prescription;
    }

    public function listByPatient(int $patientId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->byPatient($patientId)
            ->with('doctor', 'items.medication')
            ->orderBy('prescribed_at', 'desc')
            ->paginate($perPage);
    }

    public function listByDoctor(int $doctorId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->where('doctor_id', $doctorId)
            ->with('patient', 'items.medication')
            ->orderBy('prescribed_at', 'desc')
            ->paginate($perPage);
    }

    public function listByMedicalRecord(int $medicalRecordId): LengthAwarePaginator
    {
        return $this->model
            ->where('medical_record_id', $medicalRecordId)
            ->with('items.medication')
            ->orderBy('prescribed_at', 'desc')
            ->paginate();
    }

    public function getWithItems(int $id): ?Prescription
    {
        return $this->model
            ->with(['patient', 'doctor', 'items.medication', 'items.medication.batches'])
            ->find($id);
    }

    public function getByDateRange(int $institutionId, string $from, string $to, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->byInstitution($institutionId)
            ->with('patient', 'doctor')
            ->whereBetween('prescribed_at', [$from, $to])
            ->orderBy('prescribed_at', 'desc')
            ->paginate($perPage);
    }

    public function countDispensed(int $institutionId, ?string $from = null, ?string $to = null): int
    {
        $query = $this->model
            ->byInstitution($institutionId)
            ->where('status', 'dispensed');

        if ($from && $to) {
            $query->whereBetween('prescribed_at', [$from, $to]);
        }

        return $query->count();
    }
}
