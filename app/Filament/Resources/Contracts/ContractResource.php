<?php

namespace App\Filament\Resources\Contracts;

use App\Filament\Resources\Contracts\Pages\CreateContract;
use App\Filament\Resources\Contracts\Pages\EditContract;
use App\Filament\Resources\Contracts\Pages\ListContracts;
use App\Filament\Resources\Contracts\Schemas\ContractForm;
use App\Filament\Resources\Contracts\Tables\ContractsTable;
use App\Models\Contract;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ContractResource extends Resource
{
    protected static ?string $model = Contract::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentCurrencyDollar;

    public static function getNavigationLabel(): string
    {
        return __('contracts.navigation_label');
    }
    public static function getModelLabel(): string
    {
        return __('contracts.title');
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->select('*')
            ->selectRaw('DATEDIFF(end_date, CURRENT_DATE) as days_to_expire');
    }
    public static function form(Schema $schema): Schema
    {
        return ContractForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContractsTable::configure($table);
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
            'index' => ListContracts::route('/'),
            'create' => CreateContract::route('/create'),
            'edit' => EditContract::route('/{record}/edit'),
        ];
    }
}
