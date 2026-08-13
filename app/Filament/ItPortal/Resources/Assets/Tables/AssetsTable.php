<?php

namespace App\Filament\ItPortal\Resources\Assets\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AssetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('asset_code')
                    ->label(__('it_portal.assets.asset_code'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('item.category.name')
                    ->label(__('it_portal.assets.category')),
                TextColumn::make('item.model')
                    ->label(__('it_portal.assets.model'))
                    ->formatStateUsing(fn ($record) => $record->item->brand . ' ' . $record->item->model),
                TextColumn::make('specifications')
                    ->toggleable()
                    ->label(__('it_portal.assets.specifications'))
                    ->getStateUsing(function ($record) {
                        $specifications = $record->specifications;
                        if (! is_array($specifications) || empty($specifications)) {
                            return '-';
                        }
                        $format = function (array $data) use (&$format): string {
                            return collect($data)
                                ->map(function ($value, $key) use (&$format) {
                                    if (is_array($value)) {
                                        return "{$key} : {$format($value)}";
                                    }
                                    return "{$key} : {$value}";
                                })
                                ->implode(', ');
                        };
                        return $format($specifications);
                    })
                    ->wrap(),
                TextColumn::make('location.name')
                    ->label(__('it_portal.assets.location'))
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('it_portal.assets.status'))
                    ->badge()
                    ->searchable(),
                TextColumn::make('condition')
                    ->label(__('it_portal.assets.condition'))
                    ->badge()
                    ->searchable(),
                TextColumn::make('user')
                    ->label(__('it_portal.assets.user'))
                    ->searchable(),
                TextColumn::make('ip_address')
                    ->label(__('it_portal.assets.ip_address'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('date_of_entry')
                    ->label(__('it_portal.assets.date_of_entry'))
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                TextColumn::make('damage_date')
                    ->label(__('it_portal.assets.damage_date'))
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                TextColumn::make('notes')
                    ->label(__('it_portal.assets.notes'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('location')
                    ->label(__('it_portal.assets.location'))
                    ->relationship('location', 'name'),
                SelectFilter::make('item.category')
                    ->label(__('it_portal.assets.category'))
                    ->relationship('item.category', 'name'),
                SelectFilter::make('status')
                    ->label(__('it_portal.assets.status'))
                    ->multiple()
                    ->options(\App\Models\Asset::STATUS),
                SelectFilter::make('condition')
                    ->label(__('it_portal.assets.condition'))
                    ->multiple()
                    ->options(\App\Models\Asset::CONDITION),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ExportBulkAction::make()
                        ->label('Download')
                        ->color('success')
                        ->exporter(\App\Filament\Exports\AssetExporter::class),
                ]),
            ]);
    }
}
