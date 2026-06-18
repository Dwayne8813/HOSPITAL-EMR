<?php

declare(strict_types=1);

namespace App\DTOs;

readonly class LoginDTO
{
    public function __construct(
        public string $email,
        public string $password,
        public string $institutionSlug,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            email: $data['email'],
            password: $data['password'],
            institutionSlug: $data['institution_slug'],
        );
    }

    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'password' => $this->password,
            'institution_slug' => $this->institutionSlug,
        ];
    }
}
