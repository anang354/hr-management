<?php

namespace App\Filament\ItPortal\Resources\AssetLocations\Pages;

use App\Filament\ItPortal\Resources\AssetLocations\AssetLocationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use LaraZeus\SpatieTranslatable\Resources\Pages\EditRecord\Concerns\Translatable;

class EditAssetLocation extends EditRecord
{
    protected static string $resource = AssetLocationResource::class;
    use Translatable;
    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            LocaleSwitcher::make(),
        ];
    }
}
