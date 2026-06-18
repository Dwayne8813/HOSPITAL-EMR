<?php

namespace App\Repositories\Interfaces;

use App\Models\Appointment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface AppointmentRepositoryInterface
{
    public function findById(int $id): ?Appointment;

    public function create(array $data): Appointment;

    public function update(int $id, array $data): Appointment;

    public function delete(int $id): bool;

    public function listByDoctor(int $doctorId, array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function listByPatient(int $patientId, int $perPage = 15): LengthAwarePaginator;

    public function listByInstitution(int $institutionId, array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function listByDate(int $institutionId, string $date, int $perPage = 15): LengthAwarePaginator;

    public function confirm(int $id): Appointment;

    public function cancel(int $id, ?string $reason = null): Appointment;

    public function getAvailableSlots(int $doctorId, string $date): Collection;

    public function getUpcoming(int $institutionId, int $limit = 10): Collection;

    public function getByDateRange(int $institutionId, string $from, string $to, int $perPage = 15): LengthAwarePaginator;

    public function countByStatus(int $institutionId, ?string $date = null): array;
}
