<?php

namespace App\Filament\ItPortal\Resources\AssetLocations;

use App\Filament\ItPortal\Resources\AssetLocations\Pages\CreateAssetLocation;
use App\Filament\ItPortal\Resources\AssetLocations\Pages\EditAssetLocation;
use App\Filament\ItPortal\Resources\AssetLocations\Pages\ListAssetLocations;
use App\Filament\ItPortal\Resources\AssetLocations\Schemas\AssetLocationForm;
use App\Filament\ItPortal\Resources\AssetLocations\Tables\AssetLocationsTable;
use App\Models\AssetLocation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;

class AssetLocationResource extends Resource
{
    use Translatable;
    protected static ?string $model = AssetLocation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;
    public static function getNavigationGroup(): ?string
    {
        return __('it_portal.assets.navigation_group');
    }
    protected static ?int $navigationSort = 3;
    public static function form(Schema $schema): Schema
    {
        return AssetLocationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AssetLocationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAssetLocations::route('/'),
            'create' => CreateAssetLocation::route('/create'),
            'edit' => EditAssetLocation::route('/{record}/edit'),
        ];
    }
}
