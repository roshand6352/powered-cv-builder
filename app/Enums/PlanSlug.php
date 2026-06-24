<?php

namespace App\Enums;

enum PlanSlug: string
{
    case Free = 'free';
    case Daily = 'daily';
    case Weekly = 'weekly';
    case Monthly = 'monthly';
    case Yearly = 'yearly';

    /**
     * @return list<string>
     */
    public static function catalogSlugs(): array
    {
        return array_column(self::cases(), 'value');
    }
}
