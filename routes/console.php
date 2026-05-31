<?php

use App\Http\Controllers\TelegramWebhookController;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('telegram:poll {--once : Process pending updates once and exit}', function () {
    $botToken = config('services.telegram.bot_token');
    if (! $botToken) {
        $this->error('TELEGRAM_BOT_TOKEN belum diatur.');
        return self::FAILURE;
    }

    $baseUrl = "https://api.telegram.org/bot{$botToken}";

    Http::timeout(10)->post("{$baseUrl}/deleteWebhook", ['drop_pending_updates' => false]);
    Http::timeout(10)->post("{$baseUrl}/setMyCommands", [
        'commands' => [
            ['command' => 'start', 'description' => 'Mulai dan hubungkan nomor HP'],
            ['command' => 'hubungkan', 'description' => 'Bagikan nomor HP wali'],
            ['command' => 'siswa', 'description' => 'Pilih siswa untuk notifikasi'],
            ['command' => 'plan', 'description' => 'Lihat langkah sinkronisasi'],
        ],
    ]);

    $this->info('Telegram polling aktif.');

    do {
        $offset = Cache::get('telegram_update_offset');
        $response = Http::timeout(35)->get("{$baseUrl}/getUpdates", array_filter([
            'timeout' => 25,
            'offset' => $offset,
            'allowed_updates' => json_encode(['message', 'callback_query']),
        ]));

        if (! $response->ok()) {
            $this->warn('Gagal mengambil update Telegram: ' . $response->body());
            sleep(5);
            continue;
        }

        foreach ($response->json('result', []) as $update) {
            $updateId = (int) ($update['update_id'] ?? 0);
            if ($updateId > 0) {
                Cache::forever('telegram_update_offset', $updateId + 1);
            }

            $secret = config('services.telegram.webhook_secret');
            $internalRequest = Request::create('/api/telegram/webhook', 'POST', $update, [], [], array_filter([
                'HTTP_X_TELEGRAM_BOT_API_SECRET_TOKEN' => $secret,
            ]));

            app(TelegramWebhookController::class)->handle($internalRequest);
        }
    } while (! $this->option('once'));

    return self::SUCCESS;
})->purpose('Poll Telegram updates when HTTPS webhook is not available');
