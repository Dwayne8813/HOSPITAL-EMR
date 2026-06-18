<?php

declare(strict_types=1);

namespace App\Enums;

enum CashMovementType: string
{
    case INCOME = 'income';
    case OUTCOME = 'outcome';
    case ADJUSTMENT = 'adjustment';

    public function label(): string
    {
        return match ($this) {
            self::INCOME => 'Income',
            self::OUTCOME => 'Outcome',
            self::ADJUSTMENT => 'Adjustment',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::INCOME => 'Cash inflow or receipt',
            self::OUTCOME => 'Cash outflow or disbursement',
            self::ADJUSTMENT => 'Cash register balance adjustment',
        };
    }
}
