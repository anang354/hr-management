<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BreakView extends Model
{
    protected $table = 'view_employee_breaks'; // Arahkan ke View MySQL

    protected $primaryKey = 'id';
    public $incrementing = false; // Karena ID kita berbentuk string (contoh: 1-2026-07-13)
    protected $keyType = 'string';

    public $timestamps = false; // View tidak punya created_at / updated_at

    // (Opsional) Tambahkan relasi jika Anda ingin menampilkan Nama Karyawan, bukan sekadar biometric_id
    public function attendanceUser()
    {
        return $this->belongsTo(AttendanceUser::class, 'biometric_id', 'biometric_id');
    }
}
