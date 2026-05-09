<?php

namespace App\Filament\Attendance\Resources\AttendanceUsers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AttendanceUsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('biometric_id')
                    ->label('Biometric ID'),
                TextColumn::make('employee.name')
                    ->label('Employee Name'),
                TextColumn::make('display_name')
                    ->label('Display Name')
                    ->searchable(),
                TextColumn::make('employee.department.name')
                    ->label('Departemen'),
                TextColumn::make('last_sync')
                    ->label('Last Sync')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable(),
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
