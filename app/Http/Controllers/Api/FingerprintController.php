<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FingerprintDeletion;
use App\Models\FingerprintEnrollment;
use App\Models\Student;
use App\Services\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FingerprintController extends Controller
{
    private function validateToken(Request $request): bool
    {
        $configured = config('services.fingerprint.token') ?: config('services.rfid_attendance.token');

        if (! $configured) {
            return true;
        }

        $provided = $request->bearerToken()
            ?: $request->header('X-Fingerprint-Token')
            ?: $request->input('device_token')
            ?: $request->input('token');

        return hash_equals($configured, (string) $provided);
    }

    // ===== ENROLLMENT =====

    public function requestEnrollment(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
        ]);

        $student = Student::findOrFail($validated['student_id']);

        // Cancel any existing pending enrollment for this student
        FingerprintEnrollment::where('student_id', $student->id)
            ->where('status', 'pending')
            ->update(['status' => 'cancelled', 'completed_at' => now()]);

        $enrollment = FingerprintEnrollment::create([
            'student_id' => $student->id,
            'status' => 'pending',
            'requested_by' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permintaan enrollment fingerprint dibuat. Menunggu scan di perangkat.',
            'data' => [
                'enrollment_id' => $enrollment->id,
                'student_name' => $student->full_name,
            ],
        ]);
    }

    public function enrollmentStatus(Request $request, $id): JsonResponse
    {
        $enrollment = FingerprintEnrollment::with('student:id,full_name')->find($id);

        if (! $enrollment) {
            return response()->json(['success' => false, 'message' => 'Enrollment tidak ditemukan.'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'enrollment_id' => $enrollment->id,
                'status' => $enrollment->status,
                'student_name' => $enrollment->student?->full_name,
                'fingerprint_id' => $enrollment->fingerprint_id,
                'error_message' => $enrollment->error_message,
            ],
        ]);
    }

    public function checkEnrollment(Request $request): JsonResponse
    {
        if (! $this->validateToken($request)) {
            return response()->json(['success' => false, 'message' => 'Token tidak valid.'], 401);
        }

        $enrollment = FingerprintEnrollment::with('student:id,full_name,class_group')
            ->where('status', 'pending')
            ->orderBy('created_at')
            ->first();

        if (! $enrollment) {
            return response()->json(['success' => true, 'data' => null]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'enrollment_id' => $enrollment->id,
                'student_id' => $enrollment->student_id,
                'student_name' => $enrollment->student?->full_name,
                'class_group' => $enrollment->student?->class_group,
            ],
        ]);
    }

    public function completeEnrollment(Request $request): JsonResponse
    {
        if (! $this->validateToken($request)) {
            return response()->json(['success' => false, 'message' => 'Token tidak valid.'], 401);
        }

        $validated = $request->validate([
            'enrollment_id' => ['required', 'exists:fingerprint_enrollments,id'],
            'fingerprint_id' => ['required', 'integer', 'min:1', 'max:162'],
            'fingerprint_data' => ['nullable', 'string'],
        ]);

        $enrollment = FingerprintEnrollment::findOrFail($validated['enrollment_id']);

        if ($enrollment->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Enrollment sudah tidak pending.'], 422);
        }

        $templateBytes = null;
        if (! empty($validated['fingerprint_data'])) {
            $templateBytes = base64_decode($validated['fingerprint_data'], true);
            if ($templateBytes === false) {
                $templateBytes = null;
            }
        }

        // Free up old fingerprint slot if student had one
        if ($enrollment->student->fingerprint_id && $enrollment->student->fingerprint_id !== $validated['fingerprint_id']) {
            FingerprintDeletion::create([
                'student_id' => $enrollment->student_id,
                'fingerprint_id' => $enrollment->student->fingerprint_id,
                'status' => 'pending',
                'requested_by' => $enrollment->requested_by,
            ]);
        }

        $enrollment->update([
            'status' => 'enrolled',
            'fingerprint_id' => $validated['fingerprint_id'],
            'fingerprint_data' => $templateBytes,
            'completed_at' => now(),
        ]);

        $enrollment->student->update([
            'fingerprint_id' => $validated['fingerprint_id'],
            'fingerprint_data' => $templateBytes,
            'fingerprint_enrolled_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Enrollment berhasil.',
            'data' => [
                'enrollment_id' => $enrollment->id,
                'fingerprint_id' => $validated['fingerprint_id'],
                'student_name' => $enrollment->student->full_name,
            ],
        ]);
    }

    public function failEnrollment(Request $request): JsonResponse
    {
        if (! $this->validateToken($request)) {
            return response()->json(['success' => false, 'message' => 'Token tidak valid.'], 401);
        }

        $validated = $request->validate([
            'enrollment_id' => ['required', 'exists:fingerprint_enrollments,id'],
            'error_message' => ['required', 'string'],
        ]);

        $enrollment = FingerprintEnrollment::findOrFail($validated['enrollment_id']);

        $enrollment->update([
            'status' => 'failed',
            'error_message' => $validated['error_message'],
            'completed_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Enrollment ditandai gagal.']);
    }

    // ===== DELETION =====

    public function requestDeletion(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
        ]);

        $student = Student::findOrFail($validated['student_id']);

        if (! $student->fingerprint_id) {
            return response()->json(['success' => false, 'message' => 'Siswa tidak punya fingerprint.'], 422);
        }

        FingerprintDeletion::create([
            'student_id' => $student->id,
            'fingerprint_id' => $student->fingerprint_id,
            'status' => 'pending',
            'requested_by' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permintaan hapus fingerprint dibuat. Menunggu eksekusi di perangkat.',
        ]);
    }

    public function checkDeletion(Request $request): JsonResponse
    {
        if (! $this->validateToken($request)) {
            return response()->json(['success' => false, 'message' => 'Token tidak valid.'], 401);
        }

        $deletion = FingerprintDeletion::with('student:id,full_name')
            ->where('status', 'pending')
            ->orderBy('created_at')
            ->first();

        if (! $deletion) {
            return response()->json(['success' => true, 'data' => null]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'deletion_id' => $deletion->id,
                'fingerprint_id' => $deletion->fingerprint_id,
                'student_name' => $deletion->student?->full_name,
            ],
        ]);
    }

    public function completeDeletion(Request $request): JsonResponse
    {
        if (! $this->validateToken($request)) {
            return response()->json(['success' => false, 'message' => 'Token tidak valid.'], 401);
        }

        $validated = $request->validate([
            'deletion_id' => ['required', 'exists:fingerprint_deletions,id'],
        ]);

        $deletion = FingerprintDeletion::findOrFail($validated['deletion_id']);

        $deletion->update([
            'status' => 'done',
            'completed_at' => now(),
        ]);

        if ($deletion->student && $deletion->student->fingerprint_id === $deletion->fingerprint_id) {
            $deletion->student->update([
                'fingerprint_id' => null,
                'fingerprint_data' => null,
                'fingerprint_enrolled_at' => null,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Fingerprint dihapus dari perangkat.']);
    }
}
