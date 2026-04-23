<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OvertimeRequest extends Model
{
    protected $fillable = ['user_id', 'overtime_date', 'content', 'status', 'reason', 'rejected_by'];
    protected $casts = [
        'status' => \App\Enums\OvertimeStatus::class,
        'date' => 'overtime_date',
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OvertimeRequestItem::class);
    }

    public function getEmployeesItemsAttribute()
    {
        return $this->items()->count();
    }
}
