<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class ReligionChart extends ChartWidget
{
    public function getHeading(): string
    {
        return __('charts.religions.title');
    }
    protected bool $isCollapsible = true;
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = [
        'md' => 2,
        'xl' => 2,
    ];

    protected function getData(): array
    {
        $getData = \App\Models\Employee::query()->select('religion', 'id')->where('is_active', true)->get();
        $data = $getData->groupBy('religion')->map(fn($item) => $item->count());
        return [
            'labels' => $data->keys()->map(fn($item) => ucwords($item)),
            'datasets' => [
                [
                    'label' => 'Religion',
                    'data' => $data->values(),
                    'backgroundColor' => ['#00f5d4', '#00bbf9', '#fee440', '#f15bb5', '#9b5de5'],
                    'hoverBackgroundColor' => ['#00f5d4', '#00bbf9', '#fee440', '#f15bb5', '#9b5de5'],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
