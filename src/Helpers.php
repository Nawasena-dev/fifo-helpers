<?php

namespace Nawasena\Helpers;

class Helpers
{
    public static function formatRegistration($prefix, $year, $serial, $padding = 5)
    {
        $serialPadded = str_pad($serial, $padding, '0', STR_PAD_LEFT);
        return "{$prefix}-{$year}-{$serialPadded}";
    }
}
