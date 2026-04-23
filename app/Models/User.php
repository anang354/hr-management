<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

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

    const USER_ROLES = [
        self::ROLE_ADMIN => 'Admin',
        self::ROLE_HR => 'HR',
        self::ROLE_MANAGER => 'Manager',
        self::ROLE_LEADER => 'Leader',
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
}
