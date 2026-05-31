<?php

namespace App\Http\Controllers;

use App\Models\GuardianTelegramChat;
use App\Models\ParentProfile;
use App\Models\Student;
use App\Services\TelegramNotifier;
use App\Support\PhoneNumber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

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

        if ($request->has('callback_query')) {
            $this->handleCallbackQuery($request->input('callback_query', []));

            return response()->json(['ok' => true]);
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

            $chat = GuardianTelegramChat::query()->updateOrCreate(
                ['phone_number_normalized' => $phone],
                [
                    'chat_id' => $chatId,
                    'telegram_user_id' => data_get($message, 'from.id') ? (string) data_get($message, 'from.id') : null,
                    'telegram_username' => data_get($message, 'from.username'),
                ]
            );

            $this->sendStudentSelection($chat);

            return response()->json(['ok' => true]);
        }

        $text = trim((string) data_get($message, 'text', ''));
        $command = strtolower(strtok($text, ' ') ?: $text);
        $command = strtok($command, '@') ?: $command;

        if (in_array($command, ['/start', '/hubungkan'], true)) {
            $this->telegramNotifier->requestContact($chatId);
        } elseif (in_array($command, ['/plan', '/bantuan', '/help'], true)) {
            $this->telegramNotifier->sendMessage($chatId, $this->planMessage());
        } elseif ($command === '/siswa') {
            $chat = GuardianTelegramChat::query()->where('chat_id', $chatId)->first();
            if (! $chat) {
                $this->telegramNotifier->requestContact($chatId);
            } else {
                $this->sendStudentSelection($chat);
            }
        }

        return response()->json(['ok' => true]);
    }

    private function handleCallbackQuery(array $callbackQuery): void
    {
        $callbackQueryId = (string) data_get($callbackQuery, 'id', '');
        $chatId = (string) data_get($callbackQuery, 'message.chat.id', '');
        $data = (string) data_get($callbackQuery, 'data', '');

        if ($callbackQueryId === '' || $chatId === '' || ! str_starts_with($data, 'select_student:')) {
            return;
        }

        $studentId = (int) str_replace('select_student:', '', $data);
        $chat = GuardianTelegramChat::query()->where('chat_id', $chatId)->first();
        if (! $chat) {
            $this->telegramNotifier->answerCallbackQuery($callbackQueryId, 'Bagikan nomor telepon dulu.');
            return;
        }

        $students = $this->studentsForPhone($chat->phone_number_normalized);
        $student = $students->firstWhere('id', $studentId);
        if (! $student) {
            $this->telegramNotifier->answerCallbackQuery($callbackQueryId, 'Siswa tidak cocok dengan nomor wali.');
            return;
        }

        $chat->update(['selected_student_id' => $student->id]);
        $this->telegramNotifier->answerCallbackQuery($callbackQueryId, 'Siswa dipilih.');
        $this->telegramNotifier->sendMessage(
            $chat->chat_id,
            "Notifikasi aktif untuk:\nAnanda: <b>{$student->full_name}</b>\nKelas: {$student->class_group}"
        );
    }

    private function sendStudentSelection(GuardianTelegramChat $chat): void
    {
        $students = $this->studentsForPhone($chat->phone_number_normalized);

        if ($students->isEmpty()) {
            $this->telegramNotifier->sendMessage(
                $chat->chat_id,
                "Nomor <b>{$chat->phone_number_normalized}</b> sudah terhubung, tetapi belum cocok dengan nomor HP wali di biodata siswa.\nPeriksa kolom nomor HP wali murid di biodata."
            );
            return;
        }

        if ($students->count() === 1) {
            $student = $students->first();
            $chat->update(['selected_student_id' => $student->id]);
            $this->telegramNotifier->sendMessage(
                $chat->chat_id,
                "Nomor <b>{$chat->phone_number_normalized}</b> sudah terhubung.\nNotifikasi aktif untuk:\nAnanda: <b>{$student->full_name}</b>\nKelas: {$student->class_group}"
            );
            return;
        }

        $chat->update(['selected_student_id' => null]);
        $this->telegramNotifier->sendMessage(
            $chat->chat_id,
            "Nomor <b>{$chat->phone_number_normalized}</b> terhubung ke beberapa siswa.\nPilih siswa yang ingin menerima notifikasi di chat ini.",
            [
                'inline_keyboard' => $students->map(fn (Student $student) => [[
                    'text' => "{$student->full_name} - Kel. {$student->class_group}",
                    'callback_data' => 'select_student:' . $student->id,
                ]])->values()->all(),
            ]
        );
    }

    private function studentsForPhone(string $normalizedPhone): Collection
    {
        return ParentProfile::query()
            ->whereNotNull('guardian_phone')
            ->with('student:id,full_name,class_group')
            ->get()
            ->filter(fn (ParentProfile $profile) => PhoneNumber::normalize($profile->guardian_phone) === $normalizedPhone)
            ->map(fn (ParentProfile $profile) => $profile->student)
            ->filter()
            ->values();
    }

    private function planMessage(): string
    {
        return "Plan sinkron Telegram:\n"
            . "1. Ketik /hubungkan lalu bagikan nomor HP wali.\n"
            . "2. Ketik /siswa untuk memilih anak jika nomor dipakai lebih dari satu siswa.\n"
            . "3. Setelah terpilih, notifikasi MASUK/PULANG akan dikirim otomatis.\n\n"
            . "Jika tidak ada balasan, pastikan nomor di biodata wali murid sama dengan nomor Telegram yang dibagikan.";
    }
}
