<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Enums\HospitalizationStatus;

readonly class HospitalizationDTO
{
    public function __construct(
        public int $id,
        public int $institutionId,
        public int $patientId,
        public int $doctorId,
        public ?int $roomId = null,
        public ?int $bedId = null,
        public HospitalizationStatus $status = HospitalizationStatus::ACTIVE,
        public ?string $admissionReason = null,
        public ?string $diagnosis = null,
        public ?string $notes = null,
        public ?\DateTimeImmutable $admissionDate = null,
        public ?\DateTimeImmutable $dischargeDate = null,
        public ?string $dischargeSummary = null,
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
            roomId: $data['room_id'] ?? null,
            bedId: $data['bed_id'] ?? null,
            status: HospitalizationStatus::from($data['status'] ?? 'active'),
            admissionReason: $data['admission_reason'] ?? null,
            diagnosis: $data['diagnosis'] ?? null,
            notes: $data['notes'] ?? null,
            admissionDate: isset($data['admission_date']) ? new \DateTimeImmutable($data['admission_date']) : null,
            dischargeDate: isset($data['discharge_date']) ? new \DateTimeImmutable($data['discharge_date']) : null,
            dischargeSummary: $data['discharge_summary'] ?? null,
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
            'room_id' => $this->roomId,
            'bed_id' => $this->bedId,
            'status' => $this->status->value,
            'admission_reason' => $this->admissionReason,
            'diagnosis' => $this->diagnosis,
            'notes' => $this->notes,
            'admission_date' => $this->admissionDate?->format('Y-m-d H:i:s'),
            'discharge_date' => $this->dischargeDate?->format('Y-m-d H:i:s'),
            'discharge_summary' => $this->dischargeSummary,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
        ];
    }
}
