<?php

namespace App\Filament\Attendance\Pages;

use App\Models\BreakLog;
use BackedEnum;
use Carbon\Carbon;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BreakLogs extends Page implements HasTable
{
    use InteractsWithTable;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFilm;
    public static function getNavigationGroup(): ?string
    {
        return __('attendances.navigation_group');
    }

    public static function canAccess(): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->isHr();
    }
    protected string $view = 'filament.attendance.pages.break-logs';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                BreakLog::query()->where('created_at', '>=', Carbon::now()->subMonths(2))->orderByDesc('created_at')
            )
            ->columns([
                TextColumn::make('attendanceUser.display_name')
                    ->searchable()
                    ->label('Name'),
                TextColumn::make('attendanceUser.employee.department.name')
                    ->label('Department'),
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
                TextColumn::make('verify_method')
                    ->label('Login by')
                    ->badge()
            ])
            ->filters([
                SelectFilter::make('department')
                    ->relationship('attendanceUser.employee.department', 'name'),
                SelectFilter::make('type')
                    ->options([
                        2 => 'Keluar',
                        3 => 'Kembali',
                    ]),
            ])
            ->actions([
                // Define your table actions here
            ])
            ->bulkActions([
                // Define your table bulk actions here
            ]);
    }
}
