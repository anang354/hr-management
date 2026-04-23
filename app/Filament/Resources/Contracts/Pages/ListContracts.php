<?php

namespace App\Filament\Resources\Contracts\Pages;

use App\Filament\Resources\Contracts\ContractResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListContracts extends ListRecords
{
    protected static string $resource = ContractResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'active' => Tab::make(__('contracts.tabs.active'))
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_active', true)),
            'inactive' => Tab::make(__('contracts.tabs.inactive'))
                ->icon('heroicon-o-x-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_active', false)),
        ];
    }
    public function getDefaultActiveTab(): string|int|null
    {
        return 'active';
    }
}
