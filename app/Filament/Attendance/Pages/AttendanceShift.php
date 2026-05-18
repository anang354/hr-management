<?php

namespace App\Filament\Attendance\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class AttendanceShift extends Page implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;
    protected string $view = 'filament.attendance.pages.attendance-shift';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    public static function getNavigationLabel(): string
    {
        return 'Shift';
    }

    public static function canAccess(): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->isHr();
    }

    protected function getShiftFormSchema(): array
    {
        return [
            Grid::make(2)->schema([
                TextInput::make('name')->required()->label('Nama Shift'),
                TextInput::make('break_minutes')->numeric()->default(60)->label('Durasi Istirahat (Menit)'),

                TimePicker::make('check_in_time')->seconds(false)->required()->label('Jam Masuk'),
                TimePicker::make('check_out_time')->seconds(false)->required()->label('Jam Pulang'),
                TimePicker::make('check_in_start')->seconds(false)->required()->label('Mulai Deteksi Masuk'),
                TimePicker::make('check_out_start')->seconds(false)->required()->label('Mulai Deteksi Keluar'),
                TimePicker::make('check_in_end')->seconds(false)->required()->label('Batas Akhir Masuk'),
                TimePicker::make('check_out_end')->seconds(false)->required()->label('Batas Akhir Keluar'),
                Toggle::make('is_cross_day')->default(false)->label('Lintas Hari')
                ->helperText('Aktifkan jika jam pulang berada di hari berikutnya'),
            ])
            ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createShift')
                ->label('Tambah Shift Baru')
                ->icon('heroicon-m-plus')
                ->modalHeading('Buat Aturan Shift')
                ->slideOver()
                ->form($this->getShiftFormSchema())
                ->action(function (array $data) {
                    \App\Models\AttendanceShift::create($data);
                    Notification::make()->title('Shift Berhasil Dibuat')->success()->send();
                }),
        ];
    }
    public function table(Table $table): Table
    {
        return $table->query(\App\Models\AttendanceShift::query())
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('check_in_time')->formatStateUsing(fn($state) => date('H:i', strtotime($state))),
                TextColumn::make('check_out_time')
                ->tooltip(fn($record) => $record->is_cross_day ? 'Lintas Hari' : '')
                ->html() // Penting agar tag <sup> terbaca
                ->formatStateUsing(function ($state, \App\Models\AttendanceShift $record) {
                    $formattedTime = \Carbon\Carbon::parse($state)->format('H:i');

                    if ($record->is_cross_day) {
                        return "{$formattedTime} <sup style='color: red; font-weight: bold;'>+1</sup>";
                    }

                    return $formattedTime;
                }),
            ])
            ->filters([])
            ->actions([
                EditAction::make()
                    ->label('Edit')
                    ->modalHeading('Ubah Aturan Shift')
                    ->slideOver()
                    ->form($this->getShiftFormSchema()),
                DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
