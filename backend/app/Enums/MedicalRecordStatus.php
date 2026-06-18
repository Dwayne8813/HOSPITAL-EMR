<?php

declare(strict_types=1);

namespace App\Enums;

enum MedicalRecordStatus: string
{
    case DRAFT = 'draft';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::DRAFT => 'Record is still being drafted',
            self::COMPLETED => 'Record has been finalized and signed',
            self::CANCELLED => 'Record has been cancelled',
        };
    }
}
