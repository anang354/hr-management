<?php

namespace App\Filament\ItPortal\Resources\AssetItems\Pages;

use App\Filament\ItPortal\Resources\AssetItems\AssetItemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAssetItems extends ListRecords
{
    protected static string $resource = AssetItemResource::class;
    protected string $view = 'filament.resources.asset-items.list-assets';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function hasTableHeader(): bool
    {
        return true;
    }
}
