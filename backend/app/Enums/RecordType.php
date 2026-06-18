<?php

declare(strict_types=1);

namespace App\Enums;

enum RecordType: string
{
    case CONSULTATION = 'consultation';
    case EMERGENCY = 'emergency';
    case HOSPITALIZATION = 'hospitalization';
    case TELEMEDICINE = 'telemedicine';

    public function label(): string
    {
        return match ($this) {
            self::CONSULTATION => 'Consultation',
            self::EMERGENCY => 'Emergency',
            self::HOSPITALIZATION => 'Hospitalization',
            self::TELEMEDICINE => 'Telemedicine',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::CONSULTATION => 'In-person medical consultation',
            self::EMERGENCY => 'Emergency department visit',
            self::HOSPITALIZATION => 'Inpatient hospitalization record',
            self::TELEMEDICINE => 'Remote telemedicine consultation',
        };
    }
}
