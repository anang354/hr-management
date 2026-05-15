<?php

namespace App\Filament\Attendance\Pages;

use App\Models\AttendanceData;
use App\Models\AttendanceUser;
use App\Models\Employee;
use App\Models\OvertimeRequestItem;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class AttendanceOvertime extends Page implements HasForms
{
    use InteractsWithForms;
    protected string $view = 'filament.attendance.pages.attendance-overtime';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::ArrowsRightLeft;
    public static function getNavigationGroup(): ?string
    {
        return 'Attendance';
    }
    public static function getNavigationLabel(): string
    {
        return 'Comparison Overtime';
    }

    public ?string $dateFrom = null;
    public ?string $dateTo = null;
    public ?int $employeeId = null;

    public function mount(): void
    {
        $this->dateFrom = now()
            ->startOfMonth()
            ->toDateString();

        $this->dateTo = now()
            ->endOfMonth()
            ->toDateString();
    }


    public function schema(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('dateFrom')
                    ->label('Date From')
                    ->native(false)
                ->live(),
            DatePicker::make('dateTo')
                ->label('Date To')
                ->native(false)
                ->live(),
            Select::make('employeeId')
                ->label('Employee')
                ->searchable()
                ->preload()
                ->options(
                    Employee::query()
                        ->pluck('name', 'id')
                )
                ->live(),
        ])
        ->columns(3)
        ->statePath('');
    }

    /*
    |--------------------------------------------------------------------------
    | Attendance Query
    |--------------------------------------------------------------------------
    */

    public function getAttendanceDataProperty()
    {
        return AttendanceData::query()

            ->with([
                'attendance_user.employee',
                'attendance_shift',
            ])

            ->whereHas(
                'attendance_user',
                fn ($q) =>
                    $q->where(
                        'employee_id',
                        $this->employeeId
                    )
            )

            ->whereBetween('date', [
                $this->dateFrom,
                $this->dateTo,
            ])

            ->latest()

            ->paginate(31);
    }

    /*
    |--------------------------------------------------------------------------
    | Overtime Query
    |--------------------------------------------------------------------------
    */

    public function getOvertimeDataProperty()
    {
        return OvertimeRequestItem::query()

            ->with([
                'employee',
                'overtimeRequest'
            ])

            ->when(
                $this->employeeId,

                fn ($q) =>
                    $q->where(
                        'employee_id',
                        $this->employeeId
                    )
            )

            ->whereHas(
                'overtimeRequest',

                fn ($q) => $q->whereBetween(
                    'overtime_date',
                    [
                        $this->dateFrom,
                        $this->dateTo,
                    ]
                )
            )
            ->orderBy('created_at', 'asc')

            ->paginate(31);
    }
}
