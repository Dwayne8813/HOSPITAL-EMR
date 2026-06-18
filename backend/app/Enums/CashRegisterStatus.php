<?php

declare(strict_types=1);

namespace App\Enums;

enum CashRegisterStatus: string
{
    case OPEN = 'open';
    case CLOSED = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::OPEN => 'Open',
            self::CLOSED => 'Closed',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::OPEN => 'Cash register is currently open and accepting transactions',
            self::CLOSED => 'Cash register is closed',
        };
    }
}
