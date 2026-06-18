<?php

declare(strict_types=1);

namespace App\Enums;

enum BloodType: string
{
    case A_POSITIVE = 'A+';
    case A_NEGATIVE = 'A-';
    case B_POSITIVE = 'B+';
    case B_NEGATIVE = 'B-';
    case AB_POSITIVE = 'AB+';
    case AB_NEGATIVE = 'AB-';
    case O_POSITIVE = 'O+';
    case O_NEGATIVE = 'O-';

    public function label(): string
    {
        return match ($this) {
            self::A_POSITIVE => 'A+',
            self::A_NEGATIVE => 'A-',
            self::B_POSITIVE => 'B+',
            self::B_NEGATIVE => 'B-',
            self::AB_POSITIVE => 'AB+',
            self::AB_NEGATIVE => 'AB-',
            self::O_POSITIVE => 'O+',
            self::O_NEGATIVE => 'O-',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::A_POSITIVE => 'Blood type A, Rh positive',
            self::A_NEGATIVE => 'Blood type A, Rh negative',
            self::B_POSITIVE => 'Blood type B, Rh positive',
            self::B_NEGATIVE => 'Blood type B, Rh negative',
            self::AB_POSITIVE => 'Blood type AB, Rh positive',
            self::AB_NEGATIVE => 'Blood type AB, Rh negative',
            self::O_POSITIVE => 'Blood type O, Rh positive',
            self::O_NEGATIVE => 'Blood type O, Rh negative',
        };
    }
}
