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
Route::get('/test', function() {
    $logTime = '2026-05-08 20:00:50';
    $shift = \App\Models\AttendanceShift::where('id', 2)->first();
    $currentTime = Carbon\Carbon::parse($logTime)->timezone(config('app.timezone'));

    $shiftIn = $currentTime->copy()->setTimeFrom($shift->check_in_time);
        if ($currentTime->gt($shiftIn)) {
            // Gunakan absolute: true agar tidak negatif
            $diffMinutes = $currentTime->diffInMinutes($shiftIn, true);
            return (float) ceil($diffMinutes / 60);
        }
        return 0.0;
});
