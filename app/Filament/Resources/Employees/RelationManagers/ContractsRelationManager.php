<?php

namespace App\Filament\Resources\Employees\RelationManagers;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ContractsRelationManager extends RelationManager
{
    protected static string $relationship = 'contracts';

    public function canAccess(): bool
    {
        return auth()->user()->can('viewAny', \App\Models\Contract::class);
    }


    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                TextEntry::make('employee.name'),
                TextEntry::make('employee.department.name')->label('Department'),
                TextEntry::make('contract_type'),
                TextEntry::make('start_date')->date('d M Y'),
                TextEntry::make('end_date')->date('d M Y'),
                TextEntry::make('contract_periode')
                    ->badge()
                    ->color('primary')
                    ->suffix(' Bulan'),
                Section::make('Detail Salary')
                    ->columnSpanFull()
                    ->columns(2)
                    ->components([
                        TextEntry::make('gaji_pokok')->numeric(),
                        TextEntry::make('tunjangan_jabatan')->numeric(),
                        TextEntry::make('tunjangan_bahasa')->numeric(),
                        TextEntry::make('tunjangan_keahlian')->numeric(),
                        TextEntry::make('tunjangan_transportasi')->numeric(),
                        TextEntry::make('tunjangan_lainnya')->numeric(),
                        TextEntry::make('total_gaji')->numeric()->color('success'),
                    ])
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('contract_number')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('contract_number')
                    ->searchable(),
                TextColumn::make('total_gaji')
                    ->numeric(),
                TextColumn::make('start_date')
                    ->date(),
                TextColumn::make('end_date')
                    ->date(),
                IconColumn::make('is_active')->boolean(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('print')
                    ->url(fn($record) => route('contract-settings-show', $record->id))
                    ->color('info')
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-printer'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
