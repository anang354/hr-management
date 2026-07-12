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

        // 1. Cari Karyawan
        $employee = AttendanceUser::where('biometric_id', $biometricId)->first();
        if (!$employee) return;

        $currentTime = Carbon::parse($logTime)->timezone(config('app.timezone'));
        $timeOnly = $currentTime->format('H:i:s');
        $dateOnly = $currentTime->format('Y-m-d');

        // Ambil record kehadiran hari ini (jika sudah ada)
        $attendance = AttendanceData::firstOrNew(['user_id' => $employee->id, 'date' => $dateOnly]);

        // --- PROSES CHECK-IN (TYPE 0) ---
        if ($typeValue == 0) {
            // LOGIKA KONFLIK: Hanya update jika clock_in masih kosong
            // ATAU jika jam tap baru lebih awal dari yang sudah tercatat
            if (!$attendance->clock_in || $currentTime->lt(Carbon::parse($attendance->clock_in))) {

                $shift = $this->findMatchingShift($timeOnly, 0);
                if ($shift) {
                    $isOffDay = $this->attendanceService->isHolidayOrWeekend($dateOnly);

                    $attendance->shift_id = $shift->id;
                    $attendance->clock_in = $currentTime;
                    $attendance->status = $isOffDay ? 'Lembur' : 'Hadir';
                    $attendance->coming_late = $this->calculateLatePenalty($currentTime, $shift);
                    $attendance->save();
                }
            }
        }

        // --- PROSES CHECK-OUT (TYPE 1) ---
        else if ($typeValue == 1) {
            // Gunakan shift dari data masuk (jika ada) agar tidak berubah shift di tengah jalan
            // Jika belum ada data masuk, baru cari shift berdasarkan waktu tap saat ini
            $activeShiftId = $attendance->shift_id;
            $currentShift = $activeShiftId ? AttendanceShift::find($activeShiftId) : $this->findMatchingShift($timeOnly, 1);

            if (!$currentShift) return;

            // Tentukan Tanggal Target (H-1 jika cross day)
            $targetDate = $currentShift->is_cross_day ? $currentTime->copy()->subDay()->format('Y-m-d') : $dateOnly;

            // Cari record pada targetDate (untuk shift malam, targetDate adalah hari sebelumnya)
            $targetAttendance = ($targetDate === $dateOnly) ? $attendance : AttendanceData::where('user_id', $employee->id)->where('date', $targetDate)->first();

            if ($targetAttendance && $targetAttendance->clock_in) {

                // LOGIKA KONFLIK: Hanya update jika clock_out masih kosong
                // ATAU jika jam tap baru lebih akhir dari yang sudah tercatat
                if (!$targetAttendance->clock_out || $currentTime->gt(Carbon::parse($targetAttendance->clock_out))) {

                    $clockIn = Carbon::parse($targetAttendance->clock_in);
                    $metrics = $this->calculateExitMetrics($currentTime, $clockIn, $currentShift);

                    // KODE LAMA
                    // $isOffDayStart = $this->attendanceService->isHolidayOrWeekend($targetDate);
                    // $overtimeFinal = $isOffDayStart ? $metrics['working_hours'] : $metrics['overtime_hours'];
                    // $overtimeFixHours = $isOffDayStart ? $metrics['overtime_hours'] * 2 : $metrics['overtime_hours'] * 1.5;

                    $isOffDayStart = $this->attendanceService->isHolidayOrWeekend($targetDate);
                    // Total jam lembur: Jika hari libur, hitung semua jam kerja. Jika hari biasa, hitung jam setelah shift selesai.
                    $overtimeFinal = $isOffDayStart ? $metrics['working_hours'] : $metrics['overtime_hours'];
                    // PERBAIKAN: Gunakan $overtimeFinal sebagai dasar perkalian untuk hari libur
                    $overtimeFixHours = $isOffDayStart ? $overtimeFinal * 2 : $metrics['overtime_hours'] * 1.5;

                    $targetAttendance->update([
                        'clock_out' => $currentTime,
                        'early_leave' => $metrics['early_leave'],
                        'overtime_hours' => (float) (floor($overtimeFinal * 2) / 2),
                        'working_hours' => (float) (floor($metrics['working_hours'] * 2) / 2),
                        'overtime_fix_hours' => (float) (floor($overtimeFixHours * 2) / 2),
                    ]);
                }
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

    public function reprocessManual($attendanceId, $newShiftId)
    {
        $attendance = AttendanceData::find($attendanceId);
        $shift = AttendanceShift::find($newShiftId);
        $biometricId = $attendance->attendance_user->biometric_id;

        $dateIn = $attendance->date;
        $clockIn = null;
        $clockOut = null;

        if (!$shift->is_cross_day) {
            // --- LOGIKA SHIFT SIANG (Satu Hari) ---
            // Ambil semua log di tanggal tersebut
            $logs = \App\Models\AttendanceLog::where('biometric_id', $biometricId)
                ->whereDate('attendance_time', $dateIn)
                ->orderBy('attendance_time', 'asc')
                ->get();

            if ($logs->isNotEmpty()) {
                $clockIn = $logs->first()->attendance_time;
                // Pastikan clock_out berbeda dengan clock_in
                if ($logs->count() > 1) {
                    $clockOut = $logs->last()->attendance_time;
                }
            }
        } else {
            // --- LOGIKA SHIFT MALAM (Beda Hari) ---
            $dateOut = \Carbon\Carbon::parse($dateIn)->addDay()->format('Y-m-d');

            // Cari jam masuk: Ambil log paling awal di sore/malam hari H
            $clockIn = \App\Models\AttendanceLog::where('biometric_id', $biometricId)
                ->whereDate('attendance_time', $dateIn)
                ->whereTime('attendance_time', '>=', '12:00:00') // Ambang batas siang
                ->orderBy('attendance_time', 'asc')
                ->first()?->attendance_time;

            // Cari jam pulang: Ambil log paling akhir di pagi/siang hari H+1
            $clockOut = \App\Models\AttendanceLog::where('biometric_id', $biometricId)
                ->whereDate('attendance_time', $dateOut)
                ->whereTime('attendance_time', '<=', '12:00:00') // Ambang batas siang besoknya
                ->orderBy('attendance_time', 'desc')
                ->first()?->attendance_time;
        }

        // Hitung ulang metrik menggunakan fungsi yang sudah Anda miliki
        $status = $this->attendanceService->isHolidayOrWeekend($dateIn) ? 'Lembur' : 'Hadir';

        $late = $clockIn ? $this->calculateLatePenalty(\Carbon\Carbon::parse($clockIn), $shift) : 0;

        $metrics = ['early_leave' => 0, 'overtime_hours' => 0, 'working_hours' => 0];
        if ($clockIn && $clockOut) {
            $metrics = $this->calculateExitMetrics(
                \Carbon\Carbon::parse($clockOut),
                \Carbon\Carbon::parse($clockIn),
                $shift
            );
        }
        // KODE LAMA
        // $isOffDayStart = $this->attendanceService->isHolidayOrWeekend($dateIn);
        // $overtimeFinal = $isOffDayStart ? $metrics['working_hours'] : $metrics['overtime_hours'];
        // $overtimeFixHours = $isOffDayStart ? $metrics['overtime_hours'] * 2 : $metrics['overtime_hours'] * 1.5;

        $isOffDayStart = $this->attendanceService->isHolidayOrWeekend($dateIn);
        $overtimeFinal = $isOffDayStart ? $metrics['working_hours'] : $metrics['overtime_hours'];
        $overtimeFixHours = $isOffDayStart ? $overtimeFinal * 2 : $metrics['overtime_hours'] * 1.5;

        // Update data final
        $attendance->update([
            'shift_id' => $shift->id,
            'clock_in' => $clockIn,
            'clock_out' => $clockOut,
            'status' => $status,
            'coming_late' => $late,
            'early_leave' => $metrics['early_leave'],
            'overtime_hours' => $overtimeFinal,
            'working_hours' => $metrics['working_hours'],
            'overtime_fix_hours' => $overtimeFixHours,
        ]);
    }
}
