<?php

namespace App\Filament\Attendance\Resources\AttendanceUsers;

use App\Filament\Attendance\Resources\AttendanceUsers\Pages\CreateAttendanceUser;
use App\Filament\Attendance\Resources\AttendanceUsers\Pages\EditAttendanceUser;
use App\Filament\Attendance\Resources\AttendanceUsers\Pages\ListAttendanceUsers;
use App\Filament\Attendance\Resources\AttendanceUsers\Schemas\AttendanceUserForm;
use App\Filament\Attendance\Resources\AttendanceUsers\Tables\AttendanceUsersTable;
use App\Models\AttendanceUser;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AttendanceUserResource extends Resource
{
    protected static ?string $model = AttendanceUser::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    public static function getNavigationGroup(): ?string
    {
        return 'Attendance';
    }

    public static function form(Schema $schema): Schema
    {
        return AttendanceUserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AttendanceUsersTable::configure($table);
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
            'index' => ListAttendanceUsers::route('/'),
            'create' => CreateAttendanceUser::route('/create'),
            'edit' => EditAttendanceUser::route('/{record}/edit'),
        ];
    }
}
