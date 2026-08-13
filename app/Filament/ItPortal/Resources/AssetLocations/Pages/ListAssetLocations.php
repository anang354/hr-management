<?php

namespace App\Filament\ItPortal\Resources\AssetLocations\Pages;

use App\Filament\ItPortal\Resources\AssetLocations\AssetLocationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAssetLocations extends ListRecords
{
    protected static string $resource = AssetLocationResource::class;
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
