<?php

namespace App\Repositories\Interfaces;

use App\Models\SoapNote;
use Illuminate\Database\Eloquent\Collection;

interface SoapNoteRepositoryInterface
{
    public function findById(int $id): ?SoapNote;

    public function create(array $data): SoapNote;

    public function update(int $id, array $data): SoapNote;

    public function listByMedicalRecord(int $medicalRecordId): Collection;
}
