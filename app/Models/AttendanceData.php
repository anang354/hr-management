<?php

namespace App\Models;

use App\Enums\AttendanceStatus;
use App\Services\AttendanceService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceData extends Model
{
    //
    protected $fillable = [
        'user_id',
        'shift_id',
        'date',
        'clock_in',
        'clock_out',
        'coming_late',
        'early_leave',
        'overtime_hours',
        'overtime_fix_hours',
        'working_hours',
        'status',
    ];

    protected $casts = [
        'status' => AttendanceStatus::class,
    ];

    public function attendance_user(): BelongsTo
    {
        return $this->belongsTo(AttendanceUser::class, 'user_id', 'id');
    }

    public function attendance_shift(): BelongsTo
    {
        return $this->belongsTo(AttendanceShift::class, 'shift_id', 'id');
    }
    protected static function booted()
    {
        static::saving(function ($attendance) {
            // Hanya jalankan jika kolom overtime_hours berubah (isDirty)
            // atau jika ini adalah record baru
            if ($attendance->isDirty('overtime_hours')) {
                // Memanggil service secara manual karena Model tidak mendukung DI di constructor
                $service = app(AttendanceService::class);
                $isOffDay = $service->isHolidayOrWeekend($attendance->date);
                // Logika Multiplier
                $multiplier = $isOffDay ? 2.0 : 1.5;
                // Hitung overtime_fix_hours
                $attendance->overtime_fix_hours = (float) round($attendance->overtime_hours * $multiplier, 2);
            }
        });
    }

}
