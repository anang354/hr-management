<?php

namespace App\Filament\Attendance\Resources\AttendanceLogs\Tables;

use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AttendanceLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('attendanceUser.biometric_id')
                    ->label(__('attendances.fields.biometric_id')),
                TextColumn::make('attendanceUser.display_name')
                    ->label(__('attendances.fields.display_name'))
                    ->searchable(),
                TextColumn::make('attendance_date') // Nama unik untuk kolom tanggal
                    ->label(__('attendances.fields.date'))
                    ->state(fn($record) => $record->attendance_time)
                    ->date('Y-m-d')
                    ->sortable(),

                TextColumn::make('attendance_hour')
                    ->label(__('attendances.fields.time'))
                    ->state(fn($record) => $record->attendance_time)
                    ->time('H:i')
                    ->sortable(),
                TextColumn::make('type')
                    ->label(__('attendances.fields.type'))
                    ->badge(),
                TextColumn::make('verify_method')
                    ->label(__('attendances.fields.verify_method'))
                    ->badge()
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label(__('attendances.fields.type'))
                    ->options([
                        '0' => 'Masuk',
                        '1' => 'Keluar',
                    ]),
                SelectFilter::make('verify_method')
                    ->label(__('attendances.fields.verify_method'))
                    ->options([
                        '1' => 'Finger',
                        '2' => 'Lock',
                        '3' => 'Key',
                        '4' => 'Card',
                    ]),
            ])
            ->recordActions([
                // EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn() => auth()->user()->role === 'admin'),
                ]),
            ]);
    }
}
