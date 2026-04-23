<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OvertimeRequestItem extends Model
{
    protected $fillable = [
        'overtime_request_id',
        'employee_id',
        'start_time',
        'end_time',
        'overtime_hours',
        'status',
        'reason',
    ];

    protected $casts = [
        'status' => \App\Enums\OvertimeItemStatus::class,
    ];
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
    public function overtimeRequest(): BelongsTo
    {
        return $this->belongsTo(OvertimeRequest::class);
    }
}
