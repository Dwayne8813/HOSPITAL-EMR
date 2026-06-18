<?php

declare(strict_types=1);

namespace App\Enums;

enum LabOrderStatus: string
{
    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::IN_PROGRESS => 'In Progress',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::PENDING => 'Lab order is awaiting processing',
            self::IN_PROGRESS => 'Lab order is being processed',
            self::COMPLETED => 'Lab order has been completed with results',
            self::CANCELLED => 'Lab order has been cancelled',
        };
    }
}
