<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\UserInfoController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;



Route::middleware(['verified', 'auth'])->group(function () {

    Route::get('/', [AttendanceController::class, 'index']);
    Route::get(
        '/attendance',
        [AttendanceController::class, 'index']
    );
    Route::post('/attendance', [AttendanceController::class, 'attendanceLogic']);
    Route::get('/search', [AttendanceController::class, 'search']);
    Route::get('/search/user', [UserInfoController::class, 'index']);
    Route::get('/search/user/attendance', [UserInfoController::class, 'attendance']);
});

Route::get('/login', function () {
    return view('auth.login');
});

Route::get('/register', function () {
    return view('auth.register');
});

//メール認証用
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/attendance');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/resend', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');
