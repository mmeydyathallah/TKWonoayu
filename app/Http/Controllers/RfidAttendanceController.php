<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\GuardianTelegramChat;
use App\Models\Student;
use App\Services\TelegramNotifier;
use App\Support\PhoneNumber;
use App\Support\RfidCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RfidAttendanceController extends Controller
{
    public function __construct(private readonly TelegramNotifier $telegramNotifier)
    {
    }

    public function store(Request $request): JsonResponse
    {
        $configuredToken = config('services.rfid_attendance.token');

        if ($configuredToken) {
            $providedToken = $request->bearerToken()
                ?: $request->header('X-RFID-Token')
                ?: $request->input('device_token')
                ?: $request->input('token');

            if (! hash_equals($configuredToken, (string) $providedToken)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token alat RFID tidak valid.',
                ], 401);
            }
        }

        $rfidCode = RfidCode::normalize(
            $request->input('rfid_code')
                ?: $request->input('uid')
                ?: $request->input('card_uid')
        );

        if (! $rfidCode || strlen($rfidCode) > 64) {
            return response()->json([
                'success' => false,
                'message' => 'Kode RFID wajib diisi dan maksimal 64 karakter.',
            ], 422);
        }

        $student = Student::query()
            ->where('rfid_code', $rfidCode)
            ->first();

        if (! $student) {
            return response()->json([
                'success' => false,
                'message' => 'Kartu RFID belum terdaftar pada biodata siswa.',
                'data' => [
                    'rfid_code' => $rfidCode,
                ],
            ], 404);
        }

        $today = now()->toDateString();
        $attendance = Attendance::query()->firstOrNew([
            'student_id' => $student->id,
            'date' => $today,
        ]);
        $timestamp = now();
        $previousStatus = $attendance->exists ? $attendance->status : null;
        $eventType = 'masuk';

        $attendance->status = 'hadir';
        if (! $attendance->check_in_at) {
            $attendance->check_in_at = $timestamp;
        } elseif (! $attendance->check_out_at) {
            $attendance->check_out_at = $timestamp;
            $eventType = 'pulang';
        } else {
            $eventType = 'sudah_tercatat';
        }

        if (! $attendance->note || str_starts_with($attendance->note, 'Absen otomatis RFID')) {
            $attendance->note = 'Absen otomatis RFID';
        }
        $attendance->marked_by = null;
        $attendance->save();

        if (in_array($eventType, ['masuk', 'pulang'], true)) {
            $this->notifyGuardian($student, $eventType, $timestamp);
        }

        return response()->json([
            'success' => true,
            'message' => $eventType === 'pulang'
                ? 'Absensi pulang berhasil dicatat.'
                : ($eventType === 'masuk' ? 'Absensi masuk berhasil dicatat.' : 'Absensi hari ini sudah lengkap.'),
            'data' => [
                'student_id' => $student->id,
                'student_name' => $student->full_name,
                'class_group' => $student->class_group,
                'rfid_code' => $rfidCode,
                'date' => $today,
                'status' => $attendance->status,
                'event_type' => $eventType,
                'check_in_at' => optional($attendance->check_in_at)->format('Y-m-d H:i:s'),
                'check_out_at' => optional($attendance->check_out_at)->format('Y-m-d H:i:s'),
                'previous_status' => $previousStatus,
                'time' => $timestamp->format('H:i:s'),
            ],
        ]);
    }

    private function notifyGuardian(Student $student, string $eventType, $timestamp): void
    {
        $student->loadMissing('parentProfile');
        $guardianPhone = PhoneNumber::normalize($student->parentProfile?->guardian_phone);
        if (! $guardianPhone) {
            return;
        }

        $chat = GuardianTelegramChat::query()
            ->where('phone_number_normalized', $guardianPhone)
            ->first();
        if (! $chat) {
            return;
        }

        $eventLabel = $eventType === 'masuk' ? 'MASUK' : 'PULANG';
        $text = "Notifikasi Absensi TK Wonoayu\n"
            . "Ananda: <b>{$student->full_name}</b>\n"
            . "Status: <b>{$eventLabel}</b>\n"
            . "Waktu: <b>{$timestamp->format('d-m-Y H:i')}</b>\n"
            . "Kelas: {$student->class_group}";

        $this->telegramNotifier->sendMessage($chat->chat_id, $text);
    }
}
