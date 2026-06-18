<?php

namespace App\Repositories\Eloquent;

use App\Models\Patient;
use App\Repositories\Interfaces\PatientRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EloquentPatientRepository implements PatientRepositoryInterface
{
    public function __construct(
        protected Patient $model,
    ) {}

    public function findById(int $id): ?Patient
    {
        return $this->model->with('city', 'department', 'doctor')->find($id);
    }

    public function findByCI(string $documentNumber): ?Patient
    {
        return $this->model->where('document_number', $documentNumber)->first();
    }

    public function findByMedicalRecord(string $medicalRecordNumber): ?Patient
    {
        return $this->model->where('medical_record_number', $medicalRecordNumber)->first();
    }

    public function create(array $data): Patient
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Patient
    {
        $patient = $this->model->findOrFail($id);
        $patient->update($data);

        return $patient->fresh(['city', 'department', 'doctor']);
    }

    public function delete(int $id): bool
    {
        $patient = $this->model->findOrFail($id);

        return $patient->delete();
    }

    public function list(int $institutionId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->byInstitution($institutionId)->with('city', 'doctor');

        if (!empty($filters['gender'])) {
            $query->where('gender', $filters['gender']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (!empty($filters['blood_type'])) {
            $query->where('blood_type', $filters['blood_type']);
        }

        return $query->orderBy('last_name')->orderBy('first_name')->paginate($perPage);
    }

    public function search(int $institutionId, string $query, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->byInstitution($institutionId)
            ->with('city', 'doctor')
            ->search($query)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate($perPage);
    }

    public function getByDoctor(int $doctorId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->where('doctor_id', $doctorId)
            ->with('city')
            ->orderBy('last_name')
            ->paginate($perPage);
    }

    public function countByInstitution(int $institutionId): int
    {
        return $this->model->byInstitution($institutionId)->count();
    }

    public function getRecentPatients(int $institutionId, int $limit = 10): Collection
    {
        return $this->model
            ->byInstitution($institutionId)
            ->with('doctor')
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function getPatientWithDetails(int $id): ?Patient
    {
        return $this->model
            ->with([
                'city',
                'department',
                'doctor',
                'contacts',
                'allergies',
                'insurances',
                'medicalRecords' => fn ($q) => $q->latest()->limit(5),
            ])
            ->find($id);
    }
}
