<?php

namespace App\Filament\Attendance\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AttendanceData extends Page implements HasTable
{
    use InteractsWithTable;
    protected string $view = 'filament.attendance.pages.attendance-data';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    public function table(Table $table): Table
    {
        return $table->query(\App\Models\AttendanceData::query())
            ->columns([
                TextColumn::make('date')->label('Tanggal'),
                TextColumn::make('attendance_user.display_name')
                    ->searchable()
                    ->label('Karyawan'),
                TextColumn::make('attendance_shift.name')
                    ->label('Shift'),
                TextColumn::make('clock_in')->label('Jam Masuk')
                ->formatStateUsing(fn($state) => date('H:i', strtotime($state)))
                ->color(fn($record) => $record->coming_late > 0 ? 'danger' : ''),
                TextColumn::make('clock_out')->label('Jam Pulang')
                ->formatStateUsing(fn($state) => date('H:i', strtotime($state)))
                ->color(fn($record) => $record->early_leave > 0 ? 'danger' : ''),
                TextColumn::make('coming_late')->label('Terlambat'),
                TextColumn::make('early_leave')->label('Pulang Cepat'),
                TextColumn::make('overtime_hours')->label('Lembur'),
                TextColumn::make('working_hours')->label('Jam Kerja'),
                TextColumn::make('status')->label('Status'),
            ])
            ->filters([
                SelectFilter::make('attendance_shift_id')
                    ->label('Shift')
                    ->relationship('attendance_shift', 'name'),
            ])
            ->actions([])
            ->defaultSort('created_at', 'desc');
    }
}
