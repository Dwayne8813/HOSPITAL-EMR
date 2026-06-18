<?php

declare(strict_types=1);

namespace App\Enums;

enum TriageLevel: string
{
    case LEVEL_I = 'I';
    case LEVEL_II = 'II';
    case LEVEL_III = 'III';
    case LEVEL_IV = 'IV';
    case LEVEL_V = 'V';

    public function label(): string
    {
        return match ($this) {
            self::LEVEL_I => 'Level I - Resuscitation',
            self::LEVEL_II => 'Level II - Emergent',
            self::LEVEL_III => 'Level III - Urgent',
            self::LEVEL_IV => 'Level IV - Less Urgent',
            self::LEVEL_V => 'Level V - Non-Urgent',
        };
    }

    public function colorCode(): string
    {
        return match ($this) {
            self::LEVEL_I => '#FF0000',
            self::LEVEL_II => '#FF6600',
            self::LEVEL_III => '#FFCC00',
            self::LEVEL_IV => '#00CC00',
            self::LEVEL_V => '#0066FF',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::LEVEL_I => 'Requires immediate life-saving intervention',
            self::LEVEL_II => 'High risk, distorted vital signs, severe pain',
            self::LEVEL_III => 'Stable vital signs but risk of rapid deterioration',
            self::LEVEL_IV => 'Stable, no vital sign abnormalities, non-urgent',
            self::LEVEL_V => 'Minor conditions, no immediate treatment needed',
        };
    }
}
