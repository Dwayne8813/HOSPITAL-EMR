<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Enums\EmergencyStatus;
use App\Enums\TriageLevel;

readonly class EmergencyTriageDTO
{
    public function __construct(
        public int $id,
        public int $institutionId,
        public int $patientId,
        public ?int $doctorId = null,
        public TriageLevel $triageLevel = TriageLevel::LEVEL_V,
        public EmergencyStatus $status = EmergencyStatus::WAITING,
        public ?string $chiefComplaint = null,
        public ?string $arrivalMode = null,
        public ?string $notes = null,
        public ?int $assignedBedId = null,
        public ?\DateTimeImmutable $arrivalTime = null,
        public ?\DateTimeImmutable $triageTime = null,
        public ?\DateTimeImmutable $treatmentStartTime = null,
        public ?\DateTimeImmutable $dischargeTime = null,
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
            doctorId: $data['doctor_id'] ?? null,
            triageLevel: TriageLevel::from($data['triage_level'] ?? 'V'),
            status: EmergencyStatus::from($data['status'] ?? 'waiting'),
            chiefComplaint: $data['chief_complaint'] ?? null,
            arrivalMode: $data['arrival_mode'] ?? null,
            notes: $data['notes'] ?? null,
            assignedBedId: $data['assigned_bed_id'] ?? null,
            arrivalTime: isset($data['arrival_time']) ? new \DateTimeImmutable($data['arrival_time']) : null,
            triageTime: isset($data['triage_time']) ? new \DateTimeImmutable($data['triage_time']) : null,
            treatmentStartTime: isset($data['treatment_start_time']) ? new \DateTimeImmutable($data['treatment_start_time']) : null,
            dischargeTime: isset($data['discharge_time']) ? new \DateTimeImmutable($data['discharge_time']) : null,
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
            'triage_level' => $this->triageLevel->value,
            'status' => $this->status->value,
            'chief_complaint' => $this->chiefComplaint,
            'arrival_mode' => $this->arrivalMode,
            'notes' => $this->notes,
            'assigned_bed_id' => $this->assignedBedId,
            'arrival_time' => $this->arrivalTime?->format('Y-m-d H:i:s'),
            'triage_time' => $this->triageTime?->format('Y-m-d H:i:s'),
            'treatment_start_time' => $this->treatmentStartTime?->format('Y-m-d H:i:s'),
            'discharge_time' => $this->dischargeTime?->format('Y-m-d H:i:s'),
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
        ];
    }
}
