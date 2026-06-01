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

    public static function variants(?string $code): array
    {
        $normalized = self::normalize($code);

        if (! $normalized) {
            return [];
        }

        $variants = [$normalized];

        if (ctype_digit($normalized)) {
            $value = (int) ltrim($normalized, '0');

            if ($value >= 0 && $value <= 0xFFFFFFFF) {
                $bigEndianHex = strtoupper(str_pad(dechex($value), 8, '0', STR_PAD_LEFT));
                $variants[] = $bigEndianHex;
                $variants[] = self::reverseHexBytes($bigEndianHex);
            }
        }

        if (strlen($normalized) === 8 && ctype_xdigit($normalized)) {
            $bigEndianValue = hexdec($normalized);
            $reversedHex = self::reverseHexBytes($normalized);
            $reversedValue = hexdec($reversedHex);

            $variants[] = str_pad((string) $bigEndianValue, 10, '0', STR_PAD_LEFT);
            $variants[] = str_pad((string) $reversedValue, 10, '0', STR_PAD_LEFT);
            $variants[] = $reversedHex;
        }

        return array_values(array_unique(array_filter($variants)));
    }

    private static function reverseHexBytes(string $hex): string
    {
        return implode('', array_reverse(str_split(strtoupper($hex), 2)));
    }
}
