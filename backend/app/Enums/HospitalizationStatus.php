<?php

declare(strict_types=1);

namespace App\Enums;

enum HospitalizationStatus: string
{
    case ACTIVE = 'active';
    case DISCHARGED = 'discharged';
    case TRANSFERRED = 'transferred';
    case DECEASED = 'deceased';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::DISCHARGED => 'Discharged',
            self::TRANSFERRED => 'Transferred',
            self::DECEASED => 'Deceased',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::ACTIVE => 'Patient is currently hospitalized',
            self::DISCHARGED => 'Patient has been discharged',
            self::TRANSFERRED => 'Patient has been transferred to another facility',
            self::DECEASED => 'Patient has deceased during hospitalization',
        };
    }
}
