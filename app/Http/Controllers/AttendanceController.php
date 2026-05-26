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
            abort(403);
        }

        $date = $request->date('date') ?: now()->toDateString();
        $group = $request->input('group');

        $query = Student::with(['parentProfile'])->orderBy('full_name');
        if ($group) $query->where('class_group', $group);

        $students = $query->get();

        $attendances = Attendance::where('date', $date)->get()->keyBy('student_id');

        return view('guru.attendance.index', compact('students', 'attendances', 'date', 'group'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'guru') {
            abort(403);
        }

        $validated = $request->validate([
            'date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*.status' => 'required|in:hadir,izin,sakit,alpa',
            'attendance.*.note' => 'nullable|string',
        ]);

        $date = $validated['date'];
        foreach ($validated['attendance'] as $studentId => $data) {
            Attendance::updateOrCreate(
                ['student_id' => $studentId, 'date' => $date],
                ['status' => $data['status'], 'note' => $data['note'] ?? null, 'marked_by' => $user->id]
            );
        }

        return back()->with('success', 'Absensi berhasil disimpan.');
    }

    public function update(Request $request, Attendance $attendance)
    {
        $user = Auth::user();
        if ($user->role !== 'guru') abort(403);

        $validated = $request->validate([
            'status' => 'required|in:hadir,izin,sakit,alpa',
            'note' => 'nullable|string',
        ]);

        $attendance->update(array_merge($validated, ['marked_by' => $user->id]));

        return back()->with('success', 'Absensi diperbarui.');
    }
}
