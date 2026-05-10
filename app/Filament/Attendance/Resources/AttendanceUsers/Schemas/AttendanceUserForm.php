<?php

namespace App\Filament\Attendance\Resources\AttendanceUsers\Schemas;

use App\Models\AttendanceUser;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class AttendanceUserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('employee_id')
                ->relationship('employee', 'name', fn ($query, ?AttendanceUser $record) => $query->where('is_active', true)
                    ->where(function ($q) use ($record) {
                        $q->doesntHave('attendanceUser');
                        if ($record) {
                            $q->orWhere('id', $record->employee_id);
                        }
                    }))
                ->afterStateUpdated(function (Set $set, ?string $state) {
                    $employee = \App\Models\Employee::findOrFail($state);
                    $set('display_name', rtrim(substr($employee->name, 0, 20)));
                    $set('password', \Carbon\Carbon::parse($employee->birth_date)->format('dmY'));
                })
                ->reactive()
                ->searchable()
                ->preload(),
                TextInput::make('biometric_id')
                    ->default(fn () => AttendanceUser::getNextAvailableBiometricId())
                    ->numeric()
                    ->required(),
                TextInput::make('display_name')
                    ->label('Display Name')
                    ->maxLength(25)
                    ->required(),
                Select::make('privilege')
                    ->label('Privilege')
                    ->options([
                        0 => 'User',
                        14 => 'Admin',
                    ])
                    ->default(0)
                    ->required(),
                TextInput::make('card_number')
                    ->label('Card Number')
                    ->numeric(),
                TextInput::make('password')
                    ->label('Password')
                    ->revealable()
                    ->password(),
                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ]);
    }
}
