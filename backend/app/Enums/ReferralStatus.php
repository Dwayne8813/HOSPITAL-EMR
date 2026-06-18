<?php

declare(strict_types=1);

namespace App\Enums;

enum ReferralStatus: string
{
    case SENT = 'sent';
    case RECEIVED = 'received';
    case COMPLETED = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::SENT => 'Sent',
            self::RECEIVED => 'Received',
            self::COMPLETED => 'Completed',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::SENT => 'Referral has been sent to the specialist',
            self::RECEIVED => 'Referral has been received by the specialist',
            self::COMPLETED => 'Referral has been completed',
        };
    }
}
