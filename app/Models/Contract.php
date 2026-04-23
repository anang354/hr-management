<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Contract extends Model
{
    protected $fillable = [
        'employee_id',
        'user_id',
        'sequence_number',
        'contract_number',
        'gaji_pokok',
        'tunjangan_jabatan',
        'tunjangan_bahasa',
        'tunjangan_keahlian',
        'tunjangan_transportasi',
        'tunjangan_lainnya',
        'total_gaji',
        'start_date',
        'contract_periode',
        'end_date',
        'contract_type',
        'snapshot_metadata',
        'is_active',
    ];
    protected $casts = [
        'snapshot_metadata' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function getIsActiveAttribute($value)
    {
        if (now()->parse($this->end_date)->isPast() && $value === true) {
            return false;
        }
        return $value;
    }
    public static function generateNextContractNumber(): string
    {
        $now = now();
        $year = $now->year;

        // Cari urutan terakhir di tahun ini
        $lastSequence = self::max('sequence_number');
        $nextSequence = ($lastSequence ?? 0) + 1;

        return self::formatContractString($nextSequence, $now);
    }
    public static function formatContractString(int $sequence, Carbon $date): string
    {
        $romans = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        $romanMonth = $romans[$date->month];

        return "{$sequence}/DSI-HR/PKWT/{$romanMonth}/{$date->year}";
    }
    protected static function booted()
    {
        static::creating(function ($contract) {
            DB::transaction(function () use ($contract) {
                $now = now();
                $lastSequence = static::lockForUpdate()
                    ->max('sequence_number');

                $newSequence = ($lastSequence ?? 0) + 1;

                $contract->sequence_number = $newSequence;
                $contract->contract_number = self::formatContractString($newSequence, $now);
            });
        });
    }
}
