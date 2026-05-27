<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TelegramNotifier
{
    public function sendMessage(string $chatId, string $text): bool
    {
        $botToken = config('services.telegram.bot_token');
        if (! $botToken) {
            return false;
        }

        $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
        $response = Http::timeout(10)->post($url, [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
        ]);

        return $response->ok();
    }

    public function requestContact(string $chatId): bool
    {
        $botToken = config('services.telegram.bot_token');
        if (! $botToken) {
            return false;
        }

        $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
        $response = Http::timeout(10)->post($url, [
            'chat_id' => $chatId,
            'text' => "Agar notifikasi absensi aktif, tekan tombol di bawah lalu bagikan nomor Anda.",
            'reply_markup' => [
                'keyboard' => [[[
                    'text' => 'Bagikan Nomor Telepon',
                    'request_contact' => true,
                ]]],
                'resize_keyboard' => true,
                'one_time_keyboard' => true,
            ],
        ]);

        return $response->ok();
    }
}
