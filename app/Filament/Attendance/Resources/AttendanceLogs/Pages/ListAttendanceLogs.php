<?php

namespace App\Filament\Attendance\Resources\AttendanceLogs\Pages;

use App\Filament\Attendance\Resources\AttendanceLogs\AttendanceLogResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAttendanceLogs extends ListRecords
{
    protected static string $resource = AttendanceLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),
        ];
    }
}
