<?php

namespace App\Filament\Attendance\Resources\AttendanceUsers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
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
                TextColumn::make('biometric_backups_count')
                ->counts('biometricBackups') // Nama fungsi relasi di model
                ->label('Jumlah Jari')
                ->badge() // Opsional: agar tampil seperti lencana
                ->color(fn (int $state): string => match (true) {
                    $state === 0 => 'danger',
                    $state === 1 => 'info',
                    default => 'success',
                })
                ->icon('heroicon-o-finger-print'),
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
                    // DeleteBulkAction::make(),
                ]),
            ]);
    }
}
