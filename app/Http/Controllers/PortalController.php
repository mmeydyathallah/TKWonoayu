<?php

namespace App\Http\Controllers;

use App\Models\AnecdotalNote;
use App\Models\Artwork;
use App\Models\DailyLearningReport;
use App\Models\DailyLearningReportPhoto;
use App\Models\DevelopmentReport;
use App\Models\GuardianTelegramChat;
use App\Models\ParentProfile;
use App\Models\SchoolActivity;
use App\Models\SchoolAnnouncement;
use App\Models\Student;
use App\Models\SchoolAgenda;
use App\Models\User;
use App\Models\Attendance;
use App\Support\AttendanceSchedule;
use App\Support\PhoneNumber;
use App\Support\RfidCode;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
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
        $attendanceSettings = AttendanceSchedule::settings();
        $attendanceSettings['check_in_window'] = AttendanceSchedule::windowLabel(
            $attendanceSettings['check_in_time'],
            $attendanceSettings['window_minutes']
        );
        $attendanceSettings['check_out_window'] = AttendanceSchedule::windowLabel(
            $attendanceSettings['check_out_time'],
            $attendanceSettings['window_minutes']
        );

        $telegramStats = [
            'connected_chats' => GuardianTelegramChat::query()->count(),
            'selected_student_chats' => GuardianTelegramChat::query()->whereNotNull('selected_student_id')->count(),
        ];

        $profilesByPhone = ParentProfile::query()
            ->with('student:id,full_name,class_group')
            ->get()
            ->filter(fn (ParentProfile $profile) => ! empty($profile->guardian_phone))
            ->groupBy(fn (ParentProfile $profile) => PhoneNumber::normalize($profile->guardian_phone));

        $telegramConnections = GuardianTelegramChat::query()
            ->with('selectedStudent:id,full_name,class_group')
            ->orderByDesc('updated_at')
            ->get()
            ->map(function (GuardianTelegramChat $chat) use ($profilesByPhone) {
                $matchedProfiles = $profilesByPhone->get($chat->phone_number_normalized, collect());
                $students = $matchedProfiles
                    ->pluck('student')
                    ->filter()
                    ->unique('id')
                    ->values();

                return [
                    'id' => $chat->id,
                    'phone' => $chat->phone_number_normalized,
                    'chat_id' => $chat->chat_id,
                    'telegram_username' => $chat->telegram_username,
                    'selected_student' => $chat->selectedStudent,
                    'students' => $students,
                    'updated_at' => $chat->updated_at,
                ];
            });

        return view('guru.settings.index', compact('user', 'attendanceSettings', 'telegramStats', 'telegramConnections'));
    }

    public function destroyTelegramConnection(GuardianTelegramChat $chat): RedirectResponse
    {
        $identifier = $chat->phone_number_normalized ?: 'Chat ID ' . $chat->chat_id;
        $chat->delete();

        return redirect()
            ->route('guru.settings', ['tab' => 'telegram'])
            ->with('success_telegram', 'Koneksi Telegram ' . $identifier . ' berhasil dihapus.');
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

    public function updateAttendanceSettings(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if ($user->role !== 'guru') {
            return redirect()->route('wali.dashboard');
        }

        $validated = $request->validate([
            'check_in_time' => ['required', 'date_format:H:i'],
            'check_out_time' => ['required', 'date_format:H:i', 'after:check_in_time'],
        ], [
            'check_in_time.required' => 'Jam masuk wajib diisi.',
            'check_in_time.date_format' => 'Format jam masuk tidak valid.',
            'check_out_time.required' => 'Jam pulang wajib diisi.',
            'check_out_time.date_format' => 'Format jam pulang tidak valid.',
            'check_out_time.after' => 'Jam pulang harus setelah jam masuk.',
        ]);

        AttendanceSchedule::save(
            $validated['check_in_time'],
            $validated['check_out_time'],
            AttendanceSchedule::DEFAULT_WINDOW_MINUTES
        );

        return back()->with('success_attendance', 'Pengaturan jam masuk dan pulang berhasil disimpan.');
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
        $latestReport = DevelopmentReport::query()
            ->where('student_id', $student->id)
            ->latest('updated_at')
            ->latest('id')
            ->first();
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

    public function parentTelegram(): View|RedirectResponse
    {
        $guardian = Auth::user();
        if ($guardian->role === 'guru') {
            return redirect()->route('guru.dashboard');
        }

        $student = $guardian->student?->load('parentProfile');
        if (! $student) {
            return redirect()->route('wali.dashboard')->with('error', 'Siswa terkait tidak ditemukan.');
        }

        $guardianPhoneNormalized = PhoneNumber::normalize($student->parentProfile?->guardian_phone);
        $telegramChat = null;

        if ($guardianPhoneNormalized) {
            $telegramChat = GuardianTelegramChat::query()
                ->where('phone_number_normalized', $guardianPhoneNormalized)
                ->first();
        }

        $isConnected = (bool) $telegramChat;
        $selectedStudent = $telegramChat?->selectedStudent;
        $isSelectedForThisStudent = $telegramChat
            ? ($telegramChat->selected_student_id === null || (int) $telegramChat->selected_student_id === (int) $student->id)
            : false;

        return view('wali_murid.telegram.index', compact(
            'student',
            'guardianPhoneNormalized',
            'telegramChat',
            'isConnected',
            'selectedStudent',
            'isSelectedForThisStudent'
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
        if (! $this->tableReady(['students', 'daily_learning_reports', 'daily_learning_report_photos', 'daily_learning_report_extracurriculars'])) {
            $recapDate = now();
            $startOfWeek = $recapDate->copy()->startOfWeek();
            $endOfWeek = $startOfWeek->copy()->addDays(4);

            return view('guru.daily-assessments.index', [
                'students' => collect(),
                'reportsByStudent' => collect(),
                'weeklyReports' => collect(),
                'date' => now(),
                'recapDate' => $recapDate,
                'group' => null,
                'search' => null,
                'selectedStudentId' => null,
                'selectedStudent' => null,
                'intrakurikulerDomains' => $this->intrakurikulerDomains(),
                'scoreOptions' => $this->scoreOptions(),
                'startOfWeek' => $startOfWeek,
                'endOfWeek' => $endOfWeek,
            ]);
        }

        $date = $request->date('date') ?: now();
        $recapDate = $request->date('week') ?: $date;
        $group = $request->input('group');
        $search = $request->input('search');
        $selectedStudentId = $request->integer('student_id') ?: null;

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
        $studentIds = $students->pluck('id');
        $selectedStudent = $selectedStudentId
            ? $students->firstWhere('id', $selectedStudentId)
            : null;

        if (! $selectedStudent) {
            $selectedStudentId = null;
        }

        $reportsByStudent = DailyLearningReport::query()
            ->with(['photos', 'extracurricularItems'])
            ->whereIn('student_id', $studentIds)
            ->whereDate('assessed_on', $date->toDateString())
            ->get()
            ->keyBy('student_id');

        $startOfWeek = $recapDate->copy()->startOfWeek();
        $endOfWeek = $startOfWeek->copy()->addDays(4);
        
        $weeklyReports = DailyLearningReport::query()
            ->with(['photos', 'extracurricularItems'])
            ->whereIn('student_id', $studentIds)
            ->whereBetween('assessed_on', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
            ->orderBy('assessed_on')
            ->get()
            ->groupBy('student_id');

        return view('guru.daily-assessments.index', compact(
            'students', 
            'reportsByStudent',
            'weeklyReports',
            'date', 
            'recapDate',
            'group',
            'search',
            'selectedStudentId',
            'selectedStudent',
            'startOfWeek',
            'endOfWeek'
        ) + [
            'intrakurikulerDomains' => $this->intrakurikulerDomains(),
            'scoreOptions' => $this->scoreOptions(),
        ]);
    }

    public function storeDailyAssessment(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'reports' => ['nullable', 'array'],
            'photos' => ['nullable', 'array'],
            'photos.*.*.*' => ['nullable', 'image', 'max:5120'],
        ]);

        $date = $validated['date'];
        $reports = $request->input('reports', []);
        $domains = $this->intrakurikulerDomains();
        $scores = array_keys($this->scoreOptions());
        $savedCount = 0;
        $deletedCount = 0;

        foreach ($reports as $studentId => $payload) {
            $student = Student::find($studentId);
            if (! $student) {
                continue;
            }

            $domainData = $payload['intrakurikuler'] ?? [];
            $hasFile = false;
            foreach (array_keys($domains) as $domainCode) {
                foreach ([1, 2] as $slot) {
                    $fileKey = "photos.$studentId.$domainCode.$slot";
                    if ($request->hasFile($fileKey)) {
                        $hasFile = true;
                    }
                }
            }

            $reportData = [
                'agama_budi_pekerti_score' => $this->normalizeScore($domainData['agama_budi_pekerti']['score_label'] ?? null),
                'agama_budi_pekerti_narrative' => $this->cleanNullableText($domainData['agama_budi_pekerti']['narrative'] ?? null),
                'jati_diri_score' => $this->normalizeScore($domainData['jati_diri']['score_label'] ?? null),
                'jati_diri_narrative' => $this->cleanNullableText($domainData['jati_diri']['narrative'] ?? null),
                'literasi_steam_score' => $this->normalizeScore($domainData['literasi_steam']['score_label'] ?? null),
                'literasi_steam_narrative' => $this->cleanNullableText($domainData['literasi_steam']['narrative'] ?? null),
                'kokurikuler_description' => $this->cleanNullableText($payload['kokurikuler_description'] ?? null),
            ];

            $extracurricularRows = $this->normalizeExtracurricularRows($payload['extracurriculars'] ?? []);
            $firstExtracurricular = $extracurricularRows[0] ?? null;
            $reportData['extracurricular_implementation'] = $firstExtracurricular['implementation'] ?? null;
            $reportData['extracurricular_activity'] = $firstExtracurricular['activity'] ?? null;
            $reportData['extracurricular_score_label'] = $firstExtracurricular['score_label'] ?? null;

            foreach (array_keys($domains) as $domainCode) {
                foreach ([1, 2] as $slot) {
                    $title = $this->cleanNullableText($domainData[$domainCode]['photos'][$slot]['title'] ?? null);
                    if ($request->hasFile("photos.$studentId.$domainCode.$slot") && ! $title) {
                        return back()
                            ->withInput()
                            ->with('error', 'Judul foto wajib diisi saat foto intrakurikuler diupload.');
                    }
                }
            }

            $hasTextData = collect($reportData)->filter(fn ($value) => filled($value))->isNotEmpty();
            $hasPhotoTitles = collect($domainData)
                ->flatMap(fn ($domain) => $domain['photos'] ?? [])
                ->filter(fn ($photo) => filled($photo['title'] ?? null))
                ->isNotEmpty();
            $deleteRequested = (bool) ($payload['delete_report'] ?? false);

            if ($deleteRequested || (! $hasTextData && ! $hasPhotoTitles && ! $hasFile && $extracurricularRows === [])) {
                $existing = DailyLearningReport::query()
                    ->with(['photos', 'extracurricularItems'])
                    ->where('student_id', $studentId)
                    ->whereDate('assessed_on', $date)
                    ->first();

                if ($existing) {
                    $this->deleteDailyLearningReportFiles($existing);
                    $existing->delete();
                    $deletedCount++;
                }

                continue;
            }

            $report = DailyLearningReport::query()->updateOrCreate(
                [
                    'student_id' => $studentId,
                    'assessed_on' => $date,
                ],
                array_merge($reportData, [
                    'class_group' => $student->class_group,
                ])
            );

            foreach (array_keys($domains) as $domainCode) {
                foreach ([1, 2] as $slot) {
                    $title = $this->cleanNullableText($domainData[$domainCode]['photos'][$slot]['title'] ?? null);
                    $deletePhoto = (bool) ($domainData[$domainCode]['photos'][$slot]['delete'] ?? false);
                    $photo = DailyLearningReportPhoto::query()->firstOrNew([
                        'daily_learning_report_id' => $report->id,
                        'domain_code' => $domainCode,
                        'slot' => $slot,
                    ]);

                    if ($deletePhoto && $photo->exists) {
                        if ($photo->image_path) {
                            Storage::disk('public')->delete($photo->image_path);
                        }
                        $photo->delete();
                        continue;
                    }

                    $file = $request->file("photos.$studentId.$domainCode.$slot");
                    if ($file) {
                        if ($photo->exists && $photo->image_path) {
                            Storage::disk('public')->delete($photo->image_path);
                        }
                        $photo->image_path = $file->store('daily-learning-reports', 'public');
                    }

                    if ($title || $photo->image_path) {
                        $photo->title = $title;
                        $photo->save();
                    } elseif ($photo->exists) {
                        $photo->delete();
                    }
                }
            }

            $report->extracurricularItems()->delete();
            foreach ($extracurricularRows as $index => $row) {
                $report->extracurricularItems()->create([
                    'sort_order' => $index + 1,
                    'implementation' => $row['implementation'],
                    'activity' => $row['activity'],
                    'score_label' => $row['score_label'],
                ]);
            }

            $savedCount++;
        }

        $message = $savedCount > 0
            ? "Penilaian harian format baru berhasil disimpan untuk {$savedCount} siswa."
            : 'Tidak ada data penilaian baru yang disimpan.';

        if ($deletedCount > 0) {
            $message .= " {$deletedCount} laporan dikosongkan.";
        }

        return redirect()
            ->route('guru.daily', array_filter([
                'date' => $date,
                'group' => $request->input('group'),
                'search' => $request->input('search'),
                'student_id' => $request->integer('selected_student_id') ?: null,
            ], fn ($value) => filled($value)))
            ->with('success', $message);
    }

    public function destroyDailyAssessment(DailyLearningReport $assessment): RedirectResponse
    {
        $this->deleteDailyLearningReportFiles($assessment->loadMissing('photos'));
        $assessment->delete();

        return back()->with('success', 'Penilaian harian berhasil dihapus.');
    }

    private function intrakurikulerDomains(): array
    {
        return [
            'agama_budi_pekerti' => [
                'label' => 'Nilai Agama dan Budi Pekerti',
                'short' => 'Agama',
                'icon' => 'mosque',
                'score_column' => 'agama_budi_pekerti_score',
                'narrative_column' => 'agama_budi_pekerti_narrative',
            ],
            'jati_diri' => [
                'label' => 'Jati Diri',
                'short' => 'Jati Diri',
                'icon' => 'self_improvement',
                'score_column' => 'jati_diri_score',
                'narrative_column' => 'jati_diri_narrative',
            ],
            'literasi_steam' => [
                'label' => 'Dasar-dasar Literasi, Matematika, Sains, Teknologi, Rekayasa, dan Seni',
                'short' => 'Literasi & STEAM',
                'icon' => 'science',
                'score_column' => 'literasi_steam_score',
                'narrative_column' => 'literasi_steam_narrative',
            ],
        ];
    }

    private function scoreOptions(): array
    {
        return [
            'BB' => 'Belum Berkembang',
            'MB' => 'Mulai Berkembang',
            'BSH' => 'Berkembang Sesuai Harapan',
            'BSB' => 'Berkembang Sangat Baik',
        ];
    }

    private function normalizeScore(?string $score): ?string
    {
        $score = strtoupper(trim((string) $score));

        return array_key_exists($score, $this->scoreOptions()) ? $score : null;
    }

    private function normalizeExtracurricularRows(array $rows): array
    {
        return collect($rows)
            ->map(function ($row) {
                $implementation = $this->cleanNullableText($row['implementation'] ?? null);
                $activity = $this->cleanNullableText($row['activity'] ?? null);
                $scoreLabel = $this->normalizeScore($row['score_label'] ?? null);

                if (! $implementation && ! $activity && ! $scoreLabel) {
                    return null;
                }

                return [
                    'implementation' => $implementation,
                    'activity' => $activity,
                    'score_label' => $scoreLabel,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    private function cleanNullableText(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function deleteDailyLearningReportFiles(DailyLearningReport $report): void
    {
        foreach ($report->photos as $photo) {
            if ($photo->image_path) {
                Storage::disk('public')->delete($photo->image_path);
            }
        }
    }

    public function anecdotalNotes(Request $request): View
    {
        $selectedDate = $request->date('date')?->toDateString();

        if (! $this->tableReady(['anecdotal_notes', 'students'])) {
            return view('guru.anecdotal-notes.index', [
                'notes' => collect(),
                'students' => collect(),
                'selectedDate' => $selectedDate,
            ]);
        }

        $students = Student::query()->orderBy('full_name')->get(['id', 'full_name', 'nickname']);
        $notes = AnecdotalNote::query()
            ->with('student:id,full_name,nickname,avatar_url,class_group')
            ->when($selectedDate, fn ($query) => $query->whereDate('recorded_at', $selectedDate))
            ->latest('recorded_at')
            ->latest('id')
            ->limit(10)
            ->get();

        return view('guru.anecdotal-notes.index', compact('notes', 'students', 'selectedDate'));
    }

    public function developmentNarrative(Request $request): View
    {
        if (! $this->tableReady(['development_reports', 'students'])) {
            return view('guru.development-narratives.index', [
                'students' => collect(),
                'reports' => collect(),
                'selectedStudentId' => null,
            ]);
        }

        $students = Student::query()
            ->orderBy('class_group')
            ->orderBy('full_name')
            ->get(['id', 'full_name', 'nickname', 'class_group', 'school_year', 'avatar_url']);

        $selectedStudentId = $request->integer('student_id') ?: null;

        $reports = DevelopmentReport::query()
            ->with('student:id,full_name,nickname,class_group,school_year,avatar_url')
            ->when($selectedStudentId, fn ($query) => $query->where('student_id', $selectedStudentId))
            ->latest('updated_at')
            ->latest('id')
            ->limit(30)
            ->get();

        return view('guru.development-narratives.index', compact('students', 'reports', 'selectedStudentId'));
    }

    public function storeDevelopmentNarrative(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'report_id' => ['nullable', 'exists:development_reports,id'],
            'student_id' => ['required', 'exists:students,id'],
            'semester' => ['required', 'string', 'max:100'],
            'school_year' => ['required', 'string', 'max:20'],
            'summary' => ['required', 'string'],
            'teacher_note' => ['nullable', 'string'],
        ], [
            'student_id.required' => 'Siswa wajib dipilih.',
            'semester.required' => 'Semester wajib diisi.',
            'school_year.required' => 'Tahun pelajaran wajib diisi.',
            'summary.required' => 'Narasi perkembangan wajib diisi.',
        ]);

        $reportId = $validated['report_id'] ?? null;
        unset($validated['report_id']);

        if ($reportId) {
            DevelopmentReport::query()->whereKey($reportId)->update($validated);
            $message = 'Narasi perkembangan berhasil diperbarui dan tersinkron ke wali murid.';
        } else {
            DevelopmentReport::query()->updateOrCreate(
                [
                    'student_id' => $validated['student_id'],
                    'semester' => $validated['semester'],
                    'school_year' => $validated['school_year'],
                ],
                [
                    'summary' => $validated['summary'],
                    'teacher_note' => $validated['teacher_note'] ?? null,
                ]
            );
            $message = 'Narasi perkembangan berhasil disimpan dan tersinkron ke wali murid.';
        }

        return redirect()
            ->route('guru.development-narrative', ['student_id' => $validated['student_id']])
            ->with('success', $message);
    }

    public function destroyDevelopmentNarrative(DevelopmentReport $report): RedirectResponse
    {
        $studentId = $report->student_id;
        $report->delete();

        return redirect()
            ->route('guru.development-narrative', ['student_id' => $studentId])
            ->with('success', 'Narasi perkembangan berhasil dihapus.');
    }

    public function parentReport()
    {
        $user = Auth::user();
        if ($user->role === 'guru') return redirect()->route('guru.dashboard');

        $student = $user->student;
        if (!$student) return redirect()->route('wali.dashboard');

        // 1. Ringkasan (Development Report)
        $report = DevelopmentReport::query()
            ->where('student_id', $student->id)
            ->latest('updated_at')
            ->latest('id')
            ->first();

        // 2. Harian format baru: laporan belajar per siswa, diurutkan dari tanggal lama ke baru.
        $dailyLearningReports = collect();
        if ($this->tableReady(['daily_learning_reports', 'daily_learning_report_photos', 'daily_learning_report_extracurriculars'])) {
            $dailyLearningReports = DailyLearningReport::query()
                ->with(['photos', 'extracurricularItems'])
                ->where('student_id', $student->id)
                ->orderBy('assessed_on', 'asc')
                ->get()
                ->groupBy(function($item) {
                    $date  = $item->assessed_on;
                    $start = $date->copy()->startOfWeek(); // Monday
                    $end   = $start->copy()->addDays(4);   // Friday
                    return $start->format('Y-m-d') . '|' . $end->format('Y-m-d');
                })
                ->sortKeys();
        }

        // 3. Anekdot (Anecdotal Notes)
        $anecdotalNotes = AnecdotalNote::query()
            ->where('student_id', $student->id)
            ->latest('recorded_at')
            ->get();
            
        return view('wali_murid.reports.index', compact(
            'student', 
            'report', 
            'dailyLearningReports',
            'anecdotalNotes'
        ) + [
            'intrakurikulerDomains' => $this->intrakurikulerDomains(),
            'scoreOptions' => $this->scoreOptions(),
        ]);
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
            'class_group' => ['required', 'in:A,B'],
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
            'class_group' => ['required', 'in:A,B'],
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
            'class_group' => ['required', 'in:A,B'],
        ]);

        $student->update(['class_group' => $validated['class_group']]);

        return back()->with('success', 'Kelompok ' . $student->full_name . ' berhasil diubah.');
    }

    public function storeAnecdotal(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'note_id' => ['nullable', 'exists:anecdotal_notes,id'],
            'student_id' => ['required', 'exists:students,id'],
            'recorded_at' => ['required', 'date'],
            'location' => ['nullable', 'string', 'max:255'],
            'tone' => ['nullable', 'string', 'max:24'],
            'description' => ['required', 'string'],
        ]);

        $noteId = $validated['note_id'] ?? null;
        unset($validated['note_id']);

        if ($noteId) {
            AnecdotalNote::query()->whereKey($noteId)->update($validated);
        } else {
            AnecdotalNote::query()->create($validated);
        }

        return redirect()->route('guru.anecdotal')->with('success', $noteId ? 'Catatan anekdot berhasil diperbarui.' : 'Catatan anekdot berhasil disimpan.');
    }

    public function destroyAnecdotal(AnecdotalNote $note): RedirectResponse
    {
        $note->delete();

        return back()->with('success', 'Catatan anekdot berhasil dihapus.');
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
            'class_group.in' => 'Kelompok hanya boleh A atau B.',
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
