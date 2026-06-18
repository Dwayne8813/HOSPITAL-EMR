<?php

declare(strict_types=1);

namespace App\Enums;

enum MedicationStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case DISCONTINUED = 'discontinued';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
            self::DISCONTINUED => 'Discontinued',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::ACTIVE => 'Medication is currently active and available',
            self::INACTIVE => 'Medication is temporarily unavailable',
            self::DISCONTINUED => 'Medication has been permanently discontinued',
        };
    }
}
