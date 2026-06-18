<?php

declare(strict_types=1);

namespace App\Enums;

enum AllergySeverity: string
{
    case MILD = 'mild';
    case MODERATE = 'moderate';
    case SEVERE = 'severe';

    public function label(): string
    {
        return match ($this) {
            self::MILD => 'Mild',
            self::MODERATE => 'Moderate',
            self::SEVERE => 'Severe',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::MILD => 'Mild allergic reaction, minimal symptoms',
            self::MODERATE => 'Moderate allergic reaction, noticeable symptoms',
            self::SEVERE => 'Severe allergic reaction, life-threatening (anaphylaxis)',
        };
    }
}
