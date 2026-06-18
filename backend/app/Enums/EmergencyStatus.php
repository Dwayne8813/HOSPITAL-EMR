<?php

declare(strict_types=1);

namespace App\Enums;

enum EmergencyStatus: string
{
    case WAITING = 'waiting';
    case IN_TREATMENT = 'in_treatment';
    case ADMITTED = 'admitted';
    case DISCHARGED = 'discharged';
    case REFERRED = 'referred';

    public function label(): string
    {
        return match ($this) {
            self::WAITING => 'Waiting',
            self::IN_TREATMENT => 'In Treatment',
            self::ADMITTED => 'Admitted',
            self::DISCHARGED => 'Discharged',
            self::REFERRED => 'Referred',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::WAITING => 'Patient is waiting for emergency treatment',
            self::IN_TREATMENT => 'Patient is receiving emergency treatment',
            self::ADMITTED => 'Patient has been admitted to hospital',
            self::DISCHARGED => 'Patient has been discharged from emergency',
            self::REFERRED => 'Patient has been referred to another facility',
        };
    }
}
