<?php

use App\Http\Controllers\Api\AttendanceApiController;
use App\Http\Controllers\Api\FingerprintController;
use App\Http\Controllers\RfidAttendanceController;
use App\Http\Controllers\TelegramWebhookController;
use Illuminate\Support\Facades\Route;

// Legacy RFID endpoint (kept for backward compatibility)
Route::post('/rfid/attendance', [RfidAttendanceController::class, 'store'])
    ->name('api.rfid.attendance');

// Unified attendance endpoint (RFID + Fingerprint)
Route::post('/attendance', [AttendanceApiController::class, 'store'])
    ->name('api.attendance.store');

// Fingerprint enrollment & deletion (device polling)
Route::prefix('fingerprint')->name('api.fingerprint.')->group(function (): void {
    // Web-initiated (authenticated via web session — needs web middleware for session)
    Route::post('/enrollment/request', [FingerprintController::class, 'requestEnrollment'])->middleware(['web', 'auth'])->name('enrollment.request');
    Route::get('/enrollment/status/{id}', [FingerprintController::class, 'enrollmentStatus'])->middleware(['web', 'auth'])->name('enrollment.status');
    Route::post('/deletion/request', [FingerprintController::class, 'requestDeletion'])->middleware(['web', 'auth'])->name('deletion.request');

    // Device polling (token-authenticated)
    Route::get('/enrollment/check', [FingerprintController::class, 'checkEnrollment'])->name('enrollment.check');
    Route::post('/enrollment/complete', [FingerprintController::class, 'completeEnrollment'])->name('enrollment.complete');
    Route::post('/enrollment/fail', [FingerprintController::class, 'failEnrollment'])->name('enrollment.fail');
    Route::get('/deletion/check', [FingerprintController::class, 'checkDeletion'])->name('deletion.check');
    Route::post('/deletion/done', [FingerprintController::class, 'completeDeletion'])->name('deletion.done');
});

Route::post('/telegram/webhook', [TelegramWebhookController::class, 'handle'])
    ->name('api.telegram.webhook');
