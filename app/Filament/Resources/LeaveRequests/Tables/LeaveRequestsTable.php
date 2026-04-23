<?php

namespace App\Filament\Resources\LeaveRequests\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LeaveRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('letter_number')
                    ->searchable()
                    ->label(__('leave_request.fields.letter_number')),
                TextColumn::make('employee.name')
                    ->searchable()
                    ->label(__('leave_request.fields.employee_id')),
                TextColumn::make('employee.department.name')
                    ->searchable()
                    ->label(__('employee.fields.department')),
                TextColumn::make('start_date')
                    ->label(__('leave_request.fields.start_date'))
                    ->date(),
                TextColumn::make('end_date')
                    ->label(__('leave_request.fields.end_date'))
                    ->date(),
                TextColumn::make('total_days')
                    ->label(__('leave_request.fields.total_days'))
                    ->suffix(' days'),
                TextColumn::make('leave_type')
                    ->label(__('leave_request.fields.leave_type')),
                TextColumn::make('user.name')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label(__('leave_request.contents.submitted_by')),
            ])
            ->filters([
                SelectFilter::make('department')
                    ->relationship('employee.department', 'name')
                    ->label(__('employee.fields.department')),
                SelectFilter::make('leave_type')
                    ->options(\App\Enums\LeaveType::class)
                    ->label(__('leave_request.fields.leave_type')),
            ])
            ->recordActions([
                Action::make('print')
                    ->visible(fn($record) => $record->status !== 'rejected')
                    ->url(function ($record) {
                        return route('leave-request-letter', $record->id);
                    })
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->openUrlInNewTab()
                    ->label(__('leave_request.actions.print')),
                EditAction::make()
                    ->visible(function ($record) {
                        return $record->status === 'pending';
                    }),
                Action::make('approved')
                    ->visible(function ($record) {
                        return $record->status === 'pending' && in_array(auth()->user()->role, ['admin', 'hr']);
                    })
                    ->label(__('leave_request.actions.approve'))
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->action(function ($record) {
                        $record->update([
                            'status' => 'approved',
                        ]);
                    }),
                Action::make('reject')
                    ->visible(function ($record) {
                        return $record->status === 'pending' && in_array(auth()->user()->role, ['admin', 'hr']);
                    })
                    ->label(__('leave_request.actions.reject'))
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->form([
                        TextInput::make('rejection_note'),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'status' => 'rejected',
                            'rejection_note' => $data['rejection_note']
                        ]);
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
