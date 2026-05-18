<?php

namespace App\Filament\Attendance\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Schemas\Components\Grid;

class Holiday extends Page implements HasTable, HasForms
{
    use InteractsWithTable, InteractsWithForms;
    protected string $view = 'filament.attendance.pages.holiday';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;
    public static function canAccess(): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->isHr();
    }

    protected function getHolidayFormSchema(): array
    {
        return [
            Grid::make(2)->schema([
                DatePicker::make('holiday_date')->required()->label('Tanggal Hari Libur'),
                TextInput::make('description')->required()->label('Deskripsi Hari Libur'),
            ])
            ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createHoliday')
                ->label('Tambah Hari Libur')
                ->icon('heroicon-m-plus')
                ->modalHeading('Buat Hari Libur')
                ->slideOver()
                ->form($this->getHolidayFormSchema())
                ->action(function (array $data) {
                    \App\Models\Holiday::create($data);
                    Notification::make()->title('Hari Libur Berhasil Dibuat')->success()->send();
                }),
        ];
    }
    public function table(Table $table): Table
    {
        return $table->query(\App\Models\Holiday::query())
            ->columns([
                TextColumn::make('holiday_date')->formatStateUsing(fn($state) => date('d-m-Y', strtotime($state))),
                TextColumn::make('description'),
            ])
            ->filters([])
            ->actions([
                EditAction::make()
                    ->label('Edit')
                    ->modalHeading('Ubah Hari Libur')
                    ->slideOver()
                    ->form($this->getHolidayFormSchema()),
                DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
