<?php

namespace App\Filament\Exports;

use App\Models\Asset;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class AssetExporter extends Exporter
{
    protected static ?string $model = Asset::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('item.category.name')
                ->label('Category'),
            ExportColumn::make('item.brand')
                ->label('Brand'),
            ExportColumn::make('item.model')
                ->label('Model'),
            ExportColumn::make('location.name'),
            ExportColumn::make('asset_code'),
            ExportColumn::make('specifications'),
            ExportColumn::make('status'),
            ExportColumn::make('condition'),
            ExportColumn::make('user'),
            ExportColumn::make('ip_address'),
            ExportColumn::make('date_of_entry'),
            ExportColumn::make('damage_date'),
            ExportColumn::make('notes'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your asset export has completed and ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
