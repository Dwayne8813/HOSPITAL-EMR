<?php

declare(strict_types=1);

namespace App\Enums;

enum RoomStatus: string
{
    case AVAILABLE = 'available';
    case OCCUPIED = 'occupied';
    case MAINTENANCE = 'maintenance';
    case CLEANING = 'cleaning';

    public function label(): string
    {
        return match ($this) {
            self::AVAILABLE => 'Available',
            self::OCCUPIED => 'Occupied',
            self::MAINTENANCE => 'Maintenance',
            self::CLEANING => 'Cleaning',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::AVAILABLE => 'Room is available for use',
            self::OCCUPIED => 'Room is currently in use',
            self::MAINTENANCE => 'Room is under maintenance',
            self::CLEANING => 'Room is being cleaned',
        };
    }
}
