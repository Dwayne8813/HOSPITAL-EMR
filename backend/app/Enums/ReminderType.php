<?php

declare(strict_types=1);

namespace App\Enums;

enum ReminderType: string
{
    case EMAIL = 'email';
    case SMS = 'sms';
    case WHATSAPP = 'whatsapp';

    public function label(): string
    {
        return match ($this) {
            self::EMAIL => 'Email',
            self::SMS => 'SMS',
            self::WHATSAPP => 'WhatsApp',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::EMAIL => 'Reminder sent via email',
            self::SMS => 'Reminder sent via SMS text message',
            self::WHATSAPP => 'Reminder sent via WhatsApp message',
        };
    }
}
