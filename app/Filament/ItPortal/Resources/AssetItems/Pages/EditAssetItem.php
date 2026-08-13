<?php

namespace App\Filament\ItPortal\Resources\AssetItems\Pages;

use App\Filament\ItPortal\Resources\AssetItems\AssetItemResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAssetItem extends EditRecord
{
    protected static string $resource = AssetItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
