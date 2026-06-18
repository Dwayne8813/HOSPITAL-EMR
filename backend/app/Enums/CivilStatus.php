<?php

declare(strict_types=1);

namespace App\Enums;

enum CivilStatus: string
{
    case SINGLE = 'single';
    case MARRIED = 'married';
    case DIVORCED = 'divorced';
    case WIDOWED = 'widowed';
    case COHABITING = 'cohabiting';

    public function label(): string
    {
        return match ($this) {
            self::SINGLE => 'Single',
            self::MARRIED => 'Married',
            self::DIVORCED => 'Divorced',
            self::WIDOWED => 'Widowed',
            self::COHABITING => 'Cohabiting',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::SINGLE => 'Never married',
            self::MARRIED => 'Currently married',
            self::DIVORCED => 'Legally divorced',
            self::WIDOWED => 'Spouse deceased',
            self::COHABITING => 'Living with partner',
        };
    }
}
