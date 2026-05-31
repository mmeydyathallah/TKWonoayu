<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\GuardianTelegramChat;
use App\Models\ParentProfile;
use App\Models\Student;
use App\Services\TelegramNotifier;
use App\Support\AttendanceSchedule;
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

        $students = Student::query()
            ->where('rfid_code', $rfidCode)
            ->get(['id', 'full_name', 'class_group', 'rfid_code']);

        if ($students->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Kartu RFID belum terdaftar pada biodata siswa.',
                'data' => [
                    'rfid_code' => $rfidCode,
                ],
            ], 404);
        }

        if ($students->count() > 1) {
            return response()->json([
                'success' => false,
                'message' => 'Kode RFID dipakai oleh lebih dari satu siswa. Perbaiki biodata siswa agar alat tidak salah mencatat absensi.',
                'data' => [
                    'rfid_code' => $rfidCode,
                    'students' => $students->map(fn (Student $student) => [
                        'student_id' => $student->id,
                        'student_name' => $student->full_name,
                        'class_group' => $student->class_group,
                    ])->values(),
                ],
            ], 409);
        }

        $student = $students->first();
        $today = now()->toDateString();
        $attendance = Attendance::query()->firstOrNew([
            'student_id' => $student->id,
            'date' => $today,
        ]);
        $timestamp = now();
        $previousStatus = $attendance->exists ? $attendance->status : null;
        $schedule = AttendanceSchedule::detectEvent($timestamp);
        $eventType = $schedule['event_type'];

        $attendance->status = 'hadir';
        if ($eventType === 'masuk') {
            if (! $attendance->check_in_at) {
                $attendance->check_in_at = $timestamp;
            } else {
                $eventType = 'sudah_tercatat';
            }
        } elseif ($eventType === 'pulang') {
            if (! $attendance->check_out_at) {
                $attendance->check_out_at = $timestamp;
            } else {
                $eventType = 'sudah_tercatat';
            }
        } elseif (! $attendance->check_in_at) {
            $attendance->check_in_at = $timestamp;
            $eventType = 'masuk';
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
                'inside_schedule_window' => $schedule['inside_window'],
                'check_in_window' => $schedule['check_in_window'],
                'check_out_window' => $schedule['check_out_window'],
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

        $guardianStudents = $this->studentsForGuardianPhone($guardianPhone);
        $chat = GuardianTelegramChat::query()
            ->where('phone_number_normalized', $guardianPhone)
            ->first();
        if (! $chat) {
            return;
        }

        if ($chat->selected_student_id && (int) $chat->selected_student_id !== (int) $student->id) {
            return;
        }

        if (! $chat->selected_student_id && $guardianStudents->count() > 1) {
            $this->telegramNotifier->sendMessage(
                $chat->chat_id,
                "Nomor wali terhubung ke beberapa siswa.\nKetik /siswa lalu pilih siswa agar notifikasi masuk/pulang tidak tertukar."
            );
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

    private function studentsForGuardianPhone(string $normalizedPhone)
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
}
