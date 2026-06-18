<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Enums\MedicalRecordStatus;
use App\Enums\RecordType;

readonly class MedicalRecordDTO
{
    public function __construct(
        public int $id,
        public int $institutionId,
        public int $patientId,
        public int $doctorId,
        public ?int $appointmentId = null,
        public RecordType $type = RecordType::CONSULTATION,
        public MedicalRecordStatus $status = MedicalRecordStatus::DRAFT,
        public ?string $chiefComplaint = null,
        public ?string $notes = null,
        public ?string $observations = null,
        public ?\DateTimeImmutable $recordDate = null,
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
            appointmentId: $data['appointment_id'] ?? null,
            type: RecordType::from($data['type'] ?? 'consultation'),
            status: MedicalRecordStatus::from($data['status'] ?? 'draft'),
            chiefComplaint: $data['chief_complaint'] ?? null,
            notes: $data['notes'] ?? null,
            observations: $data['observations'] ?? null,
            recordDate: isset($data['record_date']) ? new \DateTimeImmutable($data['record_date']) : null,
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
            'appointment_id' => $this->appointmentId,
            'type' => $this->type->value,
            'status' => $this->status->value,
            'chief_complaint' => $this->chiefComplaint,
            'notes' => $this->notes,
            'observations' => $this->observations,
            'record_date' => $this->recordDate?->format('Y-m-d H:i:s'),
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
        ];
    }
}
