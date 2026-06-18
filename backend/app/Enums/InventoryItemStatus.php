<?php

declare(strict_types=1);

namespace App\Enums;

enum InventoryItemStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::ACTIVE => 'Item is active in inventory',
            self::INACTIVE => 'Item is inactive or retired from inventory',
        };
    }
}
