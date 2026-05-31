<?php

namespace App\Support;

use App\Models\SchoolSetting;
use Carbon\CarbonInterface;

class AttendanceSchedule
{
    public const CHECK_IN_KEY = 'attendance_check_in_time';
    public const CHECK_OUT_KEY = 'attendance_check_out_time';
    public const WINDOW_MINUTES_KEY = 'attendance_window_minutes';

    public const DEFAULT_CHECK_IN = '07:00';
    public const DEFAULT_CHECK_OUT = '11:00';
    public const DEFAULT_WINDOW_MINUTES = 60;

    public static function settings(): array
    {
        return [
            'check_in_time' => SchoolSetting::value(self::CHECK_IN_KEY, self::DEFAULT_CHECK_IN),
            'check_out_time' => SchoolSetting::value(self::CHECK_OUT_KEY, self::DEFAULT_CHECK_OUT),
            'window_minutes' => (int) SchoolSetting::value(self::WINDOW_MINUTES_KEY, (string) self::DEFAULT_WINDOW_MINUTES),
        ];
    }

    public static function save(string $checkInTime, string $checkOutTime, int $windowMinutes = self::DEFAULT_WINDOW_MINUTES): void
    {
        SchoolSetting::setValue(self::CHECK_IN_KEY, $checkInTime);
        SchoolSetting::setValue(self::CHECK_OUT_KEY, $checkOutTime);
        SchoolSetting::setValue(self::WINDOW_MINUTES_KEY, (string) $windowMinutes);
    }

    public static function detectEvent(CarbonInterface $timestamp): array
    {
        $settings = self::settings();
        $current = self::minutesFromTime($timestamp->format('H:i'));
        $checkIn = self::minutesFromTime($settings['check_in_time']);
        $checkOut = self::minutesFromTime($settings['check_out_time']);
        $window = max(0, (int) $settings['window_minutes']);

        $checkInDistance = abs($current - $checkIn);
        $checkOutDistance = abs($current - $checkOut);
        $insideCheckIn = $checkInDistance <= $window;
        $insideCheckOut = $checkOutDistance <= $window;

        if ($insideCheckIn && (! $insideCheckOut || $checkInDistance <= $checkOutDistance)) {
            return self::result('masuk', true, $settings);
        }

        if ($insideCheckOut) {
            return self::result('pulang', true, $settings);
        }

        return self::result(null, false, $settings);
    }

    public static function windowLabel(string $time, int $windowMinutes): string
    {
        $target = self::minutesFromTime($time);

        return self::formatMinutes($target - $windowMinutes)
            . ' - '
            . self::formatMinutes($target + $windowMinutes);
    }

    private static function result(?string $eventType, bool $insideWindow, array $settings): array
    {
        return [
            'event_type' => $eventType,
            'inside_window' => $insideWindow,
            'check_in_time' => $settings['check_in_time'],
            'check_out_time' => $settings['check_out_time'],
            'window_minutes' => $settings['window_minutes'],
            'check_in_window' => self::windowLabel($settings['check_in_time'], (int) $settings['window_minutes']),
            'check_out_window' => self::windowLabel($settings['check_out_time'], (int) $settings['window_minutes']),
        ];
    }

    private static function minutesFromTime(string $time): int
    {
        [$hour, $minute] = array_pad(explode(':', $time), 2, 0);

        return ((int) $hour * 60) + (int) $minute;
    }

    private static function formatMinutes(int $minutes): string
    {
        $minutes = ($minutes % 1440 + 1440) % 1440;
        $hour = intdiv($minutes, 60);
        $minute = $minutes % 60;

        return sprintf('%02d:%02d', $hour, $minute);
    }
}
