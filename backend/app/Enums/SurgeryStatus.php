<?php

declare(strict_types=1);

namespace App\Enums;

enum SurgeryStatus: string
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
            self::SCHEDULED => 'Surgery has been scheduled',
            self::IN_PROGRESS => 'Surgery is currently in progress',
            self::COMPLETED => 'Surgery has been completed',
            self::CANCELLED => 'Surgery has been cancelled',
        };
    }
}
