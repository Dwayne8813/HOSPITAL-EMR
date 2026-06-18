<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Enums\SurgeryStatus;

readonly class SurgeryDTO
{
    public function __construct(
        public int $id,
        public int $institutionId,
        public int $patientId,
        public int $surgeonId,
        public SurgeryStatus $status = SurgeryStatus::SCHEDULED,
        public string $procedureName,
        public ?string $procedureCode = null,
        public ?string $description = null,
        public ?string $notes = null,
        public ?int $roomId = null,
        public ?\DateTimeImmutable $scheduledAt = null,
        public ?int $estimatedDurationMinutes = null,
        public ?\DateTimeImmutable $startedAt = null,
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
            surgeonId: $data['surgeon_id'],
            status: SurgeryStatus::from($data['status'] ?? 'scheduled'),
            procedureName: $data['procedure_name'],
            procedureCode: $data['procedure_code'] ?? null,
            description: $data['description'] ?? null,
            notes: $data['notes'] ?? null,
            roomId: $data['room_id'] ?? null,
            scheduledAt: isset($data['scheduled_at']) ? new \DateTimeImmutable($data['scheduled_at']) : null,
            estimatedDurationMinutes: $data['estimated_duration_minutes'] ?? null,
            startedAt: isset($data['started_at']) ? new \DateTimeImmutable($data['started_at']) : null,
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
            'surgeon_id' => $this->surgeonId,
            'status' => $this->status->value,
            'procedure_name' => $this->procedureName,
            'procedure_code' => $this->procedureCode,
            'description' => $this->description,
            'notes' => $this->notes,
            'room_id' => $this->roomId,
            'scheduled_at' => $this->scheduledAt?->format('Y-m-d H:i:s'),
            'estimated_duration_minutes' => $this->estimatedDurationMinutes,
            'started_at' => $this->startedAt?->format('Y-m-d H:i:s'),
            'completed_at' => $this->completedAt?->format('Y-m-d H:i:s'),
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
        ];
    }
}
