<?php

namespace Nawasena\Helpers;

use Illuminate\Support\Facades\DB;

class RegistrationNumber
{
    public static function generate(array $params = []): string
    {
        $table = $params['table'] ?? 'registrations';
        $format = $params['format'] ?? '{prefix}-{year}-{serial}';
        $column = $params['column'] ?? 'registration_numbers';
        $columnDate = $params['columnDate'] ?? 'date';
        $padding = $params['padding'] ?? 5;
        $resetBy = $params['resetBy'] ?? 'yearly';

        $prefix = $params['prefix'] ?? 'REG';
        $date = $params['date'] ?? date('Y-m-d');

        // Generate date segment
        $dateFormat = match ($resetBy) {
            'daily' => Carbon::parse($date)->format('Ymd'),
            'monthly' => Carbon::parse($date)->format('Ym'),
            'yearly' => Carbon::parse($date)->format('Y'),
            default => Carbon::parse($date)->format('Ymd'),
        };
        $base = str_replace(
            ['{prefix}', '{ymd}', '{ym}', '{y}', '{serial}'],
            [$prefix, Carbon::parse($date)->format('Ymd'), Carbon::parse($date)->format('Ym'), Carbon::parse($date)->format('Y'), ''],
            $format
        );
        $latest = DB::table($table)
            ->where($column, 'like', $base . '%')
            ->orderByDesc($column)
            ->first();
        $lastNumber = 0;
        if ($latest) {
            preg_match('/(\d{'.$padding.'})$/', $latest->$column, $match);
            $lastNumber = isset($match[1]) ? (int) $match[1] : 0;
        }
        $next = str_pad($lastNumber + 1, $padding, '0', STR_PAD_LEFT);
        return str_replace('{serial}', $next, $base . $next);
    }
}
