<?php

namespace App\Http\Controllers;

use App\Models\GuardianTelegramChat;
use App\Services\TelegramNotifier;
use App\Support\PhoneNumber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TelegramWebhookController extends Controller
{
    public function __construct(private readonly TelegramNotifier $telegramNotifier)
    {
    }

    public function handle(Request $request): JsonResponse
    {
        $secret = config('services.telegram.webhook_secret');
        if ($secret) {
            $headerSecret = $request->header('X-Telegram-Bot-Api-Secret-Token');
            if (! hash_equals($secret, (string) $headerSecret)) {
                return response()->json(['ok' => false], 401);
            }
        }

        $message = $request->input('message', []);
        $chatId = (string) data_get($message, 'chat.id', '');

        if ($chatId === '') {
            return response()->json(['ok' => true]);
        }

        $contact = data_get($message, 'contact');
        if (is_array($contact) && isset($contact['phone_number'])) {
            $phone = PhoneNumber::normalize((string) $contact['phone_number']);
            if (! $phone) {
                return response()->json(['ok' => true]);
            }

            GuardianTelegramChat::query()->updateOrCreate(
                ['phone_number_normalized' => $phone],
                [
                    'chat_id' => $chatId,
                    'telegram_user_id' => data_get($message, 'from.id') ? (string) data_get($message, 'from.id') : null,
                    'telegram_username' => data_get($message, 'from.username'),
                ]
            );

            $this->telegramNotifier->sendMessage(
                $chatId,
                "Nomor <b>{$phone}</b> sudah terhubung.\nNotifikasi absensi masuk/pulang akan dikirim ke chat ini."
            );

            return response()->json(['ok' => true]);
        }

        $text = trim((string) data_get($message, 'text', ''));
        if (in_array($text, ['/start', '/hubungkan'], true)) {
            $this->telegramNotifier->requestContact($chatId);
        }

        return response()->json(['ok' => true]);
    }
}
