<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Enums\CivilStatus;
use App\Enums\Gender;
use App\Enums\PatientStatus;
use App\Enums\BloodType;

readonly class PatientDTO
{
    public function __construct(
        public int $id,
        public int $institutionId,
        public string $firstName,
        public string $lastName,
        public string $documentNumber,
        public string $documentType,
        public Gender $gender,
        public \DateTimeImmutable $dateOfBirth,
        public CivilStatus $civilStatus,
        public BloodType $bloodType,
        public string $phone,
        public ?string $email = null,
        public ?string $address = null,
        public ?string $city = null,
        public ?string $state = null,
        public ?string $zipCode = null,
        public ?string $country = null,
        public ?string $emergencyContactName = null,
        public ?string $emergencyContactPhone = null,
        public ?string $insuranceProvider = null,
        public ?string $insuranceNumber = null,
        public ?string $occupation = null,
        public ?string $photoUrl = null,
        public ?string $notes = null,
        public PatientStatus $status = PatientStatus::ACTIVE,
        public \DateTimeImmutable $createdAt = new \DateTimeImmutable(),
        public ?\DateTimeImmutable $updatedAt = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            institutionId: $data['institution_id'],
            firstName: $data['first_name'],
            lastName: $data['last_name'],
            documentNumber: $data['document_number'],
            documentType: $data['document_type'],
            gender: Gender::from($data['gender']),
            dateOfBirth: new \DateTimeImmutable($data['date_of_birth']),
            civilStatus: CivilStatus::from($data['civil_status']),
            bloodType: BloodType::from($data['blood_type']),
            phone: $data['phone'],
            email: $data['email'] ?? null,
            address: $data['address'] ?? null,
            city: $data['city'] ?? null,
            state: $data['state'] ?? null,
            zipCode: $data['zip_code'] ?? null,
            country: $data['country'] ?? null,
            emergencyContactName: $data['emergency_contact_name'] ?? null,
            emergencyContactPhone: $data['emergency_contact_phone'] ?? null,
            insuranceProvider: $data['insurance_provider'] ?? null,
            insuranceNumber: $data['insurance_number'] ?? null,
            occupation: $data['occupation'] ?? null,
            photoUrl: $data['photo_url'] ?? null,
            notes: $data['notes'] ?? null,
            status: PatientStatus::from($data['status'] ?? 'active'),
            createdAt: isset($data['created_at']) ? new \DateTimeImmutable($data['created_at']) : new \DateTimeImmutable(),
            updatedAt: isset($data['updated_at']) ? new \DateTimeImmutable($data['updated_at']) : null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'institution_id' => $this->institutionId,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'document_number' => $this->documentNumber,
            'document_type' => $this->documentType,
            'gender' => $this->gender->value,
            'date_of_birth' => $this->dateOfBirth->format('Y-m-d'),
            'civil_status' => $this->civilStatus->value,
            'blood_type' => $this->bloodType->value,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'zip_code' => $this->zipCode,
            'country' => $this->country,
            'emergency_contact_name' => $this->emergencyContactName,
            'emergency_contact_phone' => $this->emergencyContactPhone,
            'insurance_provider' => $this->insuranceProvider,
            'insurance_number' => $this->insuranceNumber,
            'occupation' => $this->occupation,
            'photo_url' => $this->photoUrl,
            'notes' => $this->notes,
            'status' => $this->status->value,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
        ];
    }
}
