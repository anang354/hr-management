<?php

namespace App\Filament\ItPortal\Resources\AssetItems\Tables;

use App\Models\AssetItem;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\PaginationMode;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AssetItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(AssetItem::query()->when(request('search'), function ($q, $search) {
                $q->where('model', 'like', "%{$search}%")
                ->orWhere('brand', 'like', "%{$search}%");
            }))
            ->defaultPaginationPageOption(25)
            ->paginationMode(PaginationMode::Simple)
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('brand')->searchable(),
                TextColumn::make('model')->searchable(),
                TextColumn::make('category.name')->label(__('asset_item.fields.category')),
            ])
            ->filters([
                // SelectFilter::make('category')
                //     ->relationship('category', 'name')
                //     ->label('Category'),
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
