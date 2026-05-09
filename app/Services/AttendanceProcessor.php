<?php
namespace App\Services;

use App\Models\AttendanceData;
use App\Models\AttendanceLog;
use App\Models\AttendanceShift;
use App\Models\AttendanceUser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AttendanceProcessor
{
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
            AttendanceData::updateOrCreate(
                ['user_id' => $employee->id, 'date' => $dateOnly],
                [
                    'shift_id' => $shift->id,
                    'clock_in' => $currentTime,
                    'status' => 'Hadir',
                    'coming_late' => $this->calculateLatePenalty($currentTime, $shift),
                ]
            );
        }

        else if ($typeValue == 1) { // CHECK-OUT
            $targetDate = $shift->is_cross_day ? $currentTime->copy()->subDay()->format('Y-m-d') : $dateOnly;

            $attendance = AttendanceData::where('user_id', $employee->id)
                                    ->where('date', $targetDate)
                                    ->first();

            if ($attendance && $attendance->clock_in) {
                $clockIn = Carbon::parse($attendance->clock_in);
                $metrics = $this->calculateExitMetrics($currentTime, $clockIn, $shift);

                $attendance->update([
                    'clock_out' => $currentTime,
                    'early_leave' => $metrics['early_leave'],
                    'overtime_hours' => $metrics['overtime_hours'],
                    'working_hours' => $metrics['working_hours'],
                ]);
            }
        }
    }

    private function calculateLatePenalty($in, $shift)
    {
        $shiftIn = $in->copy()->setTimeFrom($shift->check_in_time);
        if ($in->gt($shiftIn)) {
            // Gunakan absolute: true agar tidak negatif
            $diffMinutes = $in->diffInMinutes($shiftIn, true);
            return (float) ceil($diffMinutes / 60);
        }
        return 0.0;
    }

    private function calculateExitMetrics($out, $in, $shift)
    {
        $shiftOut = Carbon::parse($in->format('Y-m-d') . ' ' . $shift->check_out_time);
        if ($shift->is_cross_day) $shiftOut->addDay();

        $earlyLeave = 0.0;
        $overtimeHours = 0.0;

        // 1. Hitung Early Leave (Strict Penalty)
        if ($out->lt($shiftOut)) {
            $diffMinutes = $out->diffInMinutes($shiftOut, true);
            $earlyLeave = (float) ceil($diffMinutes / 60);
        }

        // 2. Hitung Overtime (Decimal Hours)
        if ($out->gt($shiftOut)) {
            $diffMinutes = $out->diffInMinutes($shiftOut, true);
            $overtimeHours = (float) round($diffMinutes / 60, 2);
        }

        // 3. Hitung Working Hours (Total In to Out)
        $workingMinutes = $out->diffInMinutes($in, true);
        $workingHours = (float) round($workingMinutes / 60, 2);

        return [
            'early_leave' => $earlyLeave,
            'overtime_hours' => $overtimeHours,
            'working_hours' => $workingHours
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
