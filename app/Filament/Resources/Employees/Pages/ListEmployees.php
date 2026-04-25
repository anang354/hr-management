<?php

namespace App\Filament\Resources\Employees\Pages;

use App\Filament\Exports\EmployeeExporter;
use App\Filament\Imports\EmployeeImporter;
use App\Filament\Resources\Employees\EmployeeResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Contracts\Database\Eloquent\Builder;

class ListEmployees extends ListRecords
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon('heroicon-o-plus-circle'),
            ImportAction::make()
                ->color('success')
                ->icon('heroicon-o-arrow-up-tray')
                ->importer(EmployeeImporter::class),
            ExportAction::make()
                ->color('info')
                ->icon('heroicon-o-arrow-down-tray')
                ->exporter(EmployeeExporter::class),
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
