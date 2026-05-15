<?php

namespace App\Filament\Attendance\Pages;

use App\Enums\AttendanceStatus;
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

    protected function getHeaderActions(): array
    {
        return [
            \App\Filament\Actions\Attendances\AttendanceOverview::make(),
        ];
    }

    public function table(Table $table): Table
    {
        return $table->query(\App\Models\AttendanceData::query()->where('date', '>=', \Carbon\Carbon::now()->subMonths(2)))
            ->columns([
                TextColumn::make('date')->label('Tanggal')
                ->date('d M Y'),
                TextColumn::make('attendance_user.display_name')
                    ->searchable()
                    ->label('Karyawan'),
                TextColumn::make('attendance_user.employee.department.name')
                    ->label('Departemen'),
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
                TextInputColumn::make('coming_late')
                    // ->color(fn($record) => $record->coming_late > 0 ? 'danger' : '')
                    ->width('5%')
                    ->label('Terlambat'),
                TextInputColumn::make('early_leave')
                    // ->color(fn($record) => $record->early_leave > 0 ? 'danger' : '')
                    ->width('5%')
                    ->label('Pulang Cepat'),
                TextInputColumn::make('overtime_hours')
                    ->width('5%')
                    ->label('Lembur'),
                TextColumn::make('overtime_fix_hours')
                    ->label('Jam Lembur'),
                TextColumn::make('working_hours')
                    ->toggleable(true, isToggledHiddenByDefault: false)
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
            ->actions([
                \Filament\Actions\Action::make('koreksi')
                    ->label('Koreksi')
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning')
                    ->visible(fn($record) => $record->status === AttendanceStatus::Hadir || $record->status === AttendanceStatus::Lembur)
                    ->form([
                        \Filament\Forms\Components\Select::make('shift_id')
                            ->label('Pilih Shift yang Benar')
                            ->options(\App\Models\AttendanceShift::pluck('name', 'id'))
                            ->required()
                            ->default(fn ($record) => $record->shift_id),
                    ])
                    ->action(function ($record, array $data, \App\Services\AttendanceProcessor $processor) {
                        $processor->reprocessManual($record->id, $data['shift_id']);

                        \Filament\Notifications\Notification::make()
                            ->title('Data Berhasil Dikoreksi')
                            ->success()
                            ->send();
                    })
            ])
            ->defaultSort('created_at', 'desc');
    }
}
