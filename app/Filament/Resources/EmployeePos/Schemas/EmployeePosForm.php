<?php

namespace App\Filament\Resources\EmployeePos\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class EmployeePosForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
            TextInput::make('name')
            ->label(__('employee_pos.fields.name'))
            ->required(),
            TextInput::make('code')
            ->label(__('employee_pos.fields.code'))
            ->required(),
        ]);
    }
}
