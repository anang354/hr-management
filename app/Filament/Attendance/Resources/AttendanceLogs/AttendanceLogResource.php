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

class AttendanceLogResource extends Resource
{
    protected static ?string $model = AttendanceLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBeaker;

    public static function getNavigationGroup(): ?string
    {
        return 'Attendance';
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
