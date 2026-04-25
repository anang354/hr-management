<?php

namespace App\Filament\Imports;

use App\Models\Employee;
use Carbon\Carbon;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class EmployeeImporter extends Importer
{
    protected static ?string $model = Employee::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('department')
                ->requiredMapping()
                ->relationship()
                ->rules(['required']),
            ImportColumn::make('employeePos')
                ->requiredMapping()
                ->relationship()
                ->rules(['required']),
            ImportColumn::make('employee_number')
                ->rules(['max:20']),
            ImportColumn::make('nik')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('job')
                ->rules(['max:100', 'nullable']),
            ImportColumn::make('gender')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('email')
                ->requiredMapping()
                ->rules(['required', 'email', 'max:255']),
            ImportColumn::make('phone')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('address')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('residential_address')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('place_of_birth')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('birth_date')
                ->requiredMapping()
                ->castStateUsing(function ($state) {
                    if (!$state)
                        return null;
                    return Carbon::createFromFormat('m/d/Y', $state)->format('Y-m-d');
                })
                ->rules(['required']),
            ImportColumn::make('join_date')
                ->requiredMapping()
                ->castStateUsing(function ($state) {
                    if (!$state)
                        return null;
                    return Carbon::createFromFormat('m/d/Y', $state)->format('Y-m-d');
                })
                ->rules(['required']),
            ImportColumn::make('religion')
                ->requiredMapping()
                ->rules(['required', 'max:100']),
            ImportColumn::make('mothers_name')
                ->rules(['max:255', 'nullable']),
            ImportColumn::make('blood_group')
                ->rules(['max:5', 'nullable']),
            ImportColumn::make('last_education')
                ->rules(['max:100', 'nullable']),
            ImportColumn::make('bank_account')
                ->rules(['max:100', 'nullable']),
            ImportColumn::make('bank_name')
                ->rules(['max:100', 'nullable']),
            ImportColumn::make('npwp')
                ->rules(['max:100', 'nullable']),
            ImportColumn::make('bpjs_kesehatan')
                ->rules(['max:100', 'nullable']),
            ImportColumn::make('bpjs_ketenagakerjaan')
                ->rules(['max:100', 'nullable']),
            ImportColumn::make('ptkp_status')
                ->rules(['max:10', 'nullable']),
            ImportColumn::make('is_active')
                ->requiredMapping()
                ->rules(['required']),
        ];
    }

    public function resolveRecord(): Employee
    {
        return new Employee();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your employee import has completed and ' . Number::format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
