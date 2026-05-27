<?php

use App\Http\Controllers\RfidAttendanceController;
use Illuminate\Support\Facades\Route;

Route::post('/rfid/attendance', [RfidAttendanceController::class, 'store'])
    ->name('api.rfid.attendance');
