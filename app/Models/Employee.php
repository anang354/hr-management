<?php

namespace App\Models;

use App\Enums\Gender;
use App\Enums\Religion;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class Employee extends Model
{
    use HasTranslations, SoftDeletes;
    protected $fillable = [
        'department_id',
        'employee_pos_id',
        'employee_number',
        'nik',
        'name',
        'job',
        'gender',
        'email',
        'phone',
        'address',
        'residential_address',
        'place_of_birth',
        'birth_date',
        'join_date',
        'religion',
        'mothers_name',
        'blood_group',
        'photo',
        'last_education',
        'bank_account',
        'bank_name',
        'npwp',
        'bpjs_kesehatan',
        'bpjs_ketenagakerjaan',
        'ptkp_status',
        'is_active',
        'exit_date',
        'exit_reason',
    ];

    const BLOOD_GROUP = [
        'A' => 'A',
        'B' => 'B',
        'AB' => 'AB',
        'O' => 'O',
    ];

    const BANK_NAME = [
        'OCBC' => 'OCBC',
        'UOB' => 'UOB',
    ];
    const PTKP_STATUS = [
        'TK' => 'TK',
        'TK/0' => 'TK/0',
        'K/0' => 'K/0',
        'K/1' => 'K/1',
        'K/2' => 'K/2',
        'K/3' => 'K/3',
    ];


    protected $casts = [
        'religion' => Religion::class,
        'gender' => Gender::class,
        'total_days' => 'float',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);

    }
    public function employeePos(): BelongsTo
    {
        return $this->belongsTo(EmployeePos::class);
    }
    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }
    public function attendanceUser(): HasOne
    {
        return $this->hasOne(AttendanceUser::class);
    }
    protected function phone(): Attribute
    {
        return Attribute::make(
            set: function ($value) {
                $cleanValue = preg_replace('/[^0-9]/', '', $value);
                if (!str_starts_with($cleanValue, '0') && !str_starts_with($cleanValue, '62')) {
                    return '62' . $cleanValue;
                }
                return $cleanValue;
            },
        );
    }
    protected function name(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => Str::title(strtolower($value)),
        );
    }
    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }
    public function getIsEligibleForLeaveAttribute(): bool
    {
        // Cek apakah masa kerja sudah >= 1 tahun
        $joinDate = Carbon::parse($this->join_date);
        return $joinDate->diffInYears(now()) >= 1;
    }

    public function getRemainingLeaveAttribute(): float
    {
        if (!$this->is_eligible_for_leave) {
            return 0;
        }

        $currentYear = now()->year;

        // Hitung total cuti yang sudah disetujui HR di tahun ini
        $usedLeave = (float) $this->leaveRequests()
            ->whereYear('start_date', $currentYear)
            ->where('status', 'approved')
            ->sum('total_days');

        return (float) 12 - $usedLeave;
    }
}
