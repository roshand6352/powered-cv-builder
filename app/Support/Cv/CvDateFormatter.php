<?php

namespace App\Support\Cv;

use Carbon\Carbon;

class CvDateFormatter
{
    /**
     * Display a CV date as "January 2023".
     * Accepts YYYY-MM, YYYY-MM-DD, YYYY, and common legacy strings.
     * Unparseable values are returned unchanged.
     */
    public static function display(?string $value): string
    {
        if ($value === null) {
            return '';
        }

        $raw = trim($value);

        if ($raw === '') {
            return '';
        }

        if (strcasecmp($raw, 'Present') === 0) {
            return 'Present';
        }

        if (preg_match('/^\d{4}$/', $raw) === 1) {
            return $raw;
        }

        if (preg_match('/^(\d{4})-(\d{2})(?:-\d{2})?$/', $raw, $matches) === 1) {
            $month = (int) $matches[2];

            if ($month < 1 || $month > 12) {
                return $raw;
            }

            return Carbon::createFromDate((int) $matches[1], $month, 1)
                ->locale('en')
                ->translatedFormat('F Y');
        }

        if (preg_match('/^([A-Za-z]+)\s+(\d{4})$/', $raw, $matches) === 1) {
            try {
                return Carbon::parse($raw)
                    ->locale('en')
                    ->translatedFormat('F Y');
            } catch (\Throwable) {
                return $raw;
            }
        }

        if (preg_match('/^(\d{1,2})[\/\-](\d{4})$/', $raw, $matches) === 1) {
            $month = (int) $matches[1];
            $year = (int) $matches[2];

            if ($month < 1 || $month > 12) {
                return $raw;
            }

            return Carbon::createFromDate($year, $month, 1)
                ->locale('en')
                ->translatedFormat('F Y');
        }

        return $raw;
    }

    /**
     * Format a start/end range with optional Present flag.
     */
    public static function range(?string $start, ?string $end, bool $present = false): string
    {
        $startLabel = self::display($start);
        $endLabel = $present ? 'Present' : self::display($end);
        $hasRange = $startLabel !== '' && ($endLabel !== '' || $present);

        return $startLabel.($hasRange ? ' – ' : '').$endLabel;
    }
}
