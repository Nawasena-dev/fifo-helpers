<?php

namespace Nawasena\Helpers;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RegistrationNumber
{
    public static function generate(array $params = []): string
    {
        $table      = $params['table'] ?? 'registrations';
        $format     = $params['format'] ?? '{prefix}-{y}-{serial}';
        $column     = $params['column'] ?? 'registration_numbers';
        $columnDate = $params['columnDate'] ?? 'date';
        $padding    = $params['padding'] ?? 5;
        $resetBy    = $params['resetBy'] ?? 'yearly';

        $prefix = $params['prefix'] ?? 'REG';
        $date   = $params['date'] ?? date('Y-m-d');

        $carbon = Carbon::parse($date);

        /**
         * Generate base format (tanpa serial)
         */
        $base = str_replace(
            ['{prefix}', '{ymd}', '{dmy}', '{ym}', '{y}', '{serial}'],
            [
                $prefix,
                $carbon->format('Ymd'),
                $carbon->format('dmY'), // ← dmY
                $carbon->format('Ym'),
                $carbon->format('Y'),
                ''
            ],
            $format
        );

        return DB::transaction(function () use ($table, $column, $base, $padding) {

            $latest = DB::table($table)
                ->where($column, 'like', $base . '%')
                ->orderByDesc($column)
                ->lockForUpdate()
                ->first();

            $lastNumber = 0;

            if ($latest) {
                preg_match('/(\d{' . $padding . '})$/', $latest->$column, $match);
                $lastNumber = isset($match[1]) ? (int) $match[1] : 0;
            }

            $next = str_pad($lastNumber + 1, $padding, '0', STR_PAD_LEFT);

            return $base . $next;
        });
    }
}
