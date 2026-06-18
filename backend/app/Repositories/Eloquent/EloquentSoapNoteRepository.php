<?php

namespace App\Repositories\Eloquent;

use App\Models\SoapNote;
use App\Repositories\Interfaces\SoapNoteRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class EloquentSoapNoteRepository implements SoapNoteRepositoryInterface
{
    public function __construct(
        protected SoapNote $model,
    ) {}

    public function findById(int $id): ?SoapNote
    {
        return $this->model->with('medicalRecord')->find($id);
    }

    public function create(array $data): SoapNote
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): SoapNote
    {
        $soapNote = $this->model->findOrFail($id);
        $soapNote->update($data);

        return $soapNote;
    }

    public function listByMedicalRecord(int $medicalRecordId): Collection
    {
        return $this->model
            ->where('medical_record_id', $medicalRecordId)
            ->get();
    }
}
