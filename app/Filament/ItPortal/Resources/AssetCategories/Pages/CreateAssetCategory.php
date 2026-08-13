<?php

namespace App\Filament\ItPortal\Resources\AssetCategories\Pages;

use App\Filament\ItPortal\Resources\AssetCategories\AssetCategoryResource;
use Filament\Resources\Pages\CreateRecord;
use LaraZeus\SpatieTranslatable\Resources\Pages\CreateRecord\Concerns\Translatable;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;

class CreateAssetCategory extends CreateRecord
{
    protected static string $resource = AssetCategoryResource::class;
    use Translatable;
    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
        ];
    }
}
