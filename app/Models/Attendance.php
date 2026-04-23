<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    //
    protected $fillable = [
        'employee_id',
        'date',
        'shift',
        'checkin',
        'checkout',
        'breakout',
        'breakin',
        'status',
    ];
    protected $casts = [
        'shift' => \App\Enums\Shift::class,
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
