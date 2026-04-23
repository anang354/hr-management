<?php

use App\Http\Controllers\ContractSettingsController;
use App\Http\Controllers\LetterController;
use App\Livewire\AttendancePage;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::middleware(['auth'])->group(function () {
    Route::get('admin/contract-settings/preview', [ContractSettingsController::class, 'index'])->name('contract-settings-preview');
    Route::get('admin/contracts/{id}', [ContractSettingsController::class, 'show'])->name('contract-settings-show');
    Route::get('/admin/leave-requests/{id}/letter', [LetterController::class, 'leaveRequestShow'])->name('leave-request-letter');
});
Route::get('/attendance', AttendancePage::class)->name('attendance');
