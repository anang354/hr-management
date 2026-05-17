<?php

namespace App\Filament\Widgets;

use App\Enums\AttendanceStatus;
use App\Models\AttendanceData;
use App\Models\AttendanceUser;
use Carbon\CarbonPeriod;
use Filament\Widgets\ChartWidget;

class DailyAttendanceChartWidget extends ChartWidget
{
    protected ?string $heading = 'Daily Attendance Chart Widget';

    protected function getData(): array
    {
        // 1. Hitung total karyawan aktif sebagai pembagi (Denominator)
        $totalEmployees = AttendanceUser::where('is_active', true)->count();
        if ($totalEmployees === 0) $totalEmployees = 1; // Proteksi division by zero

        // 2. Tentukan rentang tanggal dari awal bulan sampai HARI INI
        // Kita batasi sampai hari ini agar grafik tidak drop ke 0% pada tanggal mendatang
        $startOfMonth = now()->startOfMonth();
        $today = now();
        $period = CarbonPeriod::create($startOfMonth, $today);

        // Buat array label untuk sumbu X (Tanggal 01, 02, dst)
        $labels = [];
        foreach ($period as $date) {
            $labels[] = $date->format('d M');
        }

        // 3. Tarik semua data attendance bulan berjalan
        $attendanceRecords = AttendanceData::query()
            ->whereBetween('date', [$startOfMonth->format('Y-m-d'), $today->format('Y-m-d')])
            ->get()
            ->groupBy('date');

        // 4. Siapkan wadah penampung data untuk setiap kategori
        $dataHadir = [];
        $dataAlpha = [];
        $dataLembur = [];
        $dataLibur = [];
        $dataSakitIzinCuti = [];

        // 5. Looping harian untuk menghitung persentase
        foreach ($period as $date) {
            $dateString = $date->format('Y-m-d');
            // dd($dateString);

            // Ambil data absen hari tersebut jika ada, jika tidak ada buat kumpulan kosong
            $dayRecords = $attendanceRecords->get($dateString) ?? collect();

            $countHadir = 0;
            $countAlpha = 0;
            $countLembur = 0;
            $countLibur = 0;
            $countSakitIzinCuti = 0;

            foreach ($dayRecords as $record) {
                switch ($record->status) {
                    case AttendanceStatus::Hadir:
                        $countHadir++;
                        break;
                    case AttendanceStatus::Alpha:
                        $countAlpha++;
                        break;
                    case AttendanceStatus::Lembur:
                        $countLembur++;
                        break;
                    case AttendanceStatus::Libur:
                        $countLibur++;
                        break;
                    // Gabungkan Sakit, Izin, dan Cuti menjadi satu kelompok sesuai request Anda
                    case AttendanceStatus::Sakit:
                    case AttendanceStatus::Izin:
                    case AttendanceStatus::Cuti:
                        $countSakitIzinCuti++;
                        break;
                }
            }

            // Hitung persentase tiap status: (Total Status / Total Karyawan) * 100
            $dataHadir[] = round(($countHadir / $totalEmployees) * 100, 2);
            $dataAlpha[] = round(($countAlpha / $totalEmployees) * 100, 2);
            $dataLembur[] = round(($countLembur / $totalEmployees) * 100, 2);
            $dataLibur[] = round(($countLibur / $totalEmployees) * 100, 2);
            $dataSakitIzinCuti[] = round(($countSakitIzinCuti / $totalEmployees) * 100, 2);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Hadir (%)',
                    'data' => $dataHadir,
                    'borderColor' => '#10B981', // Hijau
                    'backgroundColor' => 'transparent',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Alpha (%)',
                    'data' => $dataAlpha,
                    'borderColor' => '#EF4444', // Merah
                    'backgroundColor' => 'transparent',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Lembur (%)',
                    'data' => $dataLembur,
                    'borderColor' => '#F59E0B', // Oranye
                    'backgroundColor' => 'transparent',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Libur (%)',
                    'data' => $dataLibur,
                    'borderColor' => '#3B82F6', // Biru
                    'backgroundColor' => 'transparent',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Sakit, Izin & Cuti (%)',
                    'data' => $dataSakitIzinCuti,
                    'borderColor' => '#8B5CF6', // Ungu
                    'backgroundColor' => 'transparent',
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }
    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
            ],
            'scales' => [
                'y' => [
                    'min' => 0,
                    'max' => 100, // Mengunci batas atas grafik di 100%
                    'ticks' => [
                        'callback' => "function(value) { return value + '%'; }", // Tambah simbol % di sumbu Y
                    ],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
