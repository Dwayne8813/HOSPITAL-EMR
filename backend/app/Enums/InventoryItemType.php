<?php

declare(strict_types=1);

namespace App\Enums;

enum InventoryItemType: string
{
    case EQUIPMENT = 'equipment';
    case SUPPLIES = 'supplies';
    case DISPOSABLE = 'disposable';
    case ASSET = 'asset';

    public function label(): string
    {
        return match ($this) {
            self::EQUIPMENT => 'Equipment',
            self::SUPPLIES => 'Supplies',
            self::DISPOSABLE => 'Disposable',
            self::ASSET => 'Asset',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::EQUIPMENT => 'Reusable medical equipment',
            self::SUPPLIES => 'Consumable medical supplies',
            self::DISPOSABLE => 'Single-use disposable items',
            self::ASSET => 'Capital assets and fixed equipment',
        };
    }
}
