<?php

namespace App\Filament\Resources\LeaveRequests\Schemas;

use App\Models\Employee;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;

class LeaveRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                TextInput::make('letter_number')
                    ->label(__('leave_request.fields.letter_number'))
                    ->default(function () {
                        return (new \App\Models\LeaveRequest())->generateLetterNumber();
                    })
                    ->disabled()
                    ->dehydrated(true),
                Select::make('employee_id')
                    ->label(__('leave_request.fields.employee_id'))
                    ->options(function () {
                        $user = auth()->user();
                        return Employee::query()
                            ->where('department_id', $user->department_id)
                            ->where('join_date', '<=', now()->subYear())
                            ->get()
                            ->mapWithKeys(fn($emp) => [$emp->id => "{$emp->name} (Sisa: {$emp->remaining_leave} hari)"]);
                    })
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function ($state, $set) {
                        $emp = Employee::find($state);
                        if ($emp && $emp->remaining_leave <= 0) {
                            Notification::make()->title('Hak cuti habis!')->danger()->send();
                            $set('employee_id', null);
                        }
                    }),
                Select::make('leave_type')
                    ->label(__('leave_request.fields.leave_type'))
                    ->options(\App\Enums\LeaveType::class)
                    ->required(),
                Select::make('leave_session')
                    ->label(__('leave_request.fields.leave_session'))
                    ->options([
                        'fullday' => __('leave_request.leave_session.fullday'),
                        'halfday' => __('leave_request.leave_session.halfday'),
                    ])
                    ->default('fullday')
                    ->live()
                    ->afterStateUpdated(function ($state, $set, $get) {
                        if ($state !== 'fullday') {
                            $set('end_date', $get('start_date'));
                            $set('total_days', 0.5);
                        } else {
                            $set('total_days', 1); // Default awal
                        }
                    }),
                DatePicker::make('start_date')
                    ->label(__('leave_request.fields.start_date'))
                    ->live()
                    ->required(),
                DatePicker::make('end_date')
                    ->label(__('leave_request.fields.end_date'))
                    ->hidden(fn($get) => $get('leave_session') !== 'fullday')
                    ->live()
                    ->required()
                    ->afterStateUpdated(function ($state, $get, $set) {
                        $start = \Carbon\Carbon::parse($get('start_date'));
                        $end = \Carbon\Carbon::parse($state);
                        $set('total_days', $start->diffInDays($end) + 1);
                    }),
                TextInput::make('total_days')
                    ->suffix(__('leave_request.contents.days'))
                    ->numeric()
                    ->label(__('leave_request.fields.total_days'))
                    ->required(),
                TextInput::make('reason')
                    ->label(__('leave_request.fields.reason'))
                    ->required(),
            ]);
    }
}
