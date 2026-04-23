<?php

namespace App\Filament\Resources\Departments\Pages;

use App\Filament\Resources\Departments\DepartmentResource;
use Filament\Resources\Pages\CreateRecord;
use LaraZeus\SpatieTranslatable\Resources\Pages\CreateRecord\Concerns\Translatable;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
class CreateDepartment extends CreateRecord
{
    protected static string $resource = DepartmentResource::class;
    use Translatable;
    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
            // ...
        ];
    }
}
