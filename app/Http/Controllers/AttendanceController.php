<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'guru') {
            return redirect()->route('wali.dashboard');
        }

        $todayDate = now()->toDateString();
        $date = $request->date('date')?->toDateString() ?: $todayDate;
        $isCustomDate = $date !== $todayDate;
        $group = $request->input('group');

        $query = Student::with(['parentProfile'])->orderBy('full_name');
        if ($group) $query->where('class_group', $group);

        $students = $query->get();

        $attendances = Attendance::where('date', $date)->get()->keyBy('student_id');
        $statusCounts = [
            'hadir' => 0,
            'izin' => 0,
            'sakit' => 0,
            'alpa' => 0,
            'belum' => 0,
        ];

        $recordedStatuses = ['hadir', 'izin', 'sakit', 'alpa'];

        foreach ($students as $student) {
            $status = $attendances->get($student->id)?->status;
            $status = in_array($status, $recordedStatuses, true) ? $status : 'belum';
            $statusCounts[$status]++;
        }

        $classGroups = Student::query()
            ->select('class_group')
            ->distinct()
            ->orderBy('class_group')
            ->pluck('class_group');
        $recordedCount = collect($recordedStatuses)->sum(fn ($status) => $statusCounts[$status]);
        $totalStudents = $students->count();

        return view('guru.attendance.index', compact(
            'students',
            'attendances',
            'date',
            'todayDate',
            'isCustomDate',
            'group',
            'statusCounts',
            'classGroups',
            'recordedCount',
            'totalStudents'
        ));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'guru') {
            return redirect()->route('wali.dashboard');
        }

        $validated = $request->validate([
            'date' => 'required|date',
            'group' => 'nullable|string',
            'student_ids' => 'required|array',
            'student_ids.*' => 'required|integer|exists:students,id',
            'attendance' => 'required|array',
            'attendance.*.status' => 'nullable|in:hadir,izin,sakit,alpa',
            'attendance.*.note' => 'nullable|string',
        ]);

        $date = $validated['date'];
        foreach ($validated['student_ids'] as $studentId) {
            $data = $validated['attendance'][$studentId] ?? [];

            Attendance::updateOrCreate(
                ['student_id' => $studentId, 'date' => $date],
                [
                    'status' => $data['status'] ?? 'alpa',
                    'note' => $data['note'] ?? null,
                    'marked_by' => $user->id,
                ]
            );
        }

        return redirect()
            ->route('guru.attendance.index', array_filter([
                'date' => $date,
                'group' => $validated['group'] ?? null,
            ]))
            ->with('success', 'Absensi berhasil disimpan.');
    }

    public function update(Request $request, Attendance $attendance)
    {
        $user = Auth::user();
        if ($user->role !== 'guru') {
            return redirect()->route('wali.dashboard');
        }

        $validated = $request->validate([
            'status' => 'required|in:hadir,izin,sakit,alpa',
            'note' => 'nullable|string',
        ]);

        $attendance->update(array_merge($validated, ['marked_by' => $user->id]));

        return back()->with('success', 'Absensi diperbarui.');
    }

    public function clearTime(Request $request, Attendance $attendance, string $field)
    {
        $user = Auth::user();
        if ($user->role !== 'guru') {
            return redirect()->route('wali.dashboard');
        }

        if (! in_array($field, ['masuk', 'pulang'], true)) {
            abort(404);
        }

        $column = $field === 'masuk' ? 'check_in_at' : 'check_out_at';
        $attendance->update([
            $column => null,
            'marked_by' => $user->id,
        ]);

        return redirect()
            ->route('guru.attendance.index', array_filter([
                'date' => $attendance->date?->toDateString(),
                'group' => $request->input('group'),
            ]))
            ->with('success', 'Waktu absensi ' . $field . ' berhasil dihapus.');
    }
}
