<?php

namespace App\Filament\Resources\EmployeePos;

use App\Filament\Resources\EmployeePos\Pages\CreateEmployeePos;
use App\Filament\Resources\EmployeePos\Pages\EditEmployeePos;
use App\Filament\Resources\EmployeePos\Pages\ListEmployeePos;
use App\Filament\Resources\EmployeePos\Schemas\EmployeePosForm;
use App\Filament\Resources\EmployeePos\Tables\EmployeePosTable;
use App\Models\EmployeePos;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;

class EmployeePosResource extends Resource
{
    use Translatable;
    protected static ?string $model = EmployeePos::class;

    public static function canAccess(): bool
    {
        return auth()->user()->isAdmin()|| auth()->user()->isHr();
    }

    public static function getModelLabel(): string
    {
        return __('employee_pos.label');
    }

    // 2. Menterjemahkan Nama Jamak (misal di menu navigasi samping)
    public static function getPluralModelLabel(): string
    {
        return __('employee_pos.plural_label');
    }

    public static function getNavigationGroup(): string
    {
        return __('employee_pos.group_title');
    }
    public static function getNavigationLabel(): string
    {
        return __('employee_pos.navigation_label');
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCubeTransparent;

    public static function form(Schema $schema): Schema
    {
        return EmployeePosForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EmployeePosTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEmployeePos::route('/'),
            'create' => CreateEmployeePos::route('/create'),
            'edit' => EditEmployeePos::route('/{record}/edit'),
        ];
    }
}
