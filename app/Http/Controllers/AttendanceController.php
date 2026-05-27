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

        $date = $request->date('date') ?: now()->toDateString();
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

        foreach ($students as $student) {
            $status = $attendances->get($student->id)?->status ?? 'belum';
            $statusCounts[$status] = ($statusCounts[$status] ?? 0) + 1;
        }

        $classGroups = Student::query()
            ->select('class_group')
            ->distinct()
            ->orderBy('class_group')
            ->pluck('class_group');
        $recordedCount = $attendances->only($students->pluck('id')->all())->count();
        $totalStudents = $students->count();

        return view('guru.attendance.index', compact(
            'students',
            'attendances',
            'date',
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
            'attendance' => 'required|array',
            'attendance.*.status' => 'nullable|in:hadir,izin,sakit,alpa',
            'attendance.*.note' => 'nullable|string',
        ]);

        $date = $validated['date'];
        foreach ($validated['attendance'] as $studentId => $data) {
            if (empty($data['status'])) {
                continue;
            }

            Attendance::updateOrCreate(
                ['student_id' => $studentId, 'date' => $date],
                ['status' => $data['status'], 'note' => $data['note'] ?? null, 'marked_by' => $user->id]
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
}
