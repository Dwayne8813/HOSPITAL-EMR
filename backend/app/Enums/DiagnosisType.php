<?php

declare(strict_types=1);

namespace App\Enums;

enum DiagnosisType: string
{
    case PRINCIPAL = 'principal';
    case SECONDARY = 'secondary';
    case DIFFERENTIAL = 'differential';

    public function label(): string
    {
        return match ($this) {
            self::PRINCIPAL => 'Principal',
            self::SECONDARY => 'Secondary',
            self::DIFFERENTIAL => 'Differential',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::PRINCIPAL => 'Primary or main diagnosis',
            self::SECONDARY => 'Additional or contributing diagnosis',
            self::DIFFERENTIAL => 'Differential diagnosis under evaluation',
        };
    }
}
