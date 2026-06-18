<?php

declare(strict_types=1);

namespace App\Enums;

enum TelemedicineStatus: string
{
    case SCHEDULED = 'scheduled';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::SCHEDULED => 'Scheduled',
            self::IN_PROGRESS => 'In Progress',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::SCHEDULED => 'Telemedicine session is scheduled',
            self::IN_PROGRESS => 'Telemedicine session is in progress',
            self::COMPLETED => 'Telemedicine session has been completed',
            self::CANCELLED => 'Telemedicine session has been cancelled',
        };
    }
}
