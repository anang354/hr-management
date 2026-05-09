<?php

namespace App\Models;

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
        'working_hours',
        'status',
    ];

    public function attendance_user(): BelongsTo
    {
        return $this->belongsTo(AttendanceUser::class, 'user_id', 'id');
    }

    public function attendance_shift(): BelongsTo
    {
        return $this->belongsTo(AttendanceShift::class, 'shift_id', 'id');
    }
}
