<?php

namespace App\Filament\Attendance\Resources\AttendanceUsers\Pages;

use App\Filament\Attendance\Resources\AttendanceUsers\AttendanceUserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAttendanceUser extends CreateRecord
{
    protected static string $resource = AttendanceUserResource::class;
}
