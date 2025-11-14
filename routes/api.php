<?php

use App\Http\Controllers\Api\Student\AttendanceController;
use App\Http\Controllers\Api\Student\LeaveRequestController;
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

        // Attendance (Student)
        Route::prefix('attendance')->group(function () {
            // Get today's status
            Route::get('today', [AttendanceController::class, 'today']);

            // Check in/out
            Route::post('check-in', [AttendanceController::class, 'checkIn']);
            Route::post('check-out', [AttendanceController::class, 'checkOut']);

            // History
            Route::get('history', [AttendanceController::class, 'history']);

            // Statistics
            Route::get('statistics', [AttendanceController::class, 'statistics']);

            // Permission (submit izin/sakit)
            Route::post('permission', [AttendanceController::class, 'submitPermission']);
        });
        // Leave Requests (Izin/Sakit)
        Route::prefix('leave-requests')->group(function () {
            Route::get('/', [LeaveRequestController::class, 'index']);
            Route::post('/', [LeaveRequestController::class, 'store']);
            Route::get('/{id}', [LeaveRequestController::class, 'show']);
            Route::put('/{id}', [LeaveRequestController::class, 'update']);
            Route::delete('/{id}', [LeaveRequestController::class, 'destroy']);
            Route::get('/statistics/summary', [LeaveRequestController::class, 'statistics']);
        });

        Route::get('/my-profile', [App\Http\Controllers\Api\Student\MyProfileController::class, 'index'])->name('student.my_profile.index');
        Route::post('/my-profile', [App\Http\Controllers\Api\Student\MyProfileController::class, 'update'])->name('student.my_profile.update');

    });
});

Route::get('/setting', App\Http\Controllers\Api\Student\SettingController::class)->name('setting');