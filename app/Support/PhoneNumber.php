<?php

namespace App\Support;

class PhoneNumber
{
    public static function normalize(?string $phone): ?string
    {
        $value = preg_replace('/[^0-9]/', '', (string) $phone);

        if (! $value) {
            return null;
        }

        if (str_starts_with($value, '0')) {
            $value = '62' . substr($value, 1);
        }

        if (! str_starts_with($value, '62')) {
            return $value;
        }

        return $value;
    }
}
