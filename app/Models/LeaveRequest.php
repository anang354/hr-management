<?php

namespace App\Models;

use App\Enums\LeaveType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveRequest extends Model
{
    //
    protected $fillable = [
        'employee_id',
        'user_id',
        'letter_number',
        'leave_type',
        'start_date',
        'end_date',
        'total_days',
        'reason',
        'status',
        'rejection_note',
    ];

    protected $casts = [
        'leave_type' => LeaveType::class,
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function generateLetterNumber(): string
    {
        $letter = 'SNE-BD-XZ-';
        $rand = random_int(1000, 9999);
        $year = date('Y');
        $month = date('m');
        $number = $rand . $month . $year;
        return $letter . $number;
    }
}
