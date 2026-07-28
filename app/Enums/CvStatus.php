<?php

namespace App\Enums;

enum CvStatus: int
{
    case Draft = 1;
    case Published = 2;

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Published => 'Saved',
        };
    }

    public function toApiValue(): string
    {
        return match ($this) {
            self::Draft => 'draft',
            self::Published => 'published',
        };
    }

    public static function resolve(string|int|self|null $value): self
    {
        if ($value instanceof self) {
            return $value;
        }

        if ($value === null || $value === '') {
            return self::Draft;
        }

        if (is_int($value) || (is_string($value) && ctype_digit($value))) {
            return self::tryFrom((int) $value) ?? self::Draft;
        }

        return match ((string) $value) {
            'published' => self::Published,
            default => self::Draft,
        };
    }

    public static function toValue(string|int|self|null $value): string
    {
        return self::resolve($value)->toApiValue();
    }
}
