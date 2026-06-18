<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Enums\PrescriptionStatus;

readonly class PrescriptionDTO
{
    public function __construct(
        public int $id,
        public int $institutionId,
        public int $patientId,
        public int $doctorId,
        public int $medicalRecordId,
        public PrescriptionStatus $status = PrescriptionStatus::DRAFT,
        public ?string $notes = null,
        public ?\DateTimeImmutable $prescriptionDate = null,
        public ?\DateTimeImmutable $createdAt = null,
        public ?\DateTimeImmutable $updatedAt = null,
        /** @var PrescriptionItemDTO[] */
        public array $items = [],
    ) {
    }

    public static function fromArray(array $data): self
    {
        $items = [];
        if (isset($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $item) {
                $items[] = PrescriptionItemDTO::fromArray($item);
            }
        }

        return new self(
            id: $data['id'],
            institutionId: $data['institution_id'],
            patientId: $data['patient_id'],
            doctorId: $data['doctor_id'],
            medicalRecordId: $data['medical_record_id'],
            status: PrescriptionStatus::from($data['status'] ?? 'draft'),
            notes: $data['notes'] ?? null,
            prescriptionDate: isset($data['prescription_date']) ? new \DateTimeImmutable($data['prescription_date']) : null,
            createdAt: isset($data['created_at']) ? new \DateTimeImmutable($data['created_at']) : null,
            updatedAt: isset($data['updated_at']) ? new \DateTimeImmutable($data['updated_at']) : null,
            items: $items,
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
            'notes' => $this->notes,
            'prescription_date' => $this->prescriptionDate?->format('Y-m-d H:i:s'),
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'items' => array_map(fn(PrescriptionItemDTO $item) => $item->toArray(), $this->items),
        ];
    }
}
