<?php

use App\Http\Controllers\ContractSettingsController;
use App\Http\Controllers\LetterController;
use App\Libs\ZKLibrary;
use App\Livewire\AttendancePage;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $zk = new ZKLibrary('132.168.65.250', 4370);
    try {
        $zk->connect();
        $getUser = $zk->getUser();
        dd($getUser);
        $zk->disconnect();
    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
    }
});
Route::middleware(['auth'])->group(function () {
    Route::get('admin/contract-settings/preview', [ContractSettingsController::class, 'index'])->name('contract-settings-preview');
    Route::get('admin/contracts/{id}', [ContractSettingsController::class, 'show'])->name('contract-settings-show');
    Route::get('/admin/leave-requests/{id}/letter', [LetterController::class, 'leaveRequestShow'])->name('leave-request-letter');
});
Route::get('/scan', AttendancePage::class)->name('attendance');
