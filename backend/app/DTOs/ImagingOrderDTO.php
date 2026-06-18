<?php

declare(strict_types=1);

namespace App\DTOs;

readonly class ImagingOrderDTO
{
    public function __construct(
        public int $id,
        public int $institutionId,
        public int $patientId,
        public int $doctorId,
        public ?int $medicalRecordId = null,
        public string $imagingType,
        public string $bodyRegion,
        public ?string $clinicalIndication = null,
        public ?string $priority = null,
        public ?string $notes = null,
        public ?\DateTimeImmutable $orderedAt = null,
        public ?\DateTimeImmutable $completedAt = null,
        public ?\DateTimeImmutable $createdAt = null,
        public ?\DateTimeImmutable $updatedAt = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            institutionId: $data['institution_id'],
            patientId: $data['patient_id'],
            doctorId: $data['doctor_id'],
            medicalRecordId: $data['medical_record_id'] ?? null,
            imagingType: $data['imaging_type'],
            bodyRegion: $data['body_region'],
            clinicalIndication: $data['clinical_indication'] ?? null,
            priority: $data['priority'] ?? null,
            notes: $data['notes'] ?? null,
            orderedAt: isset($data['ordered_at']) ? new \DateTimeImmutable($data['ordered_at']) : null,
            completedAt: isset($data['completed_at']) ? new \DateTimeImmutable($data['completed_at']) : null,
            createdAt: isset($data['created_at']) ? new \DateTimeImmutable($data['created_at']) : null,
            updatedAt: isset($data['updated_at']) ? new \DateTimeImmutable($data['updated_at']) : null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'institution_id' => $this->institutionId,
            'patient_id' => $this->patientId,
            'doctor_id' => $this->doctorId,
            'medical_record_id' => $this->medicalRecordId,
            'imaging_type' => $this->imagingType,
            'body_region' => $this->bodyRegion,
            'clinical_indication' => $this->clinicalIndication,
            'priority' => $this->priority,
            'notes' => $this->notes,
            'ordered_at' => $this->orderedAt?->format('Y-m-d H:i:s'),
            'completed_at' => $this->completedAt?->format('Y-m-d H:i:s'),
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
        ];
    }
}
