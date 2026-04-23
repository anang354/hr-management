<?php

namespace App\Filament\Resources\Contracts\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;

class ContractsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('employee.name')
                    ->label(__('contracts.fields.employee'))
                    ->searchable(),
                TextColumn::make('employee.department.name')
                    ->label(__('contracts.fields.department')),
                TextColumn::make('total_gaji')
                    ->label(__('contracts.fields.total_gaji'))
                    ->sortable()
                    ->numeric(),
                TextColumn::make('start_date')
                    ->label(__('contracts.fields.start_date'))
                    ->date(),
                TextColumn::make('end_date')
                    ->label(__('contracts.fields.end_date'))
                    ->sortable()
                    ->date(),
                TextColumn::make('contract_periode')
                    ->label(__('contracts.fields.contract_periode'))
                    ->numeric()
                    ->suffix(' Bulan'),
                TextColumn::make('days_to_expire')
                    ->label(__('contracts.fields.days_to_expire'))
                    ->sortable()
                    ->color(fn($state) => match (true) {
                        $state < 0 => 'danger',
                        $state <= 14 => 'warning',
                        default => 'success',
                    })
                    ->formatStateUsing(fn($state) => $state < 0 ? 'Expired' : $state . ' Days')
                    ->icon(fn($state) => $state < 0 ? 'heroicon-m-exclamation-triangle' : null)
                    ->badge(),
                TextColumn::make('contract_type')
                    ->label(__('contracts.fields.contract_type'))
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_active')
                    ->label(__('contracts.fields.is_active'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
            ])
            ->filters([
                SelectFilter::make('department')
                    ->label(__('contracts.fields.department'))
                    ->relationship('employee.department', 'name'),
            ])
            ->recordActions([
                Action::make('print')
                    ->url(fn($record) => route('contract-settings-show', $record->id))
                    ->color('info')
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-printer'),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn() => auth()->user()->isAdmin()),
                ]),
            ]);
    }
}
