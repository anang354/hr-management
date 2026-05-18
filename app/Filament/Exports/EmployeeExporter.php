<?php

namespace App\Filament\Exports;

use App\Models\Employee;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class EmployeeExporter extends Exporter
{
    protected static ?string $model = Employee::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('department.name'),
            ExportColumn::make('employeePos.name'),
            ExportColumn::make('employee_number'),
            ExportColumn::make('nik'),
            ExportColumn::make('name'),
            ExportColumn::make('job'),
            ExportColumn::make('gender'),
            ExportColumn::make('email'),
            ExportColumn::make('phone'),
            ExportColumn::make('address'),
            ExportColumn::make('residential_address'),
            ExportColumn::make('place_of_birth'),
            ExportColumn::make('birth_date'),
            ExportColumn::make('join_date'),
            ExportColumn::make('religion'),
            ExportColumn::make('mothers_name'),
            ExportColumn::make('blood_group'),
            ExportColumn::make('last_education'),
            ExportColumn::make('bank_account'),
            ExportColumn::make('bank_name'),
            ExportColumn::make('npwp'),
            ExportColumn::make('bpjs_kesehatan'),
            ExportColumn::make('bpjs_ketenagakerjaan'),
            ExportColumn::make('ptkp_status'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your employee export has completed and ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
