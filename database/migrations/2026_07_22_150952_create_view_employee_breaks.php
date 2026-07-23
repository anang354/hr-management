<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Hapus view jika sebelumnya sudah ada
        DB::statement("DROP VIEW IF EXISTS view_employee_breaks");
        DB::statement("
            CREATE VIEW view_employee_breaks AS
            WITH RankedBreaks AS (
                SELECT
                    biometric_id,
                    -- PENGELOMPOKAN CROSS-DAY: Kurangi 7 jam hanya untuk mencari 'Tanggal Shift'
                    DATE(attendance_time - INTERVAL 7 HOUR) AS tanggal_shift,

                    -- Jam asli yang akan ditampilkan di tabel
                    TIME(attendance_time) AS jam,
                    type,

                    -- Partisi berdasarkan tanggal_shift, bukan tanggal asli
                    ROW_NUMBER() OVER (
                        PARTITION BY biometric_id, DATE(attendance_time - INTERVAL 7 HOUR), type
                        ORDER BY attendance_time ASC
                    ) AS rn
                FROM break_logs
                WHERE type IN (2, 3)
            )
            SELECT
                CONCAT(biometric_id, '-', tanggal_shift) AS id,
                biometric_id,
                tanggal_shift AS tanggal,
                MAX(CASE WHEN type = 2 AND rn = 1 THEN jam END) AS break_out_1,
                MAX(CASE WHEN type = 3 AND rn = 1 THEN jam END) AS break_in_1,
                MAX(CASE WHEN type = 2 AND rn = 2 THEN jam END) AS break_out_2,
                MAX(CASE WHEN type = 3 AND rn = 2 THEN jam END) AS break_in_2
            FROM RankedBreaks
            GROUP BY biometric_id, tanggal_shift
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS view_employee_breaks");
    }
};
