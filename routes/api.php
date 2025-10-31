<?php

use App\Http\Controllers\Api\Student\LoginController;
use App\Http\Controllers\Api\Student\LogoutController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('student')->group(function () {
    Route::post('/login', LoginController::class)->name('student.login');
    Route::middleware('auth:api')->group(function () {
        Route::post('/logout', LogoutController::class)->name('student.logout');
        Route::get('/presence-today', [App\Http\Controllers\Api\Student\AttendanceController::class, 'today'])->name('student.presence.today');
        Route::post('/presence-check-in', [App\Http\Controllers\Api\Student\AttendanceController::class, 'checkIn'])->name('student.presence.check_in');
        Route::post('/presence-check-out', [App\Http\Controllers\Api\Student\AttendanceController::class, 'checkOut'])->name('student.presence.check_out');
        Route::get('/presence-history', [App\Http\Controllers\Api\Student\AttendanceController::class, 'history'])->name('student.presence.history');

        Route::get('/leave-request', [App\Http\Controllers\Api\Student\LeaveRequestController::class, 'index'])->name('student.leave_request.index');
        Route::post('/leave-request', [App\Http\Controllers\Api\Student\LeaveRequestController::class, 'store'])->name('student.leave_request.store');

        Route::get('/my-profile', [App\Http\Controllers\Api\Student\MyProfileController::class, 'index'])->name('student.my_profile.index');
        Route::post('/my-profile', [App\Http\Controllers\Api\Student\MyProfileController::class, 'update'])->name('student.my_profile.update');

    });
});

Route::get('/setting', App\Http\Controllers\Api\Student\SettingController::class)->name('setting');