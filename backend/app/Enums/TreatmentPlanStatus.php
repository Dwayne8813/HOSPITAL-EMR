<?php

declare(strict_types=1);

namespace App\Enums;

enum TreatmentPlanStatus: string
{
    case ACTIVE = 'active';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::ACTIVE => 'Treatment plan is currently active',
            self::COMPLETED => 'Treatment plan has been completed',
            self::CANCELLED => 'Treatment plan has been cancelled',
        };
    }
}
