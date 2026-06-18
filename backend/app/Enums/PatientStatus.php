<?php

declare(strict_types=1);

namespace App\Enums;

enum PatientStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case DECEASED = 'deceased';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
            self::DECEASED => 'Deceased',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::ACTIVE => 'Patient is currently active in the system',
            self::INACTIVE => 'Patient record is inactive or archived',
            self::DECEASED => 'Patient is deceased',
        };
    }
}
