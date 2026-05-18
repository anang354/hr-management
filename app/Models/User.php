<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    public function canAccessPanel(Panel $panel): bool
    {
        if($panel->getId() === 'admin') {
            return $this->role === 'admin' || $this->role === 'hr_all';
        }
        // Logika untuk Panel Attendance
        if ($panel->getId() === 'attendance') {
            return in_array($this->role, ['admin', 'hr_all', 'hr', 'manager', 'user', 'leader']);
        }
        return false;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'department_id',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    const ROLE_ADMIN = 'admin';
    const ROLE_HR = 'hr';
    const ROLE_MANAGER = 'manager';
    const ROLE_LEADER = 'leader';
    const ROLE_HR_ALL = 'hr_all';
    const ROLE_USER = 'user';

    const USER_ROLES = [
        self::ROLE_ADMIN => 'Admin',
        self::ROLE_HR => 'HR',
        self::ROLE_MANAGER => 'Manager',
        self::ROLE_LEADER => 'Leader',
        self::ROLE_HR_ALL => 'HR All',
        self::ROLE_USER => 'User',
    ];
    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }
    public function isHr()
    {
        return $this->role === self::ROLE_HR;
    }
    public function isManager()
    {
        return $this->role === self::ROLE_MANAGER;
    }
    public function isLeader()
    {
        return $this->role === self::ROLE_LEADER;
    }
    public function isHrAll()
    {
        return $this->role === self::ROLE_HR_ALL;
    }
    public function isUser()
    {
        return $this->role === self::ROLE_USER;
    }
}
