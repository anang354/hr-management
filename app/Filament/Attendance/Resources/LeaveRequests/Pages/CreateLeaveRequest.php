<?php

namespace App\Filament\Attendance\Resources\LeaveRequests\Pages;

use App\Filament\Attendance\Resources\LeaveRequests\LeaveRequestResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLeaveRequest extends CreateRecord
{
    protected static string $resource = LeaveRequestResource::class;
    public function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->user()->id;
        if ($data['leave_session'] === 'halfday') {
            $data['end_date'] = $data['start_date'];
        }
        return $data;
    }
}

