<?php

namespace App\Filament\Resources\EmployeePos\Pages;

use App\Filament\Resources\EmployeePos\EmployeePosResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use LaraZeus\SpatieTranslatable\Resources\Pages\EditRecord\Concerns\Translatable;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;

class EditEmployeePos extends EditRecord
{
    use Translatable;

    protected static string $resource = EmployeePosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            LocaleSwitcher::make(),
        ];
    }
}
