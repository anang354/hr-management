<?php

namespace App\Filament\Actions\Attendances;

use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Database\Eloquent\Collection;

class AttendanceOverview
{

    public static function make(): Action
    {
        return Action::make('attendance_overview')
            ->label('Attendance Overview')
            ->icon('heroicon-o-clock')
            ->form([
                Radio::make('type')
                    ->options([
                        'kehadiran' => 'Kehadiran',
                        'overtime' => 'Waktu & Overtime',
                    ])
                    ->default('kehadiran')
                    ->required(),
                Select::make('department_id')
                    ->label('Department')
                    ->required()
                    ->options(\App\Models\Department::all()->pluck('name', 'id')),
                DatePicker::make('from')
                    ->label('From')
                    ->default(now()->startOfMonth())
                    ->required(),
                DatePicker::make('to')
                    ->label('To')
                    ->default(now()->endOfMonth())
                    ->required(),
            ])
            ->action(function (array $data) {
                return redirect()->route('attendance-overview', [
                    'type' => $data['type'],
                    'department_id' => $data['department_id'],
                    'from' => $data['from'],
                    'to' => $data['to'],
                ]);
            });
    }
}
