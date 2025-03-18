<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


// user routes
Route::get('/users', [UserController::class, 'index']);

// sms routes
Route::post('/send-sms', [SmsController::class, 'sendSms']);

Route::prefix('sms')->group(function () {
    Route::get('/', [SmsController::class, 'index']);
    Route::get('/{sms}', [SmsController::class, 'show']);
});
