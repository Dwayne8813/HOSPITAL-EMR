<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Enums\UserRole;
use App\Enums\Gender;

readonly class UserDTO
{
    public function __construct(
        public int $id,
        public int $institutionId,
        public string $name,
        public string $email,
        public UserRole $role,
        public ?string $phone = null,
        public ?Gender $gender = null,
        public ?string $specialty = null,
        public ?string $licenseNumber = null,
        public ?string $avatarUrl = null,
        public bool $isActive = true,
        public bool $isEmailVerified = false,
        public ?\DateTimeImmutable $lastLoginAt = null,
        public ?\DateTimeImmutable $createdAt = null,
        public ?\DateTimeImmutable $updatedAt = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            institutionId: $data['institution_id'],
            name: $data['name'],
            email: $data['email'],
            role: UserRole::from($data['role']),
            phone: $data['phone'] ?? null,
            gender: isset($data['gender']) ? Gender::from($data['gender']) : null,
            specialty: $data['specialty'] ?? null,
            licenseNumber: $data['license_number'] ?? null,
            avatarUrl: $data['avatar_url'] ?? null,
            isActive: $data['is_active'] ?? true,
            isEmailVerified: $data['is_email_verified'] ?? false,
            lastLoginAt: isset($data['last_login_at']) ? new \DateTimeImmutable($data['last_login_at']) : null,
            createdAt: isset($data['created_at']) ? new \DateTimeImmutable($data['created_at']) : null,
            updatedAt: isset($data['updated_at']) ? new \DateTimeImmutable($data['updated_at']) : null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'institution_id' => $this->institutionId,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role->value,
            'phone' => $this->phone,
            'gender' => $this->gender?->value,
            'specialty' => $this->specialty,
            'license_number' => $this->licenseNumber,
            'avatar_url' => $this->avatarUrl,
            'is_active' => $this->isActive,
            'is_email_verified' => $this->isEmailVerified,
            'last_login_at' => $this->lastLoginAt?->format('Y-m-d H:i:s'),
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
        ];
    }
}
