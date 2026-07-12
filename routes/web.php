<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ContractSettingsController;
use App\Http\Controllers\LetterController;
use App\Livewire\AttendancePage;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('filament.attendance.auth.login');
});
Route::get('/test-lib', function() {
    return view('test');
});
Route::middleware(['auth'])->group(function () {
    Route::get('admin/contract-settings/preview', [ContractSettingsController::class, 'index'])->name('contract-settings-preview');
    Route::get('admin/contracts/{id}', [ContractSettingsController::class, 'show'])->name('contract-settings-show');
    Route::get('/admin/leave-requests/{id}/letter', [LetterController::class, 'leaveRequestShow'])->name('leave-request-letter');
    Route::get('/attendance/attendance-data/overview', [AttendanceController::class, 'index'])->name('attendance-overview');
});
Route::get('/scan', AttendancePage::class)->name('attendance');
