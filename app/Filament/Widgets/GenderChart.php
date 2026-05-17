<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class GenderChart extends ChartWidget
{
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
        $getData = \App\Models\Employee::query()->select('gender', 'id')->where('is_active', true)->get();
        $data = $getData->groupBy('gender')->map(fn($item) => $item->count());
        $male = $data->get('male', 0);
        $female = $data->get('female', 0);
        return [
            'labels' => [__('charts.genders.labels.male') . ' ' . $male, __('charts.genders.labels.female') . ' ' . $female],
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
