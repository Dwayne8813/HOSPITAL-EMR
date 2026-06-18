<?php

declare(strict_types=1);

namespace App\Enums;

enum InventoryMovementType: string
{
    case ENTRY = 'entry';
    case EXIT = 'exit';
    case TRANSFER = 'transfer';
    case ADJUSTMENT = 'adjustment';

    public function label(): string
    {
        return match ($this) {
            self::ENTRY => 'Entry',
            self::EXIT => 'Exit',
            self::TRANSFER => 'Transfer',
            self::ADJUSTMENT => 'Adjustment',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::ENTRY => 'Stock entry into inventory',
            self::EXIT => 'Stock exit from inventory',
            self::TRANSFER => 'Stock transfer between locations',
            self::ADJUSTMENT => 'Stock quantity adjustment',
        };
    }
}
