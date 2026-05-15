<?php

namespace App\Models;

use App\Libs\ZKLibrary;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    protected static function booted()
    {
        static::created(function ($attendanceUser) {
            $zk = new ZKLibrary(config('services.zkteco.ip'), config('services.zkteco.port'));
            try {
                $zk->connect();
                $zk->disableDevice();
                $zk->setUser(
                    (int) $attendanceUser->biometric_id,
                    (string) $attendanceUser->biometric_id,
                    (string) $attendanceUser->display_name,
                    (string) $attendanceUser->password,
                    (int) $attendanceUser->privilege
                );

                $zk->enableDevice();
                $zk->disconnect();
            } catch (\Exception $e) {
                return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
            }
        });
        static::deleted(function ($attendanceUser) {
            $zk = new ZKLibrary(config('services.zkteco.ip'), config('services.zkteco.port'));
            try {
                $zk->connect();
                $zk->disableDevice();
                $zk->deleteUser((int) $attendanceUser->biometric_id);
                $zk->enableDevice();
                $zk->disconnect();

            } catch (\Exception $e) {
                \Log::error("Gagal menghapus user di mesin: " . $e->getMessage());
            }
            $biometric_backup = BiometricBackup::where('biometric_id', $attendanceUser->biometric_id)->get();
            if ($biometric_backup->count() > 0){
                foreach ($biometric_backup as $backup) {
                    $backup->delete();
                }
            }
        });
        static::updated(function ($attendanceUser) {
            $zk = new ZKLibrary(config('services.zkteco.ip'), config('services.zkteco.port'));
            try {
                $zk->connect();
                $zk->disableDevice();
                $zk->setUser(
                    (int) $attendanceUser->biometric_id,
                    (string) $attendanceUser->biometric_id,
                    (string) $attendanceUser->display_name,
                    (string) $attendanceUser->password,
                    (int) $attendanceUser->privilege
                );

                $zk->enableDevice();
                $zk->disconnect();
            } catch (\Exception $e) {
                return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
            }
        });
    }
}
