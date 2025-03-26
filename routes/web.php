<?php

use App\Http\Controllers\SmsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-log', function () {
    Log::info('This is an info log for testing.');
    return 'Log written';
});




Route::get('/sms/send', [SmsController::class, 'showSmsForm'])->name('sms.show');
Route::post('/sms/send', [SmsController::class, 'sendSms'])->name('sms.send');
