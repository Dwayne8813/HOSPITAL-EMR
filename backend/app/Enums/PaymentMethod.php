<?php

declare(strict_types=1);

namespace App\Enums;

enum PaymentMethod: string
{
    case CASH = 'cash';
    case CARD = 'card';
    case TRANSFER = 'transfer';
    case INSURANCE = 'insurance';
    case MIXED = 'mixed';

    public function label(): string
    {
        return match ($this) {
            self::CASH => 'Cash',
            self::CARD => 'Card',
            self::TRANSFER => 'Bank Transfer',
            self::INSURANCE => 'Insurance',
            self::MIXED => 'Mixed',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::CASH => 'Payment made in cash',
            self::CARD => 'Payment made by credit or debit card',
            self::TRANSFER => 'Payment made by bank transfer',
            self::INSURANCE => 'Payment covered by insurance',
            self::MIXED => 'Payment made using multiple methods',
        };
    }
}
