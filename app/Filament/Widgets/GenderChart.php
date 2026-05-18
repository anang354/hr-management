<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class GenderChart extends ChartWidget
{
    public static function canView(): bool
    {
        return auth()->user()?->role !== 'leader';
    }
    public function getHeading(): string
    {
        return __('charts.genders.title');
    }
    protected bool $isCollapsible = true;
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = [
        'md' => 2,
        'xl' => 2,
    ];

    protected function getData(): array
    {
        $user = Auth::user();

        // 1. Ambil data karyawan dengan filter berdasarkan role
        $getData = \App\Models\Employee::query()
            ->select('gender', 'id')
            ->where('is_active', true)
            // JIKA yang login adalah manager, kunci query hanya untuk departemennya saja
            ->when($user->role === 'manager', function ($query) use ($user) {
                return $query->where('department_id', $user->department_id);
            })
            ->get();

        // 2. Kelompokkan data seperti logika lama Anda
        $data = $getData->groupBy('gender')->map(fn($item) => $item->count());

        $male = $data->get('male', 0);
        $female = $data->get('female', 0);

        return [
            'labels' => [
                __('charts.genders.labels.male') . ' ' . $male,
                __('charts.genders.labels.female') . ' ' . $female
            ],
            'datasets' => [
                [
                    'label' => __('charts.genders.datasets'),
                    'data' => [$male, $female],
                    'backgroundColor' => ['#2563EB', '#ea33a4ff'],
                    'hoverBackgroundColor' => ['#1d4ed8', '#ed3adeff'],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
