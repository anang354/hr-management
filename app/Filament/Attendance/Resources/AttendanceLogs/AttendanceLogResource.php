<?php

namespace App\Filament\Attendance\Resources\AttendanceLogs;

use App\Filament\Attendance\Resources\AttendanceLogs\Pages\CreateAttendanceLog;
use App\Filament\Attendance\Resources\AttendanceLogs\Pages\EditAttendanceLog;
use App\Filament\Attendance\Resources\AttendanceLogs\Pages\ListAttendanceLogs;
use App\Filament\Attendance\Resources\AttendanceLogs\Schemas\AttendanceLogForm;
use App\Filament\Attendance\Resources\AttendanceLogs\Tables\AttendanceLogsTable;
use App\Models\AttendanceLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AttendanceLogResource extends Resource
{
    protected static ?string $model = AttendanceLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBeaker;

    public static function getNavigationGroup(): ?string
    {
        return __('attendances.navigation_group');
    }

    public static function getNavigationLabel(): string
    {
        return __('attendances.attendance_log');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('created_at', '>=', \Carbon\Carbon::now()->subMonths(2));
    }

    public static function form(Schema $schema): Schema
    {
        // return AttendanceLogForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AttendanceLogsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAttendanceLogs::route('/'),
            // 'create' => CreateAttendanceLog::route('/create'),
            // 'edit' => EditAttendanceLog::route('/{record}/edit'),
        ];
    }
}
