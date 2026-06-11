<?php

use App\Http\Controllers\PortalController;
use App\Http\Controllers\AttendanceController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', [PortalController::class, 'login'])->name('login');
Route::get('/login', [PortalController::class, 'login'])->name('auth.login');
Route::post('/login', [PortalController::class, 'handleLogin'])->name('auth.handle');
Route::post('/logout', [PortalController::class, 'logout'])->name('auth.logout');

// Redirect routes
Route::redirect('/guru', '/guru/dashboard');
Route::redirect('/wali', '/wali/dashboard');

// Guru Routes - Protected by auth middleware
Route::prefix('guru')->name('guru.')->middleware('auth')->group(function (): void {
    Route::get('/dashboard', [PortalController::class, 'teacherDashboard'])->name('dashboard');
    Route::get('/siswa', [PortalController::class, 'studentList'])->name('students.index');
    Route::get('/siswa/tambah', [PortalController::class, 'studentForm'])->name('students.form');
    Route::post('/siswa', [PortalController::class, 'storeStudent'])->name('students.store');
    Route::get('/siswa/{student}/edit', [PortalController::class, 'editStudent'])->name('students.edit');
    Route::put('/siswa/{student}', [PortalController::class, 'updateStudent'])->name('students.update');
    Route::delete('/siswa/{student}', [PortalController::class, 'destroyStudent'])->name('students.destroy');
    Route::put('/siswa/{student}/quick-update-group', [PortalController::class, 'quickUpdateGroup'])->name('students.quickUpdateGroup');
    Route::get('/penilaian-harian', [PortalController::class, 'dailyAssessment'])->name('daily');
    Route::post('/penilaian-harian', [PortalController::class, 'storeDailyAssessment'])->name('daily.store');
    Route::delete('/penilaian-harian/{assessment}', [PortalController::class, 'destroyDailyAssessment'])->name('daily.destroy');
    Route::get('/catatan-anekdot', [PortalController::class, 'anecdotalNotes'])->name('anecdotal');
    Route::post('/catatan-anekdot', [PortalController::class, 'storeAnecdotal'])->name('anecdotal.store');
    Route::delete('/catatan-anekdot/{note}', [PortalController::class, 'destroyAnecdotal'])->name('anecdotal.destroy');
    Route::redirect('/hasil-karya', '/guru/penilaian-harian');
    Route::get('/narasi-perkembangan', [PortalController::class, 'developmentNarrative'])->name('development-narrative');
    Route::post('/narasi-perkembangan', [PortalController::class, 'storeDevelopmentNarrative'])->name('development-narrative.store');
    Route::delete('/narasi-perkembangan/{report}', [PortalController::class, 'destroyDevelopmentNarrative'])->name('development-narrative.destroy');
    Route::get('/pengaturan', [PortalController::class, 'settings'])->name('settings');
    Route::post('/pengaturan/profil', [PortalController::class, 'updateProfile'])->name('settings.profile');
    Route::post('/pengaturan/password', [PortalController::class, 'updatePassword'])->name('settings.password');
    Route::post('/pengaturan/absensi', [PortalController::class, 'updateAttendanceSettings'])->name('settings.attendance');
    Route::delete('/pengaturan/telegram/{chat}', [PortalController::class, 'destroyTelegramConnection'])->name('settings.telegram.destroy');
    
    // Attendance (Guru only)
    Route::get('/absensi', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/absensi', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::put('/absensi/{attendance}', [AttendanceController::class, 'update'])->name('attendance.update');
    Route::delete('/absensi/{attendance}/waktu/{field}', [AttendanceController::class, 'clearTime'])->name('attendance.clear-time');
    
    // Agenda Routes
    Route::get('/agenda', [PortalController::class, 'teacherAgenda'])->name('agenda');
    Route::post('/agenda', [PortalController::class, 'storeAgenda'])->name('agenda.store');
    Route::put('/agenda/{agenda}', [PortalController::class, 'updateAgenda'])->name('agenda.update');
    Route::delete('/agenda/{agenda}', [PortalController::class, 'destroyAgenda'])->name('agenda.destroy');
});

// Wali Murid Routes - Protected by auth middleware
Route::prefix('wali')->name('wali.')->middleware('auth')->group(function (): void {
    Route::get('/dashboard', [PortalController::class, 'parentDashboard'])->name('dashboard');
    Route::get('/profil', [PortalController::class, 'studentProfile'])->name('profile');
    Route::get('/laporan', [PortalController::class, 'parentReport'])->name('report');
    Route::get('/galeri', [PortalController::class, 'parentGallery'])->name('gallery');
    // Parent attendance
    Route::get('/absensi', [PortalController::class, 'parentAttendance'])->name('attendance');
    Route::get('/agenda', [PortalController::class, 'parentAgenda'])->name('agenda');
    Route::get('/telegram', [PortalController::class, 'parentTelegram'])->name('telegram');
});

Route::view('/stitch/welcome', 'welcome')->name('stitch.welcome');
