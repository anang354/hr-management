<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ContractSettingsController;
use App\Http\Controllers\LetterController;
use App\Livewire\AttendancePage;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $ip = '132.168.65.251';
    $port = 4370;

    $zk = new \App\Libs\ZKLibrary($ip, $port);

    //$uid = 28; // Sesuai dengan isi paket biner (PIN=2)
    //$fingerIndex = 4; // WAJIB 9, karena isi biner aslinya adalah FID=9, bukan 8!

    try {
            $zk->connect();
            $zk->disableDevice();
            $data = $zk->getUser();
            $zk->enableDevice();
            $zk->disconnect();
            dd($data);
    } catch (\Exception $e) {
        echo "Terjadi error: " . $e->getMessage();
    }
});
Route::middleware(['auth'])->group(function () {
    Route::get('admin/contract-settings/preview', [ContractSettingsController::class, 'index'])->name('contract-settings-preview');
    Route::get('admin/contracts/{id}', [ContractSettingsController::class, 'show'])->name('contract-settings-show');
    Route::get('/admin/leave-requests/{id}/letter', [LetterController::class, 'leaveRequestShow'])->name('leave-request-letter');
    Route::get('/attendance/attendance-data/overview', [AttendanceController::class, 'index'])->name('attendance-overview');
});
Route::get('/scan', AttendancePage::class)->name('attendance');
