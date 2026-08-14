<?php

namespace App\Filament\ItPortal\Resources\AssetItems;

use App\Filament\ItPortal\Resources\AssetItems\Pages\CreateAssetItem;
use App\Filament\ItPortal\Resources\AssetItems\Pages\EditAssetItem;
use App\Filament\ItPortal\Resources\AssetItems\Pages\ListAssetItems;
use App\Filament\ItPortal\Resources\AssetItems\Schemas\AssetItemForm;
use App\Filament\ItPortal\Resources\AssetItems\Tables\AssetItemsTable;
use App\Models\AssetItem;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AssetItemResource extends Resource
{
    protected static ?string $model = AssetItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;

    public static function getNavigationGroup(): ?string
    {
        return __('it_portal.assets.navigation_group');
    }
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return AssetItemForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AssetItemsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\AssetsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAssetItems::route('/'),
            'create' => CreateAssetItem::route('/create'),
            'edit' => EditAssetItem::route('/{record}/edit'),
        ];
    }
}
