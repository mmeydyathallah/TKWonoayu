<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Student;
use App\Support\RfidCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RfidAttendanceController extends Controller
{
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
        $previousStatus = $attendance->exists ? $attendance->status : null;

        $attendance->status = 'hadir';
        if (! $attendance->note || str_starts_with($attendance->note, 'Absen otomatis RFID')) {
            $attendance->note = 'Absen otomatis RFID pukul ' . now()->format('H:i');
        }
        $attendance->marked_by = null;
        $attendance->save();

        return response()->json([
            'success' => true,
            'message' => 'Absensi hadir berhasil dicatat.',
            'data' => [
                'student_id' => $student->id,
                'student_name' => $student->full_name,
                'class_group' => $student->class_group,
                'rfid_code' => $rfidCode,
                'date' => $today,
                'status' => $attendance->status,
                'previous_status' => $previousStatus,
                'time' => now()->format('H:i:s'),
            ],
        ]);
    }
}
