<?php

namespace App\Filament\Resources\Departments\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;

class DepartmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
            TextInput::make('name')
            ->label(__('department.fields.name'))
            ->required(),
            TextInput::make('code')
            ->label(__('department.fields.code'))
            ->required(),
        ]);
    }
}
