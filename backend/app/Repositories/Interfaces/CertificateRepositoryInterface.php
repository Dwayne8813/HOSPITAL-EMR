<?php

namespace App\Repositories\Interfaces;

use App\Models\Certificate;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CertificateRepositoryInterface
{
    public function findById(int $id): ?Certificate;

    public function create(array $data): Certificate;

    public function listByPatient(int $patientId, int $perPage = 15): LengthAwarePaginator;

    public function listByDoctor(int $doctorId, int $perPage = 15): LengthAwarePaginator;

    public function generatePdf(int $id): string;
}
