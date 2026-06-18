<?php

namespace App\Repositories\Eloquent;

use App\Models\Certificate;
use App\Repositories\Interfaces\CertificateRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentCertificateRepository implements CertificateRepositoryInterface
{
    public function __construct(
        protected Certificate $model,
    ) {}

    public function findById(int $id): ?Certificate
    {
        return $this->model->with('institution', 'medicalRecord', 'patient', 'doctor')->find($id);
    }

    public function create(array $data): Certificate
    {
        return $this->model->create($data);
    }

    public function listByPatient(int $patientId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->where('patient_id', $patientId)
            ->with('doctor', 'medicalRecord')
            ->orderBy('issued_at', 'desc')
            ->paginate($perPage);
    }

    public function listByDoctor(int $doctorId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->where('doctor_id', $doctorId)
            ->with('patient', 'medicalRecord')
            ->orderBy('issued_at', 'desc')
            ->paginate($perPage);
    }

    public function generatePdf(int $id): string
    {
        $certificate = $this->model->with('institution', 'patient', 'doctor', 'medicalRecord')->findOrFail($id);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('certificates.pdf', [
            'certificate' => $certificate,
        ]);

        $filename = "certificate_{$certificate->certificate_number}.pdf";
        $path = storage_path("app/certificates/{$filename}");

        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $pdf->save($path);

        return $path;
    }
}
