<?php

declare(strict_types=1);

namespace App\Enums;

enum CertificateType: string
{
    case MEDICAL = 'medical';
    case REST = 'rest';
    case WORK = 'work';
    case SCHOOL = 'school';

    public function label(): string
    {
        return match ($this) {
            self::MEDICAL => 'Medical Certificate',
            self::REST => 'Rest Certificate',
            self::WORK => 'Work Fitness Certificate',
            self::SCHOOL => 'School Certificate',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::MEDICAL => 'General medical certificate of condition',
            self::REST => 'Certificate of rest or medical leave',
            self::WORK => 'Certificate of fitness for work',
            self::SCHOOL => 'Certificate for school attendance or absence',
        };
    }
}
