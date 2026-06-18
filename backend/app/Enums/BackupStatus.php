<?php

declare(strict_types=1);

namespace App\Enums;

enum BackupStatus: string
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case FAILED = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::COMPLETED => 'Completed',
            self::FAILED => 'Failed',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::PENDING => 'Backup is pending',
            self::COMPLETED => 'Backup completed successfully',
            self::FAILED => 'Backup failed',
        };
    }
}
