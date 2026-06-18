<?php

declare(strict_types=1);

namespace App\DTOs;

readonly class PrescriptionItemDTO
{
    public function __construct(
        public int $id,
        public int $prescriptionId,
        public int $medicationId,
        public string $medicationName,
        public string $dosage,
        public string $frequency,
        public string $duration,
        public ?string $route = null,
        public ?int $quantity = null,
        public ?bool $refillable = null,
        public ?int $refillCount = null,
        public ?string $instructions = null,
        public ?\DateTimeImmutable $createdAt = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            prescriptionId: $data['prescription_id'],
            medicationId: $data['medication_id'],
            medicationName: $data['medication_name'],
            dosage: $data['dosage'],
            frequency: $data['frequency'],
            duration: $data['duration'],
            route: $data['route'] ?? null,
            quantity: $data['quantity'] ?? null,
            refillable: $data['refillable'] ?? null,
            refillCount: $data['refill_count'] ?? null,
            instructions: $data['instructions'] ?? null,
            createdAt: isset($data['created_at']) ? new \DateTimeImmutable($data['created_at']) : null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'prescription_id' => $this->prescriptionId,
            'medication_id' => $this->medicationId,
            'medication_name' => $this->medicationName,
            'dosage' => $this->dosage,
            'frequency' => $this->frequency,
            'duration' => $this->duration,
            'route' => $this->route,
            'quantity' => $this->quantity,
            'refillable' => $this->refillable,
            'refill_count' => $this->refillCount,
            'instructions' => $this->instructions,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
        ];
    }
}
