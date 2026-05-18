<?php

namespace App\Filament\Widgets;

use App\Enums\AttendanceStatus;
use App\Models\AttendanceData;
use App\Models\AttendanceUser;
use Carbon\CarbonPeriod;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class DailyAttendanceChartWidget extends ChartWidget
{
    public static function canView(): bool
    {
        return auth()->user()?->role !== 'leader';
    }

    public function getHeading(): string
    {
        return __('attendances.chart_title');
    }
    protected static ?int $sort = 1;
    protected bool $isCollapsible = true;
    protected int | string | array $columnSpan = [
        'md' => 4,
        'xl' => 4,
    ];

    protected function getData(): array
    {
        $user = Auth::user();
        // Ambil nama departemen sang manager (Asumsi relasi user ke employee ada)
        $managerDepartment = $user->department_id;
        $totalEmployeesQuery = AttendanceUser::where('is_active', 1);
        if ($user->role === 'manager') {
            $totalEmployeesQuery->whereHas('employee', fn($q) => $q->where('department_id', $managerDepartment));
        }
        $totalEmployees = $totalEmployeesQuery->count();
        if ($totalEmployees === 0) $totalEmployees = 1;

        $startOfMonth = now()->startOfMonth();
        $today = now();
        $period = \Carbon\CarbonPeriod::create($startOfMonth, $today);

        // 2. Tarik data absen (Gunakan kondisional whereHas untuk Manager)
        $attendanceQuery = AttendanceData::query()
            ->whereBetween('date', [$startOfMonth->format('Y-m-d'), $today->format('Y-m-d')]);

        if ($user->role === 'manager') {
            $attendanceQuery->whereHas('attendance_user.employee', fn($q) =>
                $q->where('department_id', $managerDepartment)
            );
        }

        $attendanceRecords = $attendanceQuery->get()->groupBy('date');
        // dd($attendanceRecords);

        // Buat array label untuk sumbu X (Tanggal 01, 02, dst)
        $labels = [];
        foreach ($period as $date) {
            $labels[] = $date->format('d M');
        }

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
                    'borderColor' => '#8b8b8bff', // Biru
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
