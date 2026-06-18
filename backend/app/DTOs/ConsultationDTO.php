<?php

declare(strict_types=1);

namespace App\DTOs;

readonly class ConsultationDTO
{
    public function __construct(
        public int $id,
        public int $institutionId,
        public int $medicalRecordId,
        public int $patientId,
        public int $doctorId,
        public ?string $anamnesis = null,
        public ?string $physicalExamination = null,
        public ?string $assessment = null,
        public ?string $plan = null,
        public ?string $additionalNotes = null,
        public ?\DateTimeImmutable $consultationDate = null,
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
            patientId: $data['patient_id'],
            doctorId: $data['doctor_id'],
            anamnesis: $data['anamnesis'] ?? null,
            physicalExamination: $data['physical_examination'] ?? null,
            assessment: $data['assessment'] ?? null,
            plan: $data['plan'] ?? null,
            additionalNotes: $data['additional_notes'] ?? null,
            consultationDate: isset($data['consultation_date']) ? new \DateTimeImmutable($data['consultation_date']) : null,
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
            'patient_id' => $this->patientId,
            'doctor_id' => $this->doctorId,
            'anamnesis' => $this->anamnesis,
            'physical_examination' => $this->physicalExamination,
            'assessment' => $this->assessment,
            'plan' => $this->plan,
            'additional_notes' => $this->additionalNotes,
            'consultation_date' => $this->consultationDate?->format('Y-m-d H:i:s'),
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
        ];
    }
}
