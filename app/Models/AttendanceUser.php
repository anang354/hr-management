<?php

namespace App\Models;

use App\Libs\ZKLibrary;
use App\Models\Machine;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AttendanceUser extends Model
{
    //
    protected $fillable = [
        'employee_id',
        'biometric_id',
        'display_name',
        'privilege',
        'card_number',
        'password',
        'is_active',
        'last_sync',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
    public function attendanceData(): HasMany
    {
        return $this->hasMany(AttendanceData::class, 'user_id', 'id');
    }
    public function biometricBackups(): HasMany
    {
        return $this->hasMany(BiometricBackup::class, 'biometric_id', 'biometric_id');
    }
    public static function getNextAvailableBiometricId()
    {
        // Cari ID terkecil yang hilang (The Gap)
        $allIds = self::pluck('biometric_id')->toArray();

        if (empty($allIds)) return 1;

        $maxId = max($allIds);
        for ($i = 1; $i <= $maxId; $i++) {
            if (!in_array($i, $allIds)) {
                return $i; // Mengembalikan ID 3 jika ID 3 dihapus
            }
        }

        return $maxId + 1; // Jika tidak ada lubang, lanjut ke ID berikutnya (5)
    }
    protected function displayName(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => Str::title(strtolower($value)),
        );
    }
    protected static function booted()
    {
        // 1. EVENT: KETIKA USER BARU DIBUAT (CREATE)
        static::created(function ($user) {
            // Ambil semua mesin yang aktif
            $activeMachines = Machine::where('is_active', 1)->get();

            foreach ($activeMachines as $machine) {
                self::syncUserToMachine($user, $machine, 'create');
            }
        });

        // 2. EVENT: KETIKA DATA USER DIUBAH (UPDATE NAMA/PIN)
        static::updated(function ($user) {
            $activeMachines = Machine::where('is_active', 1)->get();

            foreach ($activeMachines as $machine) {
                self::syncUserToMachine($user, $machine, 'update');
            }
        });

        // 3. EVENT: KETIKA USER DIHAPUS (DELETE)
        static::deleted(function ($user) {
            $activeMachines = Machine::where('is_active', 1)->get();

            foreach ($activeMachines as $machine) {
                self::syncUserToMachine($user, $machine, 'delete');
            }
            $deleteBiometrics = BiometricBackup::where('biometric_id', $user->biometric_id)->delete();
        });
    }
    protected static function syncUserToMachine($user, Machine $machine, string $action)
    {
        $zk = new ZKLibrary($machine->ip_address, $machine->port);

        try {
            $zk->connect();
            $getSN = $zk->getSerialNumber();
            if ($getSN === null) {
                Log::error("Absen Sync: Gagal terhubung ke mesin {$machine->name} ({$machine->ip_address})");
                return;
            }
            $zk->disableDevice();

            $uid = (int) $user->biometric_id;
            $userIdText = (string) $user->biometric_id;
            $displayName = $user->display_name;
            $password = $user->password;
            $privilege = (int) $user->privilege;

            if ($action === 'create' || $action === 'update') {
                // SetUser akan membuat baru jika belum ada, atau menimpa info nama jika sudah ada
                // Parameter: ($uid, $userid, $name, $password, $role)
                $zk->setUser($uid, $userIdText, $displayName, $password, $privilege);

            } elseif ($action === 'delete') {
                // Hapus user dari mesin target secara permanen
                $zk->deleteUser($uid);
            }
            $zk->enableDevice();
            $zk->disconnect();
        } catch (\Exception $e) {
            Log::error("Absen Sync Error pada mesin {$machine->name}: " . $e->getMessage());
        }
    }
}
