<?php

declare(strict_types=1);

namespace App\Enums;

enum InsuranceClaimStatus: string
{
    case SUBMITTED = 'submitted';
    case PROCESSING = 'processing';
    case APPROVED = 'approved';
    case DENIED = 'denied';
    case PAID = 'paid';

    public function label(): string
    {
        return match ($this) {
            self::SUBMITTED => 'Submitted',
            self::PROCESSING => 'Processing',
            self::APPROVED => 'Approved',
            self::DENIED => 'Denied',
            self::PAID => 'Paid',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::SUBMITTED => 'Claim has been submitted to insurance',
            self::PROCESSING => 'Claim is being processed by insurance',
            self::APPROVED => 'Claim has been approved by insurance',
            self::DENIED => 'Claim has been denied by insurance',
            self::PAID => 'Insurance has paid the claim',
        };
    }
}
