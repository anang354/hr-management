<?php

namespace App\Filament\ItPortal\Resources\AssetItems\RelationManagers;

use App\Filament\ItPortal\Resources\AssetItems\AssetItemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AssetsRelationManager extends RelationManager
{
    protected static string $relationship = 'assets';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('asset_code')
                    ->label(__('it_portal.assets.asset_code'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('specifications')
                    ->label(__('it_portal.assets.specifications'))
                    ->searchable(),
                TextColumn::make('location.name')
                    ->label(__('it_portal.assets.location')),
                TextColumn::make('status')
                    ->label(__('it_portal.assets.status'))
                    ->badge(),
                TextColumn::make('condition')
                    ->label(__('it_portal.assets.condition'))
                    ->badge(),
                TextColumn::make('user')
                    ->label(__('it_portal.assets.user'))
                    ->searchable(),
                TextColumn::make('notes')
                    ->label(__('it_portal.assets.notes'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                TextColumn::make('created_at')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('location')
                    ->label(__('it_portal.assets.location'))
                    ->relationship('location', 'name'),
                SelectFilter::make('status')
                    ->label(__('it_portal.assets.status'))
                    ->multiple()
                    ->options(\App\Models\Asset::STATUS),
                SelectFilter::make('condition')
                    ->label(__('it_portal.assets.condition'))
                    ->multiple()
                    ->options(\App\Models\Asset::CONDITION),
            ])
            ->headerActions([
                // CreateAction::make(),
            ]);
    }
}
