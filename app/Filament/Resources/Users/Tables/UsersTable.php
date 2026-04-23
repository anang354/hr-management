<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Columns\TextColumn::make('name')
                    ->label(__('users.fields.name'))
                    ->searchable()
                    ->sortable(),
                Columns\TextColumn::make('department.name')
                    ->label(__('users.fields.department_id')),
                Columns\TextColumn::make('email')
                    ->label(__('users.fields.email'))
                    ->searchable()
                    ->sortable(),
                Columns\TextColumn::make('role')
                    ->label(__('users.fields.role'))
                    ->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('department')
                    ->relationship('department', 'name')
                    ->label(__('users.fields.department_id')),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
