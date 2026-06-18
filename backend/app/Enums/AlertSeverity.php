<?php

declare(strict_types=1);

namespace App\Enums;

enum AlertSeverity: string
{
    case INFO = 'info';
    case WARNING = 'warning';
    case CRITICAL = 'critical';

    public function label(): string
    {
        return match ($this) {
            self::INFO => 'Info',
            self::WARNING => 'Warning',
            self::CRITICAL => 'Critical',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::INFO => 'Informational alert',
            self::WARNING => 'Warning requiring attention',
            self::CRITICAL => 'Critical alert requiring immediate action',
        };
    }
}
