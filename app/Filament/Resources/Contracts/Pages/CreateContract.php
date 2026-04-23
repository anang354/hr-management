<?php

namespace App\Filament\Resources\Contracts\Pages;

use App\Filament\Resources\Contracts\ContractResource;
use Filament\Resources\Pages\CreateRecord;

class CreateContract extends CreateRecord
{
    protected static string $resource = ContractResource::class;

    public function mutateFormDataBeforeCreate(array $data): array
    {
        $employee = \App\Models\Employee::find($data['employee_id']);
        $settings = \App\Models\ContractSetting::first(); // Ambil settingan terbaru

        // Kunci data saat ini ke dalam snapshot
        $data['snapshot_metadata'] = [
            'department' => $employee->department->name,
            'position' => 'Operator',
            'signatories' => [
                'hr' => [
                    'name' => $settings->hr_name,
                ],
                'sign1' => [
                    'name' => $settings->sign_1,
                    'position' => $settings->position_1,
                ],
                'sign2' => [
                    'name' => $settings->sign_2,
                    'position' => $settings->position_2,
                ],
            ],
        ];
        $data['user_id'] = auth()->user()->id;

        return $data;
    }
}
