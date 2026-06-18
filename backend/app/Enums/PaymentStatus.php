<?php

declare(strict_types=1);

namespace App\Enums;

enum PaymentStatus: string
{
    case COMPLETED = 'completed';
    case REFUNDED = 'refunded';
    case PENDING = 'pending';

    public function label(): string
    {
        return match ($this) {
            self::COMPLETED => 'Completed',
            self::REFUNDED => 'Refunded',
            self::PENDING => 'Pending',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::COMPLETED => 'Payment has been completed successfully',
            self::REFUNDED => 'Payment has been refunded',
            self::PENDING => 'Payment is pending processing',
        };
    }
}
