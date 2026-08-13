<?php

namespace App\Filament\ItPortal\Resources\AssetLocations\Pages;

use App\Filament\ItPortal\Resources\AssetLocations\AssetLocationResource;
use Filament\Resources\Pages\CreateRecord;
use LaraZeus\SpatieTranslatable\Resources\Pages\CreateRecord\Concerns\Translatable;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;

class CreateAssetLocation extends CreateRecord
{
    protected static string $resource = AssetLocationResource::class;
    use Translatable;
    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
        ];
    }
}
