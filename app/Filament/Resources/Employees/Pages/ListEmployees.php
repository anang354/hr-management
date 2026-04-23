<?php

namespace App\Filament\Resources\Employees\Pages;

use App\Filament\Resources\Employees\EmployeeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Contracts\Database\Eloquent\Builder;

class ListEmployees extends ListRecords
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('employee.tabs.all')),
            'active' => Tab::make(__('employee.tabs.active'))
                ->badgeColor('success')
                ->icon('heroicon-o-check-circle')
                ->badge(\App\Models\Employee::where('is_active', true)->count())
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_active', true)),
            'inactive' => Tab::make(__('employee.tabs.inactive'))
                ->icon('heroicon-o-x-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_active', false)),
        ];
    }
    public function getDefaultActiveTab(): string|int|null
    {
        return 'active';
    }

}
