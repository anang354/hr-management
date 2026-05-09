<?php

namespace App\Filament\Attendance\Resources\AttendanceLogs\Tables;

use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AttendanceLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('attendanceUser.biometric_id')
                    ->label('Biometric ID'),
                TextColumn::make('attendanceUser.display_name')
                    ->label('Display Name')
                    ->searchable(),
                TextColumn::make('attendance_date') // Nama unik untuk kolom tanggal
                    ->label('Tanggal')
                    ->state(fn($record) => $record->attendance_time)
                    ->date('Y-m-d')
                    ->sortable(),

                TextColumn::make('attendance_hour')
                    ->label('Jam')
                    ->state(fn($record) => $record->attendance_time)
                    ->time('H:i')
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Type')
                    ->badge(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Type')
                    ->options([
                        '0' => 'Masuk',
                        '1' => 'Keluar',
                    ]),
            ])
            ->recordActions([
                // EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
