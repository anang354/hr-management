<?php
namespace App\Services;

use App\Models\Holiday;
use Carbon\Carbon;

class AttendanceService
{
    public function isHolidayOrWeekend($date)
    {
        $dt = Carbon::parse($date);

        // 1. Cek Akhir Pekan (Sabtu = 6, Minggu = 0)
        if ($dt->isWeekend()) {
            return true;
        }
        // 2. Cek Tabel Tanggal Merah
        $isHoliday = Holiday::where('holiday_date', $dt->format('Y-m-d'))->exists();
        if ($isHoliday) {
            return true;
        }

        return false;
    }
}
