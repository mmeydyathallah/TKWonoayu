<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Attendance;
use App\Models\GuardianTelegramChat;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        $adminName = Auth::user()->name;
        $today = now()->toDateString();

        // Statistics
        $totalStudents = Student::count();
        $totalGuru = User::where('role', 'guru')->count();
        $totalWali = User::where('role', 'wali_murid')->count();
        $totalAdmin = User::where('role', 'admin')->count();
        $totalUsers = User::count();

        // Today's attendance
        $todayAttendances = Attendance::whereDate('date', $today)->get();
        $attendanceCounts = [
            'hadir' => $todayAttendances->where('status', 'hadir')->count(),
            'izin' => $todayAttendances->where('status', 'izin')->count(),
            'sakit' => $todayAttendances->where('status', 'sakit')->count(),
            'alpa' => max($totalStudents - $todayAttendances->whereIn('status', ['hadir', 'izin', 'sakit', 'alpa'])->count(), 0)
                + $todayAttendances->where('status', 'alpa')->count(),
        ];
        $recordedAttendanceCount = $todayAttendances->count();
        $attendancePercent = $totalStudents > 0
            ? round(($attendanceCounts['hadir'] / $totalStudents) * 100)
            : 0;

        // Telegram stats
        $telegramChats = GuardianTelegramChat::count();
        $telegramSelected = GuardianTelegramChat::whereNotNull('selected_student_id')->count();

        // Last RFID activity
        $lastRfidActivity = Attendance::where('note', 'Absen otomatis RFID')
            ->latest('check_in_at')
            ->first();

        // Recent audit logs
        $recentLogs = AuditLog::latest()->limit(10)->get();

        return view('admin.dashboard.index', compact(
            'adminName',
            'totalStudents',
            'totalGuru',
            'totalWali',
            'totalAdmin',
            'totalUsers',
            'attendanceCounts',
            'recordedAttendanceCount',
            'attendancePercent',
            'telegramChats',
            'telegramSelected',
            'lastRfidActivity',
            'recentLogs',
        ));
    }
}
