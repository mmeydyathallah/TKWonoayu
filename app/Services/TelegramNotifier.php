<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramNotifier
{
    public function sendMessage(string $chatId, string $text, ?array $replyMarkup = null): bool
    {
        $botToken = config('services.telegram.bot_token');
        if (! $botToken) {
            return false;
        }

        $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
        $payload = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
        ];

        if ($replyMarkup) {
            $payload['reply_markup'] = $replyMarkup;
        }

        $response = Http::timeout(10)->post($url, $payload);

        if (! $response->ok()) {
            Log::warning('Telegram sendMessage failed', [
                'chat_id' => $chatId,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        }

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
            'text' => "Agar notifikasi aktif:\n1. Tekan tombol di bawah untuk bagikan nomor HP.\n2. Lanjut ketik /siswa untuk pilih anak.\n3. Ketik /plan jika butuh panduan ulang.",
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

    public function answerCallbackQuery(string $callbackQueryId, string $text = ''): bool
    {
        $botToken = config('services.telegram.bot_token');
        if (! $botToken) {
            return false;
        }

        $url = "https://api.telegram.org/bot{$botToken}/answerCallbackQuery";
        $response = Http::timeout(10)->post($url, array_filter([
            'callback_query_id' => $callbackQueryId,
            'text' => $text,
        ]));

        return $response->ok();
    }
}
