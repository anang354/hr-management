<?php

namespace App\Filament\Resources\Departments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DepartmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
            TextColumn::make('name')
            ->label(__('department.fields.name')),
            TextColumn::make('code')
            ->label(__('department.fields.code')),
            TextColumn::make('employees_active')
            ->label(__('department.fields.employee_active')),
        ])
            ->filters([
            //
        ])
            ->recordActions([
            EditAction::make(),
            DeleteAction::make(),
        ])
            ->toolbarActions([
            BulkActionGroup::make([
                DeleteBulkAction::make(),
            ]),
        ]);
    }
}
