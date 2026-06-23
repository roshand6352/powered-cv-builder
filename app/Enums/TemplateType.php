<?php

namespace App\Enums;

enum TemplateType: string
{
    case Cv = 'cv';
    case CoverLetter = 'cover_letter';

    public function label(): string
    {
        return match ($this) {
            self::Cv => 'CV',
            self::CoverLetter => 'Cover Letter',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }

    public function isCv(): bool
    {
        return $this === self::Cv;
    }

    public function isCoverLetter(): bool
    {
        return $this === self::CoverLetter;
    }
}
