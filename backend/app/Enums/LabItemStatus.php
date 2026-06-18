<?php

declare(strict_types=1);

namespace App\Enums;

enum LabItemStatus: string
{
    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::IN_PROGRESS => 'In Progress',
            self::COMPLETED => 'Completed',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::PENDING => 'Individual lab test is pending',
            self::IN_PROGRESS => 'Individual lab test is in progress',
            self::COMPLETED => 'Individual lab test is completed',
        };
    }
}
