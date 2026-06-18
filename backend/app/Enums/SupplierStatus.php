<?php

declare(strict_types=1);

namespace App\Enums;

enum SupplierStatus: string
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
            self::ACTIVE => 'Supplier is active and available',
            self::INACTIVE => 'Supplier is inactive',
        };
    }
}
