<?php

namespace App\Models;

use App\Enums\AttendanceType;
use App\Enums\VerifyMethod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BreakLog extends Model
{
    protected $fillable = [
        'biometric_id',
        'attendance_time',
        'type',
        'processed_at'
    ];
    protected $casts = [
        'type' => AttendanceType::class,
        'attendance_time' => 'datetime',
        'verify_method' => VerifyMethod::class,
    ];

    public function attendanceUser(): HasOne
    {
        return $this->hasOne(AttendanceUser::class, 'biometric_id', 'biometric_id');
    }
}
