<?php

declare(strict_types=1);

namespace App\Enums;

enum BatchStatus: string
{
    case ACTIVE = 'active';
    case EXPIRED = 'expired';
    case DEPLETED = 'depleted';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::EXPIRED => 'Expired',
            self::DEPLETED => 'Depleted',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::ACTIVE => 'Batch is active and available for use',
            self::EXPIRED => 'Batch has expired',
            self::DEPLETED => 'Batch stock has been fully depleted',
        };
    }
}
