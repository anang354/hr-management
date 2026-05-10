<?php

namespace App\Filament\Attendance\Resources\OvertimeRequests\Pages;

use App\Filament\Attendance\Resources\OvertimeRequests\OvertimeRequestResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOvertimeRequest extends CreateRecord
{
    protected static string $resource = OvertimeRequestResource::class;
}
