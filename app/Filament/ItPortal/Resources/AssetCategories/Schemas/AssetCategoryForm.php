<?php

namespace App\Filament\ItPortal\Resources\AssetCategories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AssetCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('it_portal.name'))
                    ->required(),
                TextInput::make('code')
                    ->label(__('it_portal.code'))
                    ->required(),
                TextInput::make('description')
                    ->label(__('it_portal.description'))
            ]);
    }
}
