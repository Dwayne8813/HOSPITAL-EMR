<?php

declare(strict_types=1);

namespace App\Enums;

enum ImagingOrderStatus: string
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
            self::PENDING => 'Imaging order is awaiting processing',
            self::IN_PROGRESS => 'Imaging study is being performed',
            self::COMPLETED => 'Imaging study has been completed',
            self::CANCELLED => 'Imaging order has been cancelled',
        };
    }
}
