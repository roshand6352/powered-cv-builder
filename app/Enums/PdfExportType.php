<?php

namespace App\Enums;

enum PdfExportType: string
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
}
