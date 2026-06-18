<?php

declare(strict_types=1);

namespace App\Enums;

enum ReferralUrgency: string
{
    case NORMAL = 'normal';
    case URGENT = 'urgent';
    case EMERGENCY = 'emergency';

    public function label(): string
    {
        return match ($this) {
            self::NORMAL => 'Normal',
            self::URGENT => 'Urgent',
            self::EMERGENCY => 'Emergency',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::NORMAL => 'Standard referral with no urgency',
            self::URGENT => 'Referral requiring prompt attention',
            self::EMERGENCY => 'Referral requiring immediate attention',
        };
    }
}
