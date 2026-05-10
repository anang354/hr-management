<?php

namespace App\Filament\Attendance\Resources\OvertimeRequests\Tables;

use App\Enums\OvertimeStatus;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use App\Filament\Attendance\Resources\OvertimeRequests\OvertimeRequestResource;

class OvertimeRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'asc')
            ->columns([
                TextColumn::make('overtime_date')->date('D, d F Y')->sortable()
                    ->label(__('overtime_request.fields.overtime_date')),
                TextColumn::make('user.name')
                    ->label(__('overtime_request.fields.user_id'))
                    ->searchable(),
                TextColumn::make('user.department.name')
                    ->visible(fn() => auth()->user()->role === 'hr' || auth()->user()->role === 'admin')
                    ->label(__('overtime_request.fields.department')),
                TextColumn::make('employees_items')->label(__('overtime_request.fields.employees_items')),
                TextColumn::make('approved_items_count')
                    ->label(__('overtime_request.fields.approved'))
                    ->color('success'),
                TextColumn::make('rejected_items_count')
                    ->label(__('overtime_request.fields.rejected'))
                    ->color('danger'),
                TextColumn::make('status')
                    ->label(__('overtime_request.fields.status'))
                    ->badge()
                    ->tooltip(fn($record) => $record->rejected_by)
                    ->color(fn(OvertimeStatus $state): string => $state->getColor()),
                TextColumn::make('reason')
                    ->label(__('overtime_request.fields.reason'))
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->tooltip(fn($record) => $record->rejected_by),
            ])
            ->filters([
                SelectFilter::make('department')
                    ->relationship('user.department', 'name')
                    ->preload()
                    ->visible(fn() => auth()->user()->role === 'hr' || auth()->user()->role === 'admin')
                    ->searchable(),
            ])
            ->recordActions([
                Action::make('detail')
                    ->label(__('overtime_request.actions.detail'))
                    // ->visible(fn() => auth()->user()->role === 'hr' || auth()->user()->role === 'admin')
                    ->color('info')
                    ->icon('heroicon-o-eye')
                    ->url(fn(\App\Models\OvertimeRequest $record): string => OvertimeRequestResource::getUrl('detail', ['record' => $record])),
                EditAction::make()
                    ->visible(fn($record) => $record->status === OvertimeStatus::Pending),
                Action::make('approve')
                    ->label(__('overtime_request.actions.approve'))
                    ->color('success')
                    ->icon('heroicon-m-check-circle')
                    // LOGIKA VISIBILITAS: Hanya muncul jika gilirannya tiba
                    ->visible(fn($record) => match ($record->status) {
                        OvertimeStatus::Pending => auth()->user()->role === 'manager',
                        OvertimeStatus::ManagerApproved => auth()->user()->role === 'hr',
                        default => false,
                    })
                    ->action(function ($record) {
                        $nextStatus = match ($record->status) {
                            OvertimeStatus::Pending => OvertimeStatus::ManagerApproved,
                            OvertimeStatus::ManagerApproved => OvertimeStatus::Approved,
                            default => $record->status,
                        };
                        $record->update(['status' => $nextStatus]);
                    })
                    ->requiresConfirmation(),

                // Tombol REJECT
                Action::make('reject')
                    ->label(__('overtime_request.actions.reject'))
                    ->color('danger')
                    ->icon('heroicon-m-x-circle')
                    ->visible(fn($record) => match ($record->status) {
                        OvertimeStatus::Pending => auth()->user()->role === 'manager',
                        OvertimeStatus::ManagerApproved => auth()->user()->role === 'hr',
                        default => false, // Jika 'approved' atau 'rejected', tombol hilang otomatis
                    })
                    ->form([
                        Textarea::make('reason')
                            ->label(__('overtime_request.contents.reason_for_rejection'))
                            ->required()
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'status' => OvertimeStatus::Rejected,
                            'reason' => $data['reason'],
                            'rejected_by' => auth()->user()->role,
                        ]);
                    }),
                DeleteAction::make()
                    ->visible(function ($record) {
                        if (in_array(auth()->user()->role, ['admin', 'hr'])) {
                            return true;
                        }
                        return $record->status === OvertimeStatus::Pending;
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn() => in_array(auth()->user()->role, ['hr', 'admin'])),
                ]),
            ]);
    }
}
