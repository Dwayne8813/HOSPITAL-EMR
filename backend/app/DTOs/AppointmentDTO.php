<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Enums\AppointmentStatus;

readonly class AppointmentDTO
{
    public function __construct(
        public int $id,
        public int $institutionId,
        public int $patientId,
        public int $doctorId,
        public AppointmentStatus $status = AppointmentStatus::SCHEDULED,
        public ?int $serviceId = null,
        public ?string $reason = null,
        public ?string $notes = null,
        public \DateTimeImmutable $scheduledAt,
        public ?int $durationMinutes = null,
        public ?string $room = null,
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
            status: AppointmentStatus::from($data['status'] ?? 'scheduled'),
            serviceId: $data['service_id'] ?? null,
            reason: $data['reason'] ?? null,
            notes: $data['notes'] ?? null,
            scheduledAt: new \DateTimeImmutable($data['scheduled_at']),
            durationMinutes: $data['duration_minutes'] ?? null,
            room: $data['room'] ?? null,
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
            'status' => $this->status->value,
            'service_id' => $this->serviceId,
            'reason' => $this->reason,
            'notes' => $this->notes,
            'scheduled_at' => $this->scheduledAt->format('Y-m-d H:i:s'),
            'duration_minutes' => $this->durationMinutes,
            'room' => $this->room,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
        ];
    }
}
