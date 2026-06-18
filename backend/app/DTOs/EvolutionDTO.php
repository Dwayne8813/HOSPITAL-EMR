<?php

declare(strict_types=1);

namespace App\DTOs;

readonly class EvolutionDTO
{
    public function __construct(
        public int $id,
        public int $institutionId,
        public int $medicalRecordId,
        public ?int $doctorId = null,
        public ?string $evolutionType = null,
        public ?string $subjective = null,
        public ?string $objective = null,
        public ?string $assessment = null,
        public ?string $plan = null,
        public ?string $notes = null,
        public ?\DateTimeImmutable $evolutionDate = null,
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
            doctorId: $data['doctor_id'] ?? null,
            evolutionType: $data['evolution_type'] ?? null,
            subjective: $data['subjective'] ?? null,
            objective: $data['objective'] ?? null,
            assessment: $data['assessment'] ?? null,
            plan: $data['plan'] ?? null,
            notes: $data['notes'] ?? null,
            evolutionDate: isset($data['evolution_date']) ? new \DateTimeImmutable($data['evolution_date']) : null,
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
            'doctor_id' => $this->doctorId,
            'evolution_type' => $this->evolutionType,
            'subjective' => $this->subjective,
            'objective' => $this->objective,
            'assessment' => $this->assessment,
            'plan' => $this->plan,
            'notes' => $this->notes,
            'evolution_date' => $this->evolutionDate?->format('Y-m-d H:i:s'),
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
        ];
    }
}
