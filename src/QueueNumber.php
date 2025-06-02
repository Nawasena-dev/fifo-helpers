<?php

namespace Nawasena\Helpers;

use Illuminate\Support\Facades\DB;

class QueueNumber
{
    public static function generate(array $params = []): string
    {
        $table = $params['table'] ?? 'registrations';
        $format = $params['format'] ?? '{serial}';
        $column = $params['column'] ?? 'registration_numbers';
        $columnDate = $params['columnDate'] ?? 'date';
        $padding = $params['padding'] ?? 5;

        $prefix = $params['prefix'] ?? 'REG';
        $date = $params['date'] ?? date('Y-m-d');


        $prefix = $params['prefix'] ?? 'REG';
        $date = $params['date'] ?? date('Y-m-d');
        $latest = DB::table($table)
            ->where($columnDate, $date)
            ->orderBy($column, 'desc')
            ->value($column);
        $serial = 1;
        if ($latest) {
            preg_match('/(\d{'.$padding.'})$/', $latest, $matches);
            if (isset($matches[1])) {
                $serial = (int)$matches[1] + 1;
            }
        }
        $serialFormatted = str_pad($serial, $padding, '0', STR_PAD_LEFT);
        $replacements = [
            '{prefix}' => $prefix,
            '{serial}' => $serialFormatted,
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $format);
    }
}
