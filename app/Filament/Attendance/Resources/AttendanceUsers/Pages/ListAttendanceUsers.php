<?php

namespace App\Filament\Attendance\Resources\AttendanceUsers\Pages;

use App\Filament\Attendance\Resources\AttendanceUsers\AttendanceUserResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAttendanceUsers extends ListRecords
{
    protected static string $resource = AttendanceUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
