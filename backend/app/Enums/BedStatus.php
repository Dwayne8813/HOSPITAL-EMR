<?php

declare(strict_types=1);

namespace App\Enums;

enum BedStatus: string
{
    case AVAILABLE = 'available';
    case OCCUPIED = 'occupied';
    case RESERVED = 'reserved';
    case MAINTENANCE = 'maintenance';

    public function label(): string
    {
        return match ($this) {
            self::AVAILABLE => 'Available',
            self::OCCUPIED => 'Occupied',
            self::RESERVED => 'Reserved',
            self::MAINTENANCE => 'Maintenance',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::AVAILABLE => 'Bed is available for patient assignment',
            self::OCCUPIED => 'Bed is currently occupied by a patient',
            self::RESERVED => 'Bed is reserved for a specific patient',
            self::MAINTENANCE => 'Bed is under maintenance or cleaning',
        };
    }
}
