<?php

namespace App\Filament\Resources\Departments\Pages;

use App\Filament\Resources\Departments\DepartmentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use LaraZeus\SpatieTranslatable\Resources\Pages\EditRecord\Concerns\Translatable;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;

class EditDepartment extends EditRecord
{
    use Translatable;
    protected static string $resource = DepartmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            LocaleSwitcher::make(),
        ];
    }
}
