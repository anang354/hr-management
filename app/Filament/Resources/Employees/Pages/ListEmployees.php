<?php

namespace App\Filament\Resources\Employees\Pages;

use App\Filament\Exports\EmployeeExporter;
use App\Filament\Imports\EmployeeImporter;
use App\Filament\Resources\Employees\EmployeeResource;
use App\Filament\Resources\Employees\Pages\ExitEmployees;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
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
            ActionGroup::make([
                ImportAction::make()
                    ->label(__('employee.actions.import'))
                    ->visible(function () {
                        return auth()->user()->isAdmin() || auth()->user()->isHr();
                    })
                    ->color('success')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->importer(EmployeeImporter::class),
                ExportAction::make()
                    ->label(__('employee.actions.export'))
                    ->color('info')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->exporter(EmployeeExporter::class),
                Action::make('exit')
                    ->label(__('employee.actions.lay_off'))
                    ->visible(function () {
                        return auth()->user()->isAdmin() || auth()->user()->isHr();
                    })
                    ->color('danger')
                    ->icon('heroicon-o-user-minus')
                    ->url(ExitEmployees::getUrl()),
            ])
                ->label(__('employee.actions.more_actions'))
                ->icon('heroicon-m-ellipsis-vertical')
                ->color('info')
                ->button(),
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
