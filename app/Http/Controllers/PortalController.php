<?php

namespace App\Http\Controllers;

use App\Models\AnecdotalNote;
use App\Models\Artwork;
use App\Models\ChecklistAssessment;
use App\Models\DailyAssessment;
use App\Models\DevelopmentReport;
use App\Models\ParentProfile;
use App\Models\SchoolActivity;
use App\Models\SchoolAnnouncement;
use App\Models\Student;
use App\Models\SchoolAgenda;
use App\Models\User;
use App\Models\Attendance;
use App\Support\RfidCode;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class PortalController extends Controller
{
    public function login(): View
    {
        return view('auth.login');
    }

    public function handleLogin(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
            'role' => ['required', 'in:guru,wali_murid'],
        ]);

        // Cari user berdasarkan email atau name (tanpa filter role dulu)
        $user = User::query()
            ->where(function ($query) use ($validated) {
                $query->where('email', $validated['username'])
                    ->orWhere('name', $validated['username']);
            })
            ->first();

        // 1. Cek apakah user ada
        if (! $user) {
            return back()
                ->withInput($request->only('username', 'role'))
                ->withErrors(['username' => 'Akun tidak ditemukan.']);
        }

        // 2. Cek apakah password benar
        if (! Hash::check($validated['password'], $user->password)) {
            return back()
                ->withInput($request->only('username', 'role'))
                ->withErrors(['username' => 'Kata sandi salah.']);
        }

        // 3. Cek apakah role sesuai dengan yang dipilih di form
        if ($user->role !== $validated['role']) {
            return back()
                ->withInput($request->only('username', 'role'))
                ->withErrors(['username' => 'Role tidak sesuai.']);
        }

        // 4. Lakukan login
        Auth::login($user);

        // 5. Redirect berdasarkan role
        if ($user->role === 'guru') {
            return redirect()->route('guru.dashboard');
        }

        return redirect()->route('wali.dashboard');
    }


    public function logout(): RedirectResponse
    {
        Auth::logout();

        return redirect()->route('auth.login');
    }

    public function settings(): View
    {
        $user = Auth::user();
        return view('guru.settings.index', compact('user'));
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->update($validated);

        return back()->with('success_profile', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password'          => 'required',
            'password'                  => 'required|min:8|confirmed',
            'password_confirmation'     => 'required',
        ]);

        $user = Auth::user();

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak cocok.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success_password', 'Password berhasil diperbarui.');
    }

    public function teacherDashboard(): View
    {
        $teacherName = Auth::user()->name;
        $totalStudents = Student::count();
        $today = now()->toDateString();
        $todayAttendances = Attendance::query()
            ->whereDate('date', $today)
            ->get();
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

        $upcomingAgendas = SchoolAgenda::query()
            ->where(function($q) {
                $q->where('event_date', '>=', now()->toDateString())
                  ->orWhere('end_date', '>=', now()->toDateString());
            })
            ->orderBy('event_date')
            ->limit(4)
            ->get();

        return view('guru.dashboard.index', compact(
            'teacherName',
            'totalStudents',
            'upcomingAgendas',
            'attendanceCounts',
            'recordedAttendanceCount',
            'attendancePercent'
        ));
    }

    public function studentList(Request $request): View
    {
        if (! $this->tableReady(['students'])) {
            return view('guru.students.index', ['students' => collect()]);
        }

        $query = Student::with('parentProfile')->orderBy('full_name');

        if ($request->filled('group')) {
            $query->where('class_group', $request->group);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('student_no', 'like', "%{$search}%")
                  ->orWhere('rfid_code', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        $students = $query->get();

        return view('guru.students.index', compact('students'));
    }

    public function studentForm(): View
    {
        return view('guru.students.create');
    }

    public function parentDashboard(): View|RedirectResponse
    {
        $user = Auth::user();
        // Redirect Guru if they accidentally access Wali routes
        if ($user->role === 'guru') {
            return redirect()->route('guru.dashboard');
        }

        $student = $user->student;
        if (!$student) {
            Auth::logout();
            return redirect()->route('login')->withErrors(['username' => 'Akun Anda belum terhubung dengan data siswa.']);
        }

        $guardian = $student->parentProfile;
        $latestReport = DevelopmentReport::query()->where('student_id', $student->id)->latest()->first();
        $announcement = SchoolAnnouncement::query()->latest('published_on')->first();
        $upcomingAgendas = SchoolAgenda::query()
            ->where('is_public', true)
            ->where(function($q) {
                $q->where('event_date', '>=', now()->toDateString())
                  ->orWhere('end_date', '>=', now()->toDateString());
            })
            ->orderBy('event_date')
            ->limit(3)
            ->get();

        // attendance this week (Mon-Fri)
        $attendancePercent = null;
        $weekAttendances = collect();
        $todayAttendance = null;
        if ($student) {
            $weekStart = now()->startOfWeek();
            $weekEnd = $weekStart->copy()->addDays(4);
            $weekAttendances = Attendance::where('student_id', $student->id)
                ->whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()])
                ->get();
            $presentCount = $weekAttendances->where('status', 'hadir')->count();
            $expectedDays = 5;
            $attendancePercent = $expectedDays > 0 ? round($presentCount / $expectedDays * 100) : null;
            $todayAttendance = $weekAttendances->first(fn($item) => $item->date->isToday());
        }

        return view('wali_murid.dashboard', compact(
            'guardian',
            'student',
            'latestReport',
            'announcement',
            'upcomingAgendas',
            'attendancePercent',
            'weekAttendances',
            'todayAttendance'
        ));
    }

    public function parentAttendance()
    {
        $guardian = Auth::user();
        if ($guardian->role === 'guru') {
            return redirect()->route('guru.dashboard');
        }

        $student = $guardian->student;
        if (! $student) {
            return redirect()->route('wali.dashboard')->with('error', 'Siswa terkait tidak ditemukan.');
        }

        $attendances = Attendance::where('student_id', $student->id)
            ->orderByDesc('date')
            ->paginate(20);

        $weekStart = now()->startOfWeek();
        $weekEnd = $weekStart->copy()->addDays(4);
        $weekAttendances = Attendance::where('student_id', $student->id)
            ->whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->get();
        $presentCount = $weekAttendances->where('status', 'hadir')->count();
        $expectedDays = 5;
        $attendancePercent = $expectedDays > 0 ? round($presentCount / $expectedDays * 100) : null;
        $statusCounts = [
            'hadir' => $attendances->getCollection()->where('status', 'hadir')->count(),
            'izin' => $attendances->getCollection()->where('status', 'izin')->count(),
            'sakit' => $attendances->getCollection()->where('status', 'sakit')->count(),
            'alpa' => $attendances->getCollection()->where('status', 'alpa')->count(),
        ];

        return view('wali_murid.attendance.index', compact(
            'guardian',
            'student',
            'attendances',
            'attendancePercent',
            'weekAttendances',
            'statusCounts'
        ));
    }

    public function studentProfile(): View|RedirectResponse
    {
        $guardian = Auth::user();
        if ($guardian->role === 'guru') {
            return redirect()->route('guru.dashboard');
        }

        if (! $this->tableReady(['students', 'parent_profiles'])) {
            return view('wali_murid.profile.index', [
                'student' => null,
                'parentProfile' => null,
            ]);
        }

        $student = $guardian->student?->load('parentProfile');

        return view('wali_murid.profile.index', [
            'student' => $student,
            'parentProfile' => $student?->parentProfile,
        ]);
    }

    public function dailyAssessment(Request $request): View
    {
        if (! $this->tableReady(['students', 'daily_assessments'])) {
            return view('guru.daily-assessments.index', [
                'students' => collect(),
                'assessmentsByStudent' => collect(),
                'weeklyAssessments' => collect(),
                'date' => now(),
                'group' => null,
                'selectedAspect' => 'Nilai Agama & Moral',
                'activity' => '',
            ]);
        }

        $date = $request->date('date') ?: now();
        $group = $request->input('group');
        $search = $request->input('search');

        $query = Student::query()->orderBy('full_name');
        
        if ($group) {
            $query->where('class_group', $group);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('nickname', 'like', "%{$search}%");
            });
        }

        $students = $query->get();

        // 1. Determine selected aspect (Theme aspect of the day)
        // Iso day of week: 1 (Mon) - 7 (Sun)
        $dayOfWeek = $date->dayOfWeekIso;
        $defaultAspect = match($dayOfWeek) {
            1 => 'Nilai Agama & Moral',
            2 => 'Fisik Motorik',
            3 => 'Kognitif',
            4 => 'Bahasa',
            5 => 'Sosial Emosional',
            default => 'Seni'
        };
        
        $selectedAspect = $request->input('aspect', $defaultAspect);

        // 2. Fetch existing assessments for this specific date and selected aspect
        $assessmentsByStudent = DailyAssessment::query()
            ->whereDate('assessed_on', $date->toDateString())
            ->where('aspect_name', $selectedAspect)
            ->get()
            ->keyBy('student_id');

        // 3. Fetch existing activity name for this day and aspect
        $existingAssessment = DailyAssessment::query()
            ->whereDate('assessed_on', $date->toDateString())
            ->where('aspect_name', $selectedAspect)
            ->first();
        $activity = $existingAssessment ? $existingAssessment->activity : '';

        // 4. Fetch ALL assessments for the current week to show in the Weekly Recap Table!
        // Week range: Monday (startOfWeek) through Friday (startOfWeek + 4 days)
        $startOfWeek = $date->copy()->startOfWeek();
        $endOfWeek = $startOfWeek->copy()->addDays(4);
        
        $weeklyAssessments = DailyAssessment::query()
            ->whereBetween('assessed_on', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
            ->get()
            ->groupBy('student_id');

        return view('guru.daily-assessments.index', compact(
            'students', 
            'assessmentsByStudent', 
            'weeklyAssessments',
            'date', 
            'group', 
            'selectedAspect',
            'activity',
            'startOfWeek',
            'endOfWeek'
        ));
    }

    public function storeDailyAssessment(Request $request): RedirectResponse
    {
        $date = $request->input('date', now()->toDateString());
        $aspectName = $request->input('aspect');
        $activity = $request->input('activity', 'Kegiatan Harian') ?: 'Kegiatan Harian';
        
        $scores = $request->input('scores', []); // Structure: [student_id => score]
        $observations = $request->input('observations', []); // Structure: [student_id => observation]

        foreach ($scores as $studentId => $score) {
            $student = Student::find($studentId);
            if (! $student) continue;

            $observation = $observations[$studentId] ?? null;

            if (empty($score)) {
                // If the score is empty, we delete the assessment for this student, day, and aspect
                DailyAssessment::query()
                    ->where('student_id', $studentId)
                    ->whereDate('assessed_on', $date)
                    ->where('aspect_name', $aspectName)
                    ->delete();
                continue;
            }

            DailyAssessment::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'assessed_on' => $date,
                    'aspect_name' => $aspectName,
                ],
                [
                    'class_group' => $student->class_group,
                    'activity' => $activity,
                    'aspect_code' => match($aspectName) {
                        'Nilai Agama & Moral' => 'NAM',
                        'Fisik Motorik' => 'FM',
                        'Kognitif' => 'KOG',
                        'Bahasa' => 'BHS',
                        'Sosial Emosional' => 'SEM',
                        default => 'SENI'
                    },
                    'score_label' => $score,
                    'score_value' => match($score) {
                        'BB' => 1,
                        'MB' => 2,
                        'BSH' => 3,
                        'BSB' => 4,
                        default => 0
                    },
                    'observation' => $observation
                ]
            );
        }

        return back()->with('success', 'Penilaian harian berhasil disimpan.');
    }
    public function checklistAssessment(Request $request): View
    {
        if (! $this->tableReady(['students', 'checklist_assessments'])) {
            return view('guru.checklist-assessments.index', [
                'students' => collect(),
                'assessmentsByStudent' => collect(),
                'date' => now(),
                'group' => null
            ]);
        }

        $date = $request->date('date') ?: now();
        $group = $request->input('group');

        $students = Student::query()
            ->when($group, fn($q) => $q->where('class_group', $group))
            ->orderBy('full_name')
            ->get();

        $assessmentsByStudent = ChecklistAssessment::query()
            ->whereDate('assessed_on', $date->toDateString())
            ->get()
            ->groupBy('student_id');

        return view('guru.checklist-assessments.index', compact('students', 'assessmentsByStudent', 'date', 'group'));
    }

    public function storeChecklistAssessment(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'assessments' => 'required|array',
        ]);

        $date = $validated['date'];
        
        $domainNames = [
            'NAM'  => 'Nilai Agama & Moral',
            'FM'   => 'Fisik Motorik',
            'KOG'  => 'Kognitif',
            'BHS'  => 'Bahasa',
            'SOSEM' => 'Sosial Emosional',
            'SENI' => 'Seni'
        ];

        foreach ($validated['assessments'] as $studentId => $domains) {
            foreach ($domains as $domainCode => $scoreLabel) {
                ChecklistAssessment::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'assessed_on' => $date,
                        'domain_code' => $domainCode,
                    ],
                    [
                        'domain_name' => $domainNames[$domainCode] ?? $domainCode,
                        'indicator' => 'Aspek ' . ($domainNames[$domainCode] ?? $domainCode),
                        'score_label' => $scoreLabel,
                        'score_value' => match($scoreLabel) {
                            'BB' => 1, 'MB' => 2, 'BSH' => 3, 'BSB' => 4, default => 0
                        }
                    ]
                );
            }
        }

        return back()->with('success', 'Penilaian ceklis berhasil disimpan.');
    }

    public function anecdotalNotes(): View
    {
        if (! $this->tableReady(['anecdotal_notes', 'students'])) {
            return view('guru.anecdotal-notes.index', [
                'notes' => collect(),
                'students' => collect(),
            ]);
        }

        $students = Student::query()->orderBy('full_name')->get(['id', 'full_name', 'nickname']);
        $notes = AnecdotalNote::query()
            ->with('student:id,full_name,nickname,avatar_url,class_group')
            ->latest('recorded_at')
            ->limit(10)
            ->get();

        return view('guru.anecdotal-notes.index', compact('notes', 'students'));
    }

    public function artworkAssessment(Request $request): View
    {
        if (! $this->tableReady(['artworks', 'students'])) {
            return view('guru.artworks.index', [
                'students' => collect(),
                'artworks' => collect(),
                'date' => now(),
                'group' => null
            ]);
        }

        $students = Student::query()->orderBy('full_name')->get();
        $date = $request->date('date') ?: now();
        $group = $request->input('group');

        $query = \App\Models\Artwork::query()
            ->with('student')
            ->when($date, fn($q) => $q->whereDate('created_on', $date->toDateString()))
            ->latest('id');

        if ($group) {
            $query->whereHas('student', fn($q) => $q->where('class_group', $group));
        }

        $artworks = $query->get();

        return view('guru.artworks.index', compact('students', 'artworks', 'date', 'group'));
    }

    public function storeArtwork(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'artwork_id' => 'nullable|exists:artworks,id',
            'student_id' => 'required|exists:students,id',
            'created_on' => 'required|date',
            'activity' => 'required|string|max:255',
            'aspect' => 'required|string|max:255',
            'score_label' => 'required|in:BB,MB,BSH,BSB',
            'image' => 'nullable|image|max:5120',
        ]);

        $data = [
            'student_id' => $validated['student_id'],
            'created_on' => $validated['created_on'],
            'title' => $validated['activity'],
            'description' => $validated['aspect'],
            'score_label' => $validated['score_label'],
            'status' => 'published',
            'score_value' => match($validated['score_label']) {
                'BB' => 1, 'MB' => 2, 'BSH' => 3, 'BSB' => 4, default => 0
            }
        ];

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('artworks', 'public');
            $data['image_url'] = '/storage/' . $imagePath;
        }

        if ($request->artwork_id) {
            \App\Models\Artwork::where('id', $request->artwork_id)->update($data);
            $msg = 'Penilaian hasil karya berhasil diperbarui.';
        } else {
            \App\Models\Artwork::create($data);
            $msg = 'Penilaian hasil karya berhasil disimpan.';
        }

        return back()->with('success', $msg);
    }

    public function destroyArtwork(\App\Models\Artwork $artwork)
    {
        if ($artwork->image_url) {
            $path = str_replace('/storage/', '', $artwork->image_url);
            \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
        }
        $artwork->delete();
        return back()->with('success', 'Hasil karya berhasil dihapus.');
    }

    public function assessmentPanel(Request $request): View
    {
        if (! $this->tableReady(['conversation_assessments', 'students'])) {
            return view('guru.development-reports.index', [
                'students' => collect(),
                'assessments' => collect(),
            ]);
        }

        $students = Student::query()->orderBy('full_name')->get();
        
        $date = $request->date('date') ?: now();
        $group = $request->input('group');

        $query = \App\Models\ConversationAssessment::query()
            ->with('student')
            ->when($date, fn($q) => $q->whereDate('assessed_on', $date->toDateString()))
            ->latest('id');

        if ($group) {
            $query->whereHas('student', fn($q) => $q->where('class_group', $group));
        }

        $assessments = $query->get();

        return view('guru.development-reports.index', compact('students', 'assessments', 'date', 'group'));
    }

    public function storeConversationAssessment(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'assessment_id' => 'nullable|exists:conversation_assessments,id',
            'student_id' => 'required|exists:students,id',
            'assessed_on' => 'required|date',
            'activity' => 'required|string|max:255',
            'aspect' => 'required|string|max:255',
            'score_label' => 'required|in:BB,MB,BSH,BSB',
        ]);

        if ($request->assessment_id) {
            \App\Models\ConversationAssessment::where('id', $request->assessment_id)->update($validated);
            $msg = 'Penilaian percakapan berhasil diperbarui.';
        } else {
            \App\Models\ConversationAssessment::create($validated);
            $msg = 'Penilaian percakapan berhasil disimpan.';
        }

        return back()->with('success', $msg);
    }

    public function destroyConversationAssessment(\App\Models\ConversationAssessment $assessment)
    {
        $assessment->delete();
        return back()->with('success', 'Penilaian percakapan berhasil dihapus.');
    }

    
    

    public function parentReport()
    {
        $user = Auth::user();
        if ($user->role === 'guru') return redirect()->route('guru.dashboard');

        $student = $user->student;
        if (!$student) return redirect()->route('wali.dashboard');

        // 1. Ringkasan (Development Report)
        $report = DevelopmentReport::query()->where('student_id', $student->id)->latest()->first();

        // 2. Harian (Daily Assessments) - Grouped by Week (reliable Y-m-d|Y-m-d key)
        $dailyAssessments = DailyAssessment::query()
            ->where('student_id', $student->id)
            ->orderBy('assessed_on', 'asc')
            ->get()
            // Exclude weekend entries (Saturday=6, Sunday=7)
            ->filter(function($item) { return $item->assessed_on->dayOfWeekIso >= 1 && $item->assessed_on->dayOfWeekIso <= 5; })
            ->groupBy(function($item) {
                $date  = $item->assessed_on;
                $start = $date->copy()->startOfWeek(); // Monday
                $end   = $start->copy()->addDays(4);   // Friday
                return $start->format('Y-m-d') . '|' . $end->format('Y-m-d');
            })
            ->sortKeysDesc(); // newest week first

        // 3. Ceklis (Checklist Assessments)
        $checklistAssessments = ChecklistAssessment::query()
            ->where('student_id', $student->id)
            ->get()
            ->groupBy('domain_name');

        // 4. Percakapan (Conversation Assessments)
        $conversationAssessments = \App\Models\ConversationAssessment::query()
            ->where('student_id', $student->id)
            ->latest('assessed_on')
            ->get();

        // 5. Anekdot (Anecdotal Notes)
        $anecdotalNotes = AnecdotalNote::query()
            ->where('student_id', $student->id)
            ->latest('recorded_at')
            ->get();
            
        // 6. Hasil Karya (Artworks)
        $artworks = Artwork::query()
            ->where('student_id', $student->id)
            ->latest('created_on')
            ->get();

        return view('wali_murid.reports.index', compact(
            'student', 
            'report', 
            'dailyAssessments', 
            'checklistAssessments', 
            'conversationAssessments', 
            'anecdotalNotes', 
            'artworks'
        ));
    }

    public function parentGallery()
    {
        $user = Auth::user();
        if ($user->role === 'guru') return redirect()->route('guru.dashboard');

        $student = $user->student;
        if (!$student) return redirect()->route('wali.dashboard');

        $artworks = Artwork::query()->where('student_id', $student->id)->latest()->get();
        $notes = AnecdotalNote::query()->where('student_id', $student->id)->latest()->get();

        return view('wali_murid.gallery.index', compact('student', 'artworks', 'notes'));
    }

    public function storeStudent(Request $request): RedirectResponse
    {
        if (! $this->tableReady(['students', 'parent_profiles'])) {
            return back()->with('error', 'Tabel belum siap. Jalankan migrate terlebih dahulu.');
        }

        $request->merge([
            'rfid_code' => RfidCode::normalize($request->input('rfid_code')),
        ]);

        $validated = $request->validate([
            'student_no' => ['required', 'string', 'max:50'],
            'rfid_code' => ['nullable', 'string', 'max:64'],
            'nisn' => ['nullable', 'string', 'max:50'],
            'nik' => ['nullable', 'string', 'max:32'],
            'full_name' => ['required', 'string', 'max:255'],
            'class_group' => ['required', 'string', 'max:100'],
            'school_year' => ['required', 'string', 'max:20'],
            'guardian_name' => ['required', 'string', 'max:255'],
            'guardian_phone' => ['nullable', 'string', 'max:50'],
            'parent_email' => ['nullable', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'max:2048'], // Max 2MB
        ], $this->studentValidationMessages());

        $duplicateNotices = array_merge(
            $this->studentIdentityDuplicateNotices($request),
            $this->parentAccountDuplicateNotices($request)
        );

        if ($duplicateNotices && ! $request->boolean('confirm_duplicate_save')) {
            return back()
                ->withInput()
                ->with('duplicate_confirmation_notices', $duplicateNotices);
        }

        $avatarUrl = null;
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $avatarUrl = '/storage/' . $path;
        }

        try {
            $student = Student::query()->create([
                'student_no' => $validated['student_no'],
                'rfid_code' => $validated['rfid_code'] ?? null,
                'full_name' => $validated['full_name'],
                'class_group' => $validated['class_group'],
                'school_year' => $validated['school_year'],
                'nickname' => $request->string('nickname')->value(),
                'nisn' => $request->string('nisn')->value(),
                'nik' => $request->string('nik')->value(),
                'birth_place' => $request->string('birth_place')->value(),
                'birth_date' => $request->filled('birth_date') ? $request->date('birth_date') : null,
                'gender' => $request->string('gender')->value(),
                'religion' => $request->string('religion')->value(),
                'address' => $request->string('address')->value(),
                'phone_number' => $request->string('phone_number')->value(),
                'sibling_order' => $request->input('sibling_order'),
                'siblings_total' => $request->input('siblings_total'),
                'avatar_url' => $avatarUrl,
            ]);
        } catch (QueryException $e) {
            return back()
                ->withInput()
                ->withErrors($this->duplicateStudentErrors($e));
        }


        ParentProfile::query()->create([
            'student_id' => $student->id,
            'guardian_name' => $validated['guardian_name'],
            'guardian_phone' => $validated['guardian_phone'] ?? null,
            'father_name' => $request->string('father_name')->value(),
            'father_nik' => $request->string('father_nik')->value(),
            'mother_name' => $request->string('mother_name')->value(),
            'mother_nik' => $request->string('mother_nik')->value(),
            'father_job' => $request->string('father_job')->value(),
            'mother_job' => $request->string('mother_job')->value(),
        ]);

        // Handle Parent Account (Wali Murid)
        if ($request->filled('parent_email')) {
            $email = $request->string('parent_email')->value();
            $password = $request->string('parent_password')->value();

            $existingUser = User::query()->where('email', $email)->first();
            $user = null;

            if ($existingUser?->role === 'wali_murid') {
                $user = $existingUser;
            } elseif (! $existingUser) {
                $user = User::create([
                    'name' => 'Wali ' . $student->full_name,
                    'email' => $email,
                    'password' => Hash::make($password ?: 'password123'),
                    'role' => 'wali_murid',
                ]);
            }

            if ($user) {
                $student->update(['user_id' => $user->id]);
            }
        }

        return redirect()
            ->route('guru.students.index')
            ->with('success', 'Data siswa berhasil ditambahkan.')
            ->with('duplicate_identity_notices', $duplicateNotices);
    }

    public function editStudent(Student $student): View
    {
        $student->load('parentProfile');
        return view('guru.students.edit', compact('student'));
    }

    public function updateStudent(Request $request, Student $student): RedirectResponse
    {
        $request->merge([
            'rfid_code' => RfidCode::normalize($request->input('rfid_code')),
        ]);

        $validated = $request->validate([
            'student_no' => ['required', 'string', 'max:50'],
            'rfid_code' => ['nullable', 'string', 'max:64'],
            'nisn' => ['nullable', 'string', 'max:50'],
            'nik' => ['nullable', 'string', 'max:32'],
            'full_name' => ['required', 'string', 'max:255'],
            'class_group' => ['required', 'string', 'max:100'],
            'school_year' => ['required', 'string', 'max:20'],
            'guardian_name' => ['required', 'string', 'max:255'],
            'parent_email' => ['nullable', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ], $this->studentValidationMessages());

        $duplicateNotices = array_merge(
            $this->studentIdentityDuplicateNotices($request, $student->id),
            $this->parentAccountDuplicateNotices($request, $student->user_id)
        );

        if ($duplicateNotices && ! $request->boolean('confirm_duplicate_save')) {
            return back()
                ->withInput()
                ->with('duplicate_confirmation_notices', $duplicateNotices);
        }

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $student->avatar_url = '/storage/' . $path;
        }

        try {
            $student->update([
                'student_no' => $validated['student_no'],
                'rfid_code' => $validated['rfid_code'] ?? null,
                'full_name' => $validated['full_name'],
                'class_group' => $validated['class_group'],
                'school_year' => $validated['school_year'],
                'nickname' => $request->string('nickname')->value(),
                'nisn' => $request->string('nisn')->value(),
                'nik' => $request->string('nik')->value(),
                'birth_place' => $request->string('birth_place')->value(),
                'birth_date' => $request->filled('birth_date') ? $request->date('birth_date') : null,
                'gender' => $request->string('gender')->value(),
                'religion' => $request->string('religion')->value(),
                'address' => $request->string('address')->value(),
                'phone_number' => $request->string('phone_number')->value(),
                'sibling_order' => $request->input('sibling_order'),
                'siblings_total' => $request->input('siblings_total'),
                'avatar_url' => $student->avatar_url,
            ]);
        } catch (QueryException $e) {
            return back()
                ->withInput()
                ->withErrors($this->duplicateStudentErrors($e));
        }

        $student->parentProfile()->update([
            'guardian_name' => $validated['guardian_name'],
            'guardian_phone' => $request->string('guardian_phone')->value(),
            'father_name' => $request->string('father_name')->value(),
            'father_nik' => $request->string('father_nik')->value(),
            'mother_name' => $request->string('mother_name')->value(),
            'mother_nik' => $request->string('mother_nik')->value(),
            'father_job' => $request->string('father_job')->value(),
            'mother_job' => $request->string('mother_job')->value(),
        ]);

        // Handle Parent Account (Wali Murid)
        if ($request->filled('parent_email')) {
            $email = $request->string('parent_email')->value();
            $password = $request->string('parent_password')->value();

            $existingUser = User::query()
                ->where('email', $email)
                ->when($student->user_id, fn ($query) => $query->whereKeyNot($student->user_id))
                ->first();

            if ($existingUser && $existingUser->role === 'wali_murid') {
                $student->update(['user_id' => $existingUser->id]);
            } elseif ($existingUser) {
                $student->update(['user_id' => null]);
            } elseif ($student->user_id) {
                // Update existing user
                $user = User::find($student->user_id);
                if ($user) {
                    $userData = ['email' => $email, 'name' => 'Wali ' . $student->full_name];
                    if ($password) {
                        $userData['password'] = Hash::make($password);
                    }
                    $user->update($userData);
                } else {
                    $user = User::create([
                        'name' => 'Wali ' . $student->full_name,
                        'email' => $email,
                        'password' => Hash::make($password ?: 'password123'),
                        'role' => 'wali_murid',
                    ]);
                    $student->update(['user_id' => $user->id]);
                }
            } else {
                // Create new user
                $user = User::create([
                    'name' => 'Wali ' . $student->full_name,
                    'email' => $email,
                    'password' => Hash::make($password ?: 'password123'),
                    'role' => 'wali_murid',
                ]);
                $student->update(['user_id' => $user->id]);
            }
        }

        return redirect()
            ->route('guru.students.index')
            ->with('success', 'Data siswa berhasil diperbarui.')
            ->with('duplicate_identity_notices', $duplicateNotices);
    }

    public function destroyStudent(Student $student): RedirectResponse
    {
        $student->delete();
        return redirect()->route('guru.students.index')->with('success', 'Data siswa berhasil dihapus.');
    }

    public function quickUpdateGroup(Request $request, Student $student): RedirectResponse
    {
        $validated = $request->validate([
            'class_group' => ['required', 'string', 'max:100'],
        ]);

        $student->update(['class_group' => $validated['class_group']]);

        return back()->with('success', 'Kelompok ' . $student->full_name . ' berhasil diubah.');
    }

    public function storeAnecdotal(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'recorded_at' => ['required', 'date'],
            'location' => ['nullable', 'string', 'max:255'],
            'tone' => ['nullable', 'string', 'max:24'],
            'description' => ['required', 'string'],
        ]);

        AnecdotalNote::query()->create($validated);

        return redirect()->route('guru.anecdotal')->with('success', 'Catatan anekdot berhasil disimpan.');
    }

    public function teacherAgenda(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        $date = \Carbon\Carbon::create($year, $month, 1);

        $agendas = SchoolAgenda::query()
            ->whereMonth('event_date', $month)
            ->whereYear('event_date', $year)
            ->get();

        return view('guru.agenda.index', compact('agendas', 'date', 'month', 'year'));
    }

    public function storeAgenda(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'event_date' => ['required', 'date'],
            'type' => ['required', 'in:libur,kegiatan,ujian,pengumuman,lainnya'],
        ]);

        SchoolAgenda::create([
            'title' => $request->title,
            'description' => $request->description,
            'event_date' => $request->event_date,
            'end_date' => $request->end_date ?: null,
            'type' => $request->type,
            'is_public' => $request->has('is_public'),
            'created_by' => Auth::id(),
            'color' => $request->color,
        ]);

        return back()->with('success', 'Agenda berhasil ditambahkan.');
    }

    public function updateAgenda(Request $request, SchoolAgenda $agenda): RedirectResponse
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'event_date' => ['required', 'date'],
            'type' => ['required', 'in:libur,kegiatan,ujian,pengumuman,lainnya'],
        ]);

        $agenda->update([
            'title' => $request->title,
            'description' => $request->description,
            'event_date' => $request->event_date,
            'end_date' => $request->end_date ?: null,
            'type' => $request->type,
            'is_public' => $request->has('is_public'),
            'color' => $request->color,
        ]);

        return back()->with('success', 'Agenda berhasil diperbarui.');
    }

    public function destroyAgenda(SchoolAgenda $agenda): RedirectResponse
    {
        $agenda->delete();
        return back()->with('success', 'Agenda berhasil dihapus.');
    }

    public function parentAgenda(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        $date = \Carbon\Carbon::create($year, $month, 1);
        $startDate = $date->copy()->startOfMonth();
        $endDate = $date->copy()->endOfMonth();

        $agendas = SchoolAgenda::query()
            ->where('is_public', true)
            ->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('event_date', [$startDate, $endDate])
                  ->orWhereBetween('end_date', [$startDate, $endDate])
                  ->orWhere(function($sq) use ($startDate, $endDate) {
                      $sq->where('event_date', '<=', $startDate)
                        ->where('end_date', '>=', $endDate);
                  });
            })
            ->get();

        return view('wali_murid.agenda.index', compact('agendas', 'date', 'month', 'year'));
    }

    private function studentValidationMessages(): array
    {
        return [
            'student_no.unique' => 'No Induk sudah dipakai oleh siswa lain.',
            'nisn.unique' => 'NISN sudah dipakai oleh siswa lain.',
            'nik.unique' => 'NIK anak sudah dipakai oleh siswa lain.',
            'rfid_code.unique' => 'Kode RFID sudah dipakai oleh siswa lain.',
            'parent_email.unique' => 'Username / Email wali murid sudah dipakai oleh akun lain.',
            'student_no.required' => 'No Induk wajib diisi.',
            'full_name.required' => 'Nama lengkap wajib diisi.',
            'class_group.required' => 'Kelompok wajib dipilih.',
            'school_year.required' => 'Tahun pelajaran wajib diisi.',
            'guardian_name.required' => 'Nama wali murid wajib diisi.',
        ];
    }

    private function studentIdentityDuplicateNotices(Request $request, ?int $ignoreStudentId = null): array
    {
        $identityFields = [
            'student_no' => 'No Induk',
            'rfid_code' => 'Kode RFID',
            'nisn' => 'NISN',
            'nik' => 'NIK anak',
        ];
        $notices = [];

        foreach ($identityFields as $field => $label) {
            $value = trim((string) $request->input($field));
            if ($value === '') {
                continue;
            }

            $matches = Student::query()
                ->where($field, $value)
                ->when($ignoreStudentId, fn ($query) => $query->whereKeyNot($ignoreStudentId))
                ->limit(3)
                ->get(['id', 'full_name', 'class_group']);

            foreach ($matches as $match) {
                $notices[] = "{$label} {$value} sama dengan identitas anak {$match->full_name} ({$match->class_group}).";
            }
        }

        return $notices;
    }

    private function parentAccountDuplicateNotices(Request $request, ?int $ignoreUserId = null): array
    {
        $email = trim((string) $request->input('parent_email'));
        if ($email === '') {
            return [];
        }

        $user = User::query()
            ->where('email', $email)
            ->when($ignoreUserId, fn ($query) => $query->whereKeyNot($ignoreUserId))
            ->first(['id', 'name', 'email', 'role']);

        if (! $user) {
            return [];
        }

        if ($user->role === 'wali_murid') {
            return ["Username / Email wali {$email} sudah dipakai oleh akun {$user->name}, data tetap disimpan dan siswa dihubungkan ke akun tersebut."];
        }

        return ["Username / Email wali {$email} sudah dipakai oleh akun {$user->name}, data siswa tetap disimpan tetapi tidak dihubungkan ke akun tersebut."];
    }

    private function duplicateStudentErrors(QueryException $e): array
    {
        $message = $e->getMessage();

        if (str_contains($message, 'students_student_no_unique')) {
            return ['student_no' => 'No Induk sudah dipakai oleh siswa lain.'];
        }

        if (str_contains($message, 'students_nisn_unique')) {
            return ['nisn' => 'NISN sudah dipakai oleh siswa lain.'];
        }

        if (str_contains($message, 'students_nik_unique')) {
            return ['nik' => 'NIK anak sudah dipakai oleh siswa lain.'];
        }

        if (str_contains($message, 'students_rfid_code_unique')) {
            return ['rfid_code' => 'Kode RFID sudah dipakai oleh siswa lain.'];
        }

        if (str_contains($message, 'users_email_unique')) {
            return ['parent_email' => 'Username / Email wali murid sudah dipakai oleh akun lain.'];
        }

        return ['student_no' => 'Data duplikat terdeteksi. Periksa No Induk, NISN, Kode RFID, atau akun wali murid.'];
    }

    private function tableReady(array $tables): bool
    {
        foreach ($tables as $table) {
            if (! Schema::hasTable($table)) {
                return false;
            }
        }

        return true;
    }
}
