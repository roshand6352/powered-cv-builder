<?php

namespace App\Enums;

enum PaymentGateway: string
{
    case Payfast = 'payfast';
    case Manual = 'manual';

    public function label(): string
    {
        return match ($this) {
            self::Payfast => 'PayFast',
            self::Manual => 'Manual',
        };
    }
}
