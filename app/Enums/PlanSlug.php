<?php

namespace App\Enums;

enum PlanSlug: string
{
    case Free = 'free';
    case Pro = 'pro';

    /**
     * @return list<string>
     */
    public static function catalogSlugs(): array
    {
        return array_column(self::cases(), 'value');
    }
}
