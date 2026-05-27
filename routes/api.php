<?php

use App\Http\Controllers\RfidAttendanceController;
use App\Http\Controllers\TelegramWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/rfid/attendance', [RfidAttendanceController::class, 'store'])
    ->name('api.rfid.attendance');

Route::post('/telegram/webhook', [TelegramWebhookController::class, 'handle'])
    ->name('api.telegram.webhook');
