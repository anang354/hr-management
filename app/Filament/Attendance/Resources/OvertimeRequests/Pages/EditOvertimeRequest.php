<?php

namespace App\Filament\Attendance\Resources\OvertimeRequests\Pages;

use App\Filament\Attendance\Resources\OvertimeRequests\OvertimeRequestResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOvertimeRequest extends EditRecord
{
    protected static string $resource = OvertimeRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
