<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Enums\LabOrderStatus;

readonly class LabOrderDTO
{
    public function __construct(
        public int $id,
        public int $institutionId,
        public int $patientId,
        public int $doctorId,
        public int $medicalRecordId,
        public LabOrderStatus $status = LabOrderStatus::PENDING,
        public ?string $priority = null,
        public ?string $clinicalIndication = null,
        public ?string $notes = null,
        public ?\DateTimeImmutable $orderedAt = null,
        public ?\DateTimeImmutable $completedAt = null,
        public ?\DateTimeImmutable $createdAt = null,
        public ?\DateTimeImmutable $updatedAt = null,
        /** @var array<int, array{id: int, name: string, status: string, result: ?string}> */
        public array $items = [],
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            institutionId: $data['institution_id'],
            patientId: $data['patient_id'],
            doctorId: $data['doctor_id'],
            medicalRecordId: $data['medical_record_id'],
            status: LabOrderStatus::from($data['status'] ?? 'pending'),
            priority: $data['priority'] ?? null,
            clinicalIndication: $data['clinical_indication'] ?? null,
            notes: $data['notes'] ?? null,
            orderedAt: isset($data['ordered_at']) ? new \DateTimeImmutable($data['ordered_at']) : null,
            completedAt: isset($data['completed_at']) ? new \DateTimeImmutable($data['completed_at']) : null,
            createdAt: isset($data['created_at']) ? new \DateTimeImmutable($data['created_at']) : null,
            updatedAt: isset($data['updated_at']) ? new \DateTimeImmutable($data['updated_at']) : null,
            items: $data['items'] ?? [],
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
            'status' => $this->status->value,
            'priority' => $this->priority,
            'clinical_indication' => $this->clinicalIndication,
            'notes' => $this->notes,
            'ordered_at' => $this->orderedAt?->format('Y-m-d H:i:s'),
            'completed_at' => $this->completedAt?->format('Y-m-d H:i:s'),
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'items' => $this->items,
        ];
    }
}
