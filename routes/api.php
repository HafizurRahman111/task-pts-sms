<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


// user routes
Route::get('/users', [UserController::class, 'index']);

Route::get('/sms-logs/{smsId}', [SmsController::class, 'getSmsLogs']);
    

// SMS Routes
Route::prefix('sms')->group(function () {
    // Send SMS
    Route::post('/send', [SmsController::class, 'sendSms']);
   
    // List all SMS records
    Route::get('/', [SmsController::class, 'index']);
    // Show a specific SMS record
    Route::get('/{sms}', [SmsController::class, 'show']);
    // Delete a specific SMS record
    Route::delete('/{sms}', [SmsController::class, 'destroy']);
});
