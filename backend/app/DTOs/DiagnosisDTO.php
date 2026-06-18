<?php

declare(strict_types=1);

namespace App\DTOs;

readonly class DiagnosisDTO
{
    public function __construct(
        public int $id,
        public int $institutionId,
        public int $medicalRecordId,
        public int $patientId,
        public ?int $cie10CodeId = null,
        public ?string $diagnosisType = null,
        public string $description,
        public ?string $notes = null,
        public ?int $diagnosedBy = null,
        public ?\DateTimeImmutable $diagnosisDate = null,
        public ?\DateTimeImmutable $createdAt = null,
        public ?\DateTimeImmutable $updatedAt = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            institutionId: $data['institution_id'],
            medicalRecordId: $data['medical_record_id'],
            patientId: $data['patient_id'],
            cie10CodeId: $data['cie10_code_id'] ?? null,
            diagnosisType: $data['diagnosis_type'] ?? null,
            description: $data['description'],
            notes: $data['notes'] ?? null,
            diagnosedBy: $data['diagnosed_by'] ?? null,
            diagnosisDate: isset($data['diagnosis_date']) ? new \DateTimeImmutable($data['diagnosis_date']) : null,
            createdAt: isset($data['created_at']) ? new \DateTimeImmutable($data['created_at']) : null,
            updatedAt: isset($data['updated_at']) ? new \DateTimeImmutable($data['updated_at']) : null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'institution_id' => $this->institutionId,
            'medical_record_id' => $this->medicalRecordId,
            'patient_id' => $this->patientId,
            'cie10_code_id' => $this->cie10CodeId,
            'diagnosis_type' => $this->diagnosisType,
            'description' => $this->description,
            'notes' => $this->notes,
            'diagnosed_by' => $this->diagnosedBy,
            'diagnosis_date' => $this->diagnosisDate?->format('Y-m-d'),
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
        ];
    }
}
