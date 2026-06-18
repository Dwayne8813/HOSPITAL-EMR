<?php

declare(strict_types=1);

namespace App\DTOs;

readonly class VitalSignDTO
{
    public function __construct(
        public int $id,
        public int $institutionId,
        public int $medicalRecordId,
        public int $patientId,
        public ?float $temperature = null,
        public ?string $bloodPressureSystolic = null,
        public ?string $bloodPressureDiastolic = null,
        public ?int $heartRate = null,
        public ?int $respiratoryRate = null,
        public ?float $oxygenSaturation = null,
        public ?float $weight = null,
        public ?float $height = null,
        public ?float $bmi = null,
        public ?string $notes = null,
        public ?int $recordedBy = null,
        public ?\DateTimeImmutable $recordedAt = null,
        public ?\DateTimeImmutable $createdAt = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            institutionId: $data['institution_id'],
            medicalRecordId: $data['medical_record_id'],
            patientId: $data['patient_id'],
            temperature: $data['temperature'] ?? null,
            bloodPressureSystolic: $data['blood_pressure_systolic'] ?? null,
            bloodPressureDiastolic: $data['blood_pressure_diastolic'] ?? null,
            heartRate: $data['heart_rate'] ?? null,
            respiratoryRate: $data['respiratory_rate'] ?? null,
            oxygenSaturation: $data['oxygen_saturation'] ?? null,
            weight: $data['weight'] ?? null,
            height: $data['height'] ?? null,
            bmi: $data['bmi'] ?? null,
            notes: $data['notes'] ?? null,
            recordedBy: $data['recorded_by'] ?? null,
            recordedAt: isset($data['recorded_at']) ? new \DateTimeImmutable($data['recorded_at']) : null,
            createdAt: isset($data['created_at']) ? new \DateTimeImmutable($data['created_at']) : null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'institution_id' => $this->institutionId,
            'medical_record_id' => $this->medicalRecordId,
            'patient_id' => $this->patientId,
            'temperature' => $this->temperature,
            'blood_pressure_systolic' => $this->bloodPressureSystolic,
            'blood_pressure_diastolic' => $this->bloodPressureDiastolic,
            'heart_rate' => $this->heartRate,
            'respiratory_rate' => $this->respiratoryRate,
            'oxygen_saturation' => $this->oxygenSaturation,
            'weight' => $this->weight,
            'height' => $this->height,
            'bmi' => $this->bmi,
            'notes' => $this->notes,
            'recorded_by' => $this->recordedBy,
            'recorded_at' => $this->recordedAt?->format('Y-m-d H:i:s'),
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
        ];
    }
}
