<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttendanceShift extends Model
{
    //
    protected $fillable = [
        'name',
        'check_in_time',
        'check_out_time',
        'check_in_start',
        'check_in_end',
        'check_out_start',
        'check_out_end',
        'is_cross_day',
        'break_minutes',
    ];

    public function attendanceData(): HasMany
    {
        return $this->hasMany(AttendanceData::class, 'shift_id', 'id');
    }
}
