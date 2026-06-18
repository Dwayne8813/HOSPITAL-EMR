<?php

declare(strict_types=1);

namespace App\Enums;

enum Gender: string
{
    case MALE = 'M';
    case FEMALE = 'F';
    case OTHER = 'O';

    public function label(): string
    {
        return match ($this) {
            self::MALE => 'Male',
            self::FEMALE => 'Female',
            self::OTHER => 'Other',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::MALE => 'Male gender',
            self::FEMALE => 'Female gender',
            self::OTHER => 'Non-binary / Prefer not to say',
        };
    }
}
