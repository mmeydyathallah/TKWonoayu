<?php

namespace App\Support;

class RfidCode
{
    public static function normalize(?string $code): ?string
    {
        $value = trim((string) $code);

        if ($value === '') {
            return null;
        }

        $value = preg_replace('/[\s:\-]+/', '', $value);

        return strtoupper($value);
    }
}
