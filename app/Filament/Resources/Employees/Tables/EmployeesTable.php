<?php

namespace App\Filament\Resources\Employees\Tables;

use App\Filament\Actions\Employees\PrintCardBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class EmployeesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->paginated([25,50,100])
            ->columns([
                TextColumn::make('employee_number')
                    ->label(__('employee.fields.employee_number'))
                    ->copyable()
                    ->searchable(),
                TextColumn::make('name')
                    ->label(__('employee.fields.name'))
                    ->copyable()
                    ->searchable(),
                TextColumn::make('department.name')
                    ->label(__('employee.fields.department')),
                TextColumn::make('employeePos.name')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label(__('employee.fields.position')),
                TextColumn::make('job')
                    ->label(__('employee.fields.job')),
                TextColumn::make('gender')
                    ->badge(),
                TextColumn::make('email')
                    ->label(__('employee.fields.email'))
                    ->copyable()
                    ->searchable(),
                TextColumn::make('phone')
                    ->label(__('employee.fields.phone'))
                    ->copyable()
                    ->searchable(),
                TextColumn::make('place_of_birth')
                    ->label(__('employee.fields.place_of_birth'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('birth_date')
                    ->label(__('employee.fields.birth_date'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('religion')
                    ->label(__('employee.fields.religion'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('mothers_name')
                    ->label(__('employee.fields.mothers_name'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('blood_group')
                    ->label(__('employee.fields.blood_group'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('last_education')
                    ->label(__('employee.fields.last_education'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('bank_account')
                    ->label(__('employee.fields.bank_account'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('bank_name')
                    ->label(__('employee.fields.bank_name'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('npwp')
                    ->label(__('employee.fields.npwp'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('bpjs_kesehatan')
                    ->label(__('employee.fields.bpjs_kesehatan'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('bpjs_ketenagakerjaan')
                    ->label(__('employee.fields.bpjs_ketenagakerjaan'))
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('department_id')
                    ->label(__('employee.fields.department'))
                    ->multiple()
                    ->relationship('department', 'name'),
                SelectFilter::make('employee_pos_id')
                    ->label(__('employee.fields.position'))
                    ->relationship('employeePos', 'name'),
                SelectFilter::make('gender')
                    ->label(__('employee.fields.gender'))
                    ->options(\App\Enums\Gender::class),
                SelectFilter::make('religion')
                    ->label(__('employee.fields.religion'))
                    ->multiple()
                    ->options(\App\Enums\Religion::class),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    PrintCardBulkAction::make(),
                ]),
            ]);
    }
}
