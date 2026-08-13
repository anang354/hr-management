<?php

namespace App\Filament\ItPortal\Resources\AssetLocations\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AssetLocationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('it_portal.name'))
                    ->required(),
            ]);
    }
}
