<?php

declare(strict_types=1);

namespace App\Enums;

enum RoomType: string
{
    case INDIVIDUAL = 'individual';
    case SHARED = 'shared';
    case WARD = 'ward';
    case ICU = 'icu';
    case EMERGENCY = 'emergency';
    case SURGERY = 'surgery';
    case RECOVERY = 'recovery';

    public function label(): string
    {
        return match ($this) {
            self::INDIVIDUAL => 'Individual Room',
            self::SHARED => 'Shared Room',
            self::WARD => 'Ward',
            self::ICU => 'Intensive Care Unit',
            self::EMERGENCY => 'Emergency Room',
            self::SURGERY => 'Surgery Room',
            self::RECOVERY => 'Recovery Room',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::INDIVIDUAL => 'Private room for a single patient',
            self::SHARED => 'Room shared by multiple patients',
            self::WARD => 'Open ward with multiple beds',
            self::ICU => 'Intensive Care Unit room',
            self::EMERGENCY => 'Emergency department room',
            self::SURGERY => 'Operating or surgery room',
            self::RECOVERY => 'Post-surgery recovery room',
        };
    }
}
