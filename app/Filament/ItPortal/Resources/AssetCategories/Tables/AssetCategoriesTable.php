<?php

namespace App\Filament\ItPortal\Resources\AssetCategories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AssetCategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('it_portal.name')),
                TextColumn::make('code')
                    ->label(__('it_portal.code')),
                TextColumn::make('assets_count')
                    ->counts('assets')
                    ->label(__('it_portal.total_items')),
                TextColumn::make('active_assets_count')
                    ->counts('activeAssets')
                    ->color('success')
                    ->label(__('it_portal.asset_status.active')),
                 TextColumn::make('inactive_assets_count')
                    ->counts('inactiveAssets')
                    ->color('info')
                    ->label(__('it_portal.asset_status.inactive')),
                 TextColumn::make('maintenance_assets_count')
                    ->counts('maintenanceAssets')
                    ->color('warning')
                    ->label(__('it_portal.asset_status.maintenance')),
                 TextColumn::make('retired_assets_count')
                    ->color('danger')
                    ->counts('retiredAssets')
                    ->label(__('it_portal.asset_status.retired')),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
