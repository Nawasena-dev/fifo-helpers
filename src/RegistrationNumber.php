<?php

namespace Nawasena\Helpers;

use Illuminate\Support\Facades\DB;

class RegistrationNumber
{
    protected string $format;
    protected string $table;
    protected string $column;
    protected int $padding;

    public function __construct(string $table, string $column = 'registration_number', string $format = '{prefix}-{year}-{serial}', int $padding = 5)
    {
        $this->format = $format;
        $this->table = $table;
        $this->column = $column;
        $this->padding = $padding;
    }

    public function generate(array $params = []): string
    {
        $prefix = $params['prefix'] ?? 'REG';
        $year = $params['year'] ?? date('Y');
        $month = $params['month'] ?? date('m');

        $latest = DB::table($this->table)
            ->where($this->column, 'like', '%' . $year . '%')
            ->orderBy($this->column, 'desc')
            ->value($this->column);

        $serial = 1;

        if ($latest) {
            preg_match('/(\d{'.$this->padding.'})$/', $latest, $matches);
            if (isset($matches[1])) {
                $serial = (int)$matches[1] + 1;
            }
        }

        $serialFormatted = str_pad($serial, $this->padding, '0', STR_PAD_LEFT);

        $replacements = [
            '{prefix}' => $prefix,
            '{year}' => $year,
            '{month}' => $month,
            '{serial}' => $serialFormatted,
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $this->format);
    }
}
