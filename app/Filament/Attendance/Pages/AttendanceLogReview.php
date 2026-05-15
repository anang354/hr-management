<?php

namespace App\Filament\Attendance\Pages;

use App\Models\AttendanceLog;
use App\Models\AttendanceUser;
use BackedEnum;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class AttendanceLogReview extends Page implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;
    protected string $view = 'filament.attendance.pages.attendance-log-review';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::DocumentMagnifyingGlass;
    public static function getNavigationGroup(): ?string
    {
        return 'Attendance';
    }
    public ?int $attendance_user_id = null;
    public ?string $date_from = null;
    public ?string $date_to = null;

    public function schema(Schema $schema): Schema
    {
        return $schema->components([
            DatePicker::make('date_from')
                    ->label(__('attendances.filters.from'))
                    ->default(now()->startOfMonth())
                    ->native(false)
                    ->live()
                    ->afterStateUpdated(fn() => $this->resetTable()),
            DatePicker::make('date_to')
                ->label(__('attendances.filters.until'))
                ->default(now()->endOfMonth())
                ->native(false)
                ->live()
                ->afterStateUpdated(fn() => $this->resetTable()),
            Select::make('attendance_user_id')
                ->label(__('attendances.fields.employee'))
                ->options(function () {
                    return AttendanceUser::all()->pluck('display_name', 'biometric_id');
                })
                ->searchable()
                ->live()
                ->afterStateUpdated(fn() => $this->resetTable())
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                AttendanceLog::query()
                ->where('biometric_id', $this->attendance_user_id)
                ->whereBetween('attendance_time', [Carbon::parse($this->date_from)->startOfDay(), Carbon::parse($this->date_to)->endOfDay()])
                ->orderByDesc('created_at')
            )
            ->columns([
                TextColumn::make('attendanceUser.display_name')
                    ->label('Name'),
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
            ]);

    }
     public function getSelectedAttendanceUserProperty()
    {
        return AttendanceUser::where('biometric_id', $this->attendance_user_id)->first();
    }
}
