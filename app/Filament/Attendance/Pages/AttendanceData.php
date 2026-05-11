<?php

namespace App\Filament\Attendance\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AttendanceData extends Page implements HasTable
{
    use InteractsWithTable;
    protected string $view = 'filament.attendance.pages.attendance-data';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    public static function getNavigationGroup(): ?string
    {
        return 'Attendance';
    }

    public function table(Table $table): Table
    {
        return $table->query(\App\Models\AttendanceData::query())
            ->columns([
                TextColumn::make('date')->label('Tanggal')
                ->date('d M Y'),
                TextColumn::make('attendance_user.display_name')
                    ->searchable()
                    ->label('Karyawan'),
                TextColumn::make('attendance_shift.name')
                    ->badge()
                    ->color(function ($record) {
                        if($record->attendance_shift->name === 'Day') {
                            return 'warning';
                        } elseif($record->attendance_shift->name === 'Night') {
                            return 'primary';
                        } else {
                            return 'info';
                        }
                    })
                    ->icon(function ($record) {
                        if($record->attendance_shift->name === 'Day') {
                            return 'heroicon-o-sun';
                        } elseif($record->attendance_shift->name === 'Night') {
                            return 'heroicon-o-moon';
                        } else {
                            return 'heroicon-o-sun';
                        }
                    })
                    ->label('Shift'),
                TextColumn::make('clock_in')
                    ->label('Jam Masuk')
                    ->formatStateUsing(fn($state) => date('H:i', strtotime($state)))
                    ->color(fn($record) => $record->coming_late > 0 ? 'danger' : ''),
                TextColumn::make('clock_out')
                    ->label('Jam Pulang')
                    ->formatStateUsing(fn($state) => date('H:i', strtotime($state)))
                    ->color(fn($record) => $record->early_leave > 0 ? 'danger' : ''),
                TextColumn::make('coming_late')
                    ->toggleable(true, isToggledHiddenByDefault: true)
                    ->color(fn($record) => $record->coming_late > 0 ? 'danger' : '')
                    ->label('Terlambat'),
                TextColumn::make('early_leave')
                    ->toggleable(true, isToggledHiddenByDefault: true)
                    ->color(fn($record) => $record->early_leave > 0 ? 'danger' : '')
                    ->label('Pulang Cepat'),
                TextInputColumn::make('overtime_hours')
                    ->label('Lembur'),
                TextInputColumn::make('working_hours')
                    ->label('Jam Kerja'),
                TextColumn::make('status')
                    ->badge()
                    ->label('Status'),
            ])
            ->filters([
                SelectFilter::make('attendance_shift_id')
                    ->label('Shift')
                    ->relationship('attendance_shift', 'name'),
                SelectFilter::make('status')
                    ->label('Status')
                    ->multiple()
                    ->options([
                        'Hadir' => 'Hadir',
                        'Sakit' => 'Sakit',
                        'Izin' => 'Izin',
                        'Cuti' => 'Cuti',
                        'Lembur' => 'Lembur',
                        'Alpha' => 'Alpha',
                        'Libur' => 'Libur',
                    ]),
            ])
            ->actions([])
            ->defaultSort('created_at', 'desc');
    }
}
