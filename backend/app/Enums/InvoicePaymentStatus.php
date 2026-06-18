<?php

declare(strict_types=1);

namespace App\Enums;

enum InvoicePaymentStatus: string
{
    case PENDING = 'pending';
    case PAID = 'paid';
    case PARTIAL = 'partial';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::PAID => 'Paid',
            self::PARTIAL => 'Partial',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::PENDING => 'Invoice is awaiting payment',
            self::PAID => 'Invoice has been fully paid',
            self::PARTIAL => 'Invoice has been partially paid',
            self::CANCELLED => 'Invoice has been cancelled',
        };
    }
}
