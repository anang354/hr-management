<?php

namespace App\Filament\Resources\EmployeePos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EmployeePosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->color('danger'),
                TextColumn::make('name')
                    ->label(__('employee_pos.fields.name')),
                TextColumn::make('code')
                    ->label(__('employee_pos.fields.code')),
                TextColumn::make('employees_active')
                    ->label(__('department.fields.employee_active')),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
