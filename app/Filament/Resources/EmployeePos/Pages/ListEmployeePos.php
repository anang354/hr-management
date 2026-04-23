<?php

namespace App\Filament\Resources\EmployeePos\Pages;

use App\Filament\Resources\EmployeePos\EmployeePosResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEmployeePos extends ListRecords
{
    protected static string $resource = EmployeePosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
