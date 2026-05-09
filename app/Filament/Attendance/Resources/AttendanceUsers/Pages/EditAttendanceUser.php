<?php

namespace App\Filament\Attendance\Resources\AttendanceUsers\Pages;

use App\Filament\Attendance\Resources\AttendanceUsers\AttendanceUserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAttendanceUser extends EditRecord
{
    protected static string $resource = AttendanceUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
