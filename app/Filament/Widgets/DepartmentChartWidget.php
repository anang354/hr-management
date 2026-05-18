<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class DepartmentChartWidget extends ChartWidget
{
    public static function canView(): bool
    {
        return auth()->user()?->role === 'admin' || auth()->user()?->role === 'hr' || auth()->user()?->role === 'hr_all';
    }
    public function getHeading(): string
    {
        return __('department.chart_title');
    }
    protected bool $isCollapsible = true;
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = [
        'md' => 4,
        'xl' => 4,
    ];

    protected function getData(): array
    {
        // Mengambil nama departemen beserta jumlah karyawannya
        $departments = \App\Models\Department::withCount([
                'employees' => function ($query) {
                    $query->where('is_active', true);
                }
            ])
            ->orderBy('employees_count', 'desc')
            ->get();
        return [
            'datasets' => [
                [
                    'label' => 'Total Karyawan',
                    'data' => $departments->pluck('employees_count')->toArray(),
                    'backgroundColor' => 'rgba(153, 102, 255, 0.2)',
                    'hoverBackgroundColor' => 'rgba(153, 102, 255, 0.5)',
                    'borderColor' => 'rgba(153, 102, 255, 1)',
                ],
            ],
            'labels' => $departments->pluck('name')->toArray(),
        ];
    }

    protected function getOptions(): array
    {
        return [
            // KUNCI UTAMA: Memutar grafik menjadi horizontal
            'indexAxis' => 'y',

            'plugins' => [
                'legend' => [
                    'display' => true, // Sembunyikan label kotak atas jika tidak diperlukan
                ],
            ],
            'scales' => [
                'x' => [
                    'grid' => [
                        'display' => true, // Menampilkan garis pandu vertikal
                    ],
                    'ticks' => [
                        'precision' => 0, // Memastikan angka di sumbu X berupa bilangan bulat
                    ],
                ],
                'y' => [
                    'grid' => [
                        'display' => false, // Menyembunyikan garis pandu horizontal agar lebih bersih
                    ],
                ],
            ],
        ];
    }
    protected function getType(): string
    {
        return 'bar';
    }
}
