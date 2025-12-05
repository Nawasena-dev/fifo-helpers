<?php

namespace Nawasena\Helpers;

use Illuminate\Support\Facades\DB;

class QueueNumber
{
    public static function generate(array $params = []): string
    {
        $table      = $params['table'] ?? 'registrations';
        $column     = $params['column'] ?? 'registration_numbers';
        $columnDate = $params['columnDate'] ?? 'date';
        $padding    = $params['padding'] ?? 3;

        $prefix     = $params['prefix'] ?? ''; 
        $date       = $params['date'] ?? date('Y-m-d');

        return DB::transaction(function () use ($table, $column, $columnDate, $padding, $prefix, $date) {
            $pattern = $prefix . '%';
            $latest = DB::table($table)
                ->whereDate($columnDate, $date)
                ->where($column, 'LIKE', $pattern)
                ->orderByDesc($column)
                ->lockForUpdate()
                ->first();
            $serial = 1;
            if ($latest) {
                if (preg_match('/(\d{' . $padding . '})$/', $latest->$column, $m)) {
                    $serial = intval($m[1]) + 1;
                }
            }
            $serialFormatted = str_pad($serial, $padding, '0', STR_PAD_LEFT);
            return $prefix . $serialFormatted;
        });
    }

}
