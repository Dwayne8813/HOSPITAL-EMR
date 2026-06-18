<?php

declare(strict_types=1);

namespace App\Enums;

enum AppointmentStatus: string
{
    case SCHEDULED = 'scheduled';
    case CONFIRMED = 'confirmed';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case NO_SHOW = 'no_show';

    public function label(): string
    {
        return match ($this) {
            self::SCHEDULED => 'Scheduled',
            self::CONFIRMED => 'Confirmed',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
            self::NO_SHOW => 'No Show',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::SCHEDULED => 'Appointment has been scheduled',
            self::CONFIRMED => 'Appointment has been confirmed by patient',
            self::COMPLETED => 'Appointment has been completed',
            self::CANCELLED => 'Appointment has been cancelled',
            self::NO_SHOW => 'Patient did not attend the appointment',
        };
    }
}
