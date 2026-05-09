<?php
namespace App\Services;

use App\Models\AttendanceData;
use App\Models\AttendanceShift;
use App\Models\AttendanceUser;
use App\Services\AttendanceService;
use Carbon\Carbon;

class AttendanceProcessor
{
    protected $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }
    public function process($biometricId, $logTime, $type)
    {
        $typeValue = is_object($type) ? $type->value : $type;
        // 1. Cari Karyawan & Baris Kehadiran Hari Ini
        $employee = AttendanceUser::where('biometric_id', $biometricId)->first();
        if (!$employee) return;

        $currentTime = Carbon::parse($logTime)->timezone(config('app.timezone'));
        $timeOnly = $currentTime->format('H:i:s');
        $dateOnly = $currentTime->format('Y-m-d');

        $shift = $this->findMatchingShift($timeOnly, $typeValue);
        if (!$shift) return;

        if ($typeValue == 0) { // CHECK-IN
            $isOffDay = $this->attendanceService->isHolidayOrWeekend($dateOnly);
            $status = $isOffDay ? 'Lembur' : 'Hadir';
            AttendanceData::updateOrCreate(
                ['user_id' => $employee->id, 'date' => $dateOnly],
                [
                    'shift_id' => $shift->id,
                    'clock_in' => $currentTime,
                    'status' => $status,
                    'coming_late' => $this->calculateLatePenalty($currentTime, $shift),
                ]
            );
        }

        else if ($typeValue == 1) { // CHECK-OUT
            // Tentukan Tanggal Target (H-1 jika cross day)
            $targetDate = $shift->is_cross_day ? $currentTime->copy()->subDay()->format('Y-m-d') : $dateOnly;
            $isOffDayStart = $this->attendanceService->isHolidayOrWeekend($targetDate);
            $attendance = AttendanceData::where('user_id', $employee->id)
                                    ->where('date', $targetDate)
                                    ->first();

            if ($attendance && $attendance->clock_in) {
                $clockIn = Carbon::parse($attendance->clock_in);
                $metrics = $this->calculateExitMetrics($currentTime, $clockIn, $shift);
                // Jika hari libur saat mulai masuk, maka working_hours dihitung full sebagai overtime
                $overtimeFinal = $isOffDayStart ? $metrics['working_hours'] : $metrics['overtime_hours'];

                $attendance->update([
                    'clock_out' => $currentTime,
                    'early_leave' => $metrics['early_leave'],
                    'overtime_hours' => $overtimeFinal,
                    'working_hours' => $metrics['working_hours'],
                ]);
            }
        }
    }

    private function calculateLatePenalty($in, $shift)
    {
        // Abaikan detik pada waktu check-in (set ke 00)
        $inTruncated = $in->copy()->second(0);
        // Set jadwal shift juga ke detik 00 untuk perbandingan yang adil
        $shiftIn = $in->copy()->setTimeFrom($shift->check_in_time)->second(0);
        // Jika waktu check-in (tanpa detik) lebih besar dari jadwal
        if ($inTruncated->gt($shiftIn)) {
            // Hitung selisih dalam menit
            $diffMinutes = $inTruncated->diffInMinutes($shiftIn, true);
            // Aturan ketat: 1 menit telat = 1 jam penalti
            return (float) ceil($diffMinutes / 60);
        }

        return 0.0;
    }

    private function calculateExitMetrics($out, $in, $shift)
    {
        // 1. Truncate detik pada waktu checkout (set ke 00) untuk konsistensi
        $out = $out->copy()->second(0);
        $in = $in->copy()->second(0);

        $shiftOut = $in->copy()->setTimeFrom($shift->check_out_time)->second(0);
        if ($shift->is_cross_day) $shiftOut->addDay();

        // Ambil durasi istirahat (dalam menit) dari database shift
        $breakMinutes = (int) ($shift->break_minutes ?? 0);

        $earlyLeave = 0.0;
        $overtimeHours = 0.0;

        // 2. Hitung Early Leave (Strict Penalty: 1 menit = 1 jam)
        if ($out->lt($shiftOut)) {
            $diffMinutes = $out->diffInMinutes($shiftOut, true);
            $earlyLeave = (float) ceil($diffMinutes / 60);
        }

        // 3. Hitung Overtime (Jam Lembur)
        // Lembur adalah selisih antara waktu pulang asli dengan jadwal seharusnya
        if ($out->gt($shiftOut)) {
            $otMinutes = $out->diffInMinutes($shiftOut, true);
            $overtimeHours = (float) ($otMinutes / 60);
        }

        // 4. Hitung Working Hours (Total Durasi - Istirahat)
        $totalMinutes = $out->diffInMinutes($in, true);

        // Kurangi total menit dengan istirahat, pastikan tidak negatif
        $netWorkingMinutes = max(0, $totalMinutes - $breakMinutes);
        $workingHours = (float) ($netWorkingMinutes / 60);

        return [
            'early_leave' => $earlyLeave,
            // Gunakan pembulatan ke bawah tiap 0.5 jam sesuai rumus Anda
            'overtime_hours' => (float) (floor($overtimeHours * 2) / 2),
            'working_hours' => (float) (floor($workingHours * 2) / 2),
        ];
    }

    private function findMatchingShift($time, $type)
    {
        $shifts = AttendanceShift::all();
        foreach ($shifts as $s) {
            $start = ($type == 0) ? $s->check_in_start : $s->check_out_start;
            $end = ($type == 0) ? $s->check_in_end : $s->check_out_end;

            if ($start <= $end) {
                if ($time >= $start && $time <= $end) return $s;
            } else {
                if ($time >= $start || $time <= $end) return $s;
            }
        }
        return null;
    }
}
