<?php

namespace App\Filament\Attendance\Resources\OvertimeRequests\Pages;

use App\Enums\OvertimeItemStatus;
use App\Enums\OvertimeStatus;
use App\Filament\Attendance\Resources\OvertimeRequests\OvertimeRequestResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;

class DetailOvertime extends ViewRecord implements HasTable
{
    use InteractsWithTable;
    protected static string $resource = OvertimeRequestResource::class;

    protected string $view = 'filament.resources.overtime-requests.pages.detail-overtime';

    protected function getHeaderActions(): array
    {
        return [
            // Tombol Approve untuk Manager
            Action::make('manager_approve')
                ->label(__('overtime_request.actions.approve'))
                ->icon(Heroicon::CheckBadge)
                ->color('info')
                ->visible(function () {
                    $user = auth()->user();
                    $record = $this->record;
                    return $user->role === 'manager'
                        && $record->status === OvertimeStatus::Pending;
                })
                ->action(function () {
                    $this->record->update([
                        'status' => OvertimeStatus::ManagerApproved,
                    ]);

                    Notification::make()
                        ->title(__('overtime_request.notifications.request_approved'))
                        ->success()
                        ->send();

                    $this->refreshFormData(['status']);
                }),
            Action::make('manager_reject')
                ->label(__('overtime_request.actions.reject'))
                ->icon(Heroicon::XCircle)
                ->color('danger')
                ->visible(function () {
                    $user = auth()->user();
                    $record = $this->record;
                    return $user->role === 'manager'
                        && $record->status === OvertimeStatus::Pending;
                })
                ->form([
                    TextInput::make('reason')
                        ->label(__('overtime_request.contents.reason_for_rejection'))
                        ->required()
                ])
                ->action(function ($record, array $data) {
                    $record->update([
                        'status' => OvertimeStatus::Rejected,
                        'reason' => $data['reason'],
                        'rejected_by' => auth()->user()->name,
                    ]);

                    Notification::make()
                        ->title(__('overtime_request.notifications.request_rejected'))
                        ->danger()
                        ->send();

                    $this->refreshFormData(['status']);
                }),

            // Tombol Approve untuk HR
            Action::make('hr_approve')
                ->label(__('overtime_request.actions.approve'))
                ->icon(Heroicon::CheckCircle)
                ->color('success')
                ->visible(function () {
                    $user = auth()->user();
                    $record = $this->record;
                    return $user->role === 'hr'
                        && in_array($record->status, [
                            OvertimeStatus::ManagerApproved,
                        ]);
                })
                ->action(function () {
                    $this->record->update([
                        'status' => OvertimeStatus::Approved,
                    ]);

                    Notification::make()
                        ->title(__('overtime_request.notifications.request_approved'))
                        ->success()
                        ->send();

                    $this->refreshFormData(['status']);
                }),
            Action::make('hr_reject')
                ->label(__('overtime_request.actions.reject'))
                ->icon(Heroicon::XCircle)
                ->color('danger')
                ->visible(function () {
                    $user = auth()->user();
                    $record = $this->record;
                    return $user->role === 'hr'
                        && $record->status === OvertimeStatus::ManagerApproved;
                })
                ->form([
                    TextInput::make('reason')
                        ->label(__('overtime_request.contents.reason_for_rejection'))
                        ->required()
                ])
                ->action(function ($record, array $data) {
                    $record->update([
                        'status' => OvertimeStatus::Rejected,
                        'reason' => $data['reason'],
                        'rejected_by' => auth()->user()->name,
                    ]);

                    Notification::make()
                        ->title(__('overtime_request.notifications.request_rejected'))
                        ->danger()
                        ->send();

                    $this->refreshFormData(['status']);
                }),
        ];
    }
    public function table(Table $table): Table
    {
        return $table
            ->query(\App\Models\OvertimeRequestItem::query()->where('overtime_request_id', $this->record->id))
            ->columns([
                TextColumn::make('employee.name')->label(__('overtime_items.fields.name')),
                TextColumn::make('start_time')->label(__('overtime_items.fields.start_time')),
                TextColumn::make('end_time')->label(__('overtime_items.fields.end_time')),
                TextColumn::make('overtime_hours')->label(__('overtime_items.fields.overtime_hours')),
                TextColumn::make('status')
                    ->label(__('overtime_items.fields.status'))
                    ->tooltip(fn($record) => $record->reason)
                    ->badge(),
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                Action::make('approve')
                    ->label(__('overtime_request.actions.approve'))
                    ->icon(Heroicon::CheckCircle)
                    ->color('success')
                    ->visible(function ($record) {
                        return $record->status === OvertimeItemStatus::Pending && auth()->user()->role === 'hr';
                    })
                    ->action(function ($record) {
                        $record->update([
                            'status' => OvertimeItemStatus::Approved
                        ]);
                    }),
                Action::make('reject')
                    ->label(__('overtime_request.actions.reject'))
                    ->icon(Heroicon::XCircle)
                    ->color('danger')
                    ->visible(function ($record) {
                        return $record->status === OvertimeItemStatus::Pending && auth()->user()->role === 'hr';
                    })
                    ->form([
                        TextInput::make('reason')
                            ->label('Reason for Rejection')
                            ->required()
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'status' => OvertimeItemStatus::Rejected,
                            'reason' => $data['reason'],
                        ]);
                    })
                    ->requiresConfirmation(),
            ]);
    }
}
