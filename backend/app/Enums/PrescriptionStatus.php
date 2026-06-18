<?php

declare(strict_types=1);

namespace App\Enums;

enum PrescriptionStatus: string
{
    case DRAFT = 'draft';
    case DISPENSED = 'dispensed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::DISPENSED => 'Dispensed',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::DRAFT => 'Prescription is still being drafted',
            self::DISPENSED => 'Prescription has been dispensed by pharmacy',
            self::CANCELLED => 'Prescription has been cancelled',
        };
    }
}
