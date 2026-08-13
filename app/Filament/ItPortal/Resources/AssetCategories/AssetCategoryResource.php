<?php

namespace App\Filament\ItPortal\Resources\AssetCategories;

use App\Filament\ItPortal\Resources\AssetCategories\Pages\CreateAssetCategory;
use App\Filament\ItPortal\Resources\AssetCategories\Pages\EditAssetCategory;
use App\Filament\ItPortal\Resources\AssetCategories\Pages\ListAssetCategories;
use App\Filament\ItPortal\Resources\AssetCategories\Schemas\AssetCategoryForm;
use App\Filament\ItPortal\Resources\AssetCategories\Tables\AssetCategoriesTable;
use App\Models\AssetCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;

class AssetCategoryResource extends Resource
{
    protected static ?string $model = AssetCategory::class;
    use Translatable;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;
    public static function getNavigationGroup(): ?string
    {
        return __('it_portal.assets.navigation_group');
    }
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return AssetCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AssetCategoriesTable::configure($table);
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
            'index' => ListAssetCategories::route('/'),
            'create' => CreateAssetCategory::route('/create'),
            'edit' => EditAssetCategory::route('/{record}/edit'),
        ];
    }
}
