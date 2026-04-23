<?php

namespace App\Filament\Resources\EmployeePos\Pages;

use App\Filament\Resources\EmployeePos\EmployeePosResource;
use Filament\Resources\Pages\CreateRecord;
use LaraZeus\SpatieTranslatable\Resources\Pages\CreateRecord\Concerns\Translatable;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;

class CreateEmployeePos extends CreateRecord
{
    use Translatable;
    protected static string $resource = EmployeePosResource::class;
    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
            // ...
        ];
    }
}
