<?php

namespace App\Filament\Attendance\Resources\AttendanceLogs\Pages;

use App\Filament\Attendance\Resources\AttendanceLogs\AttendanceLogResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAttendanceLog extends CreateRecord
{
    protected static string $resource = AttendanceLogResource::class;
}
