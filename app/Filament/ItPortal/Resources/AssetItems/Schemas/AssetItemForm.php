<?php

namespace App\Filament\ItPortal\Resources\AssetItems\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AssetItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('asset_category_id')
                    ->label(__('Select Category'))
                    ->relationship('category', 'name')
                    ->native(false)
                    ->required(),
                TextInput::make('brand')
                    ->label('Brand')
                    ->required(),
                TextInput::make('model')
                    ->label('Model'),
                TextInput::make('description')
                    ->label('Description'),
                FileUpload::make('photo')
                    ->label('Photo')
                    ->disk('public')
                    ->directory('assets')
                    ->imageEditor()
                    ->imageEditorAspectRatioOptions([
                        '4:3',
                        '16:9',
                        '1:1',
                    ])
                    ->preserveFilenames(false)
            ]);
    }
}
