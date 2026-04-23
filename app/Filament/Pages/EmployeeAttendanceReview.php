<?php

namespace App\Filament\Pages;

use App\Models\Attendance;
use App\Models\Employee;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;

class EmployeeAttendanceReview extends Page implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;
    protected string $view = 'filament.pages.employee-attendance-review';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::AtSymbol;
    public static function getNavigationGroup(): ?string
    {
        return __('attendances.navigation_group') ?? null;
    }
    public function getTitle(): string
    {
        return __('attendances.employee_attendance_review');
    }
    public static function getNavigationLabel(): string
    {
        return __('attendances.employee_attendance_review');
    }

    public ?int $employee_id = null;
    public ?string $date_from = null;
    public ?string $date_to = null;

    public function schema(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('date_from')
                    ->label(__('attendances.filters.from'))
                    ->default(now()->startOfMonth())
                    ->live()
                    ->afterStateUpdated(fn() => $this->resetTable()),
                DatePicker::make('date_to')
                    ->label(__('attendances.filters.until'))
                    ->default(now()->endOfMonth())
                    ->live()
                    ->afterStateUpdated(fn() => $this->resetTable()),
                Select::make('employee_id')
                    ->label(__('attendances.fields.employee'))
                    ->options(function () {
                        $user = auth()->user();
                        if (in_array($user->role, ['hr', 'admin'])) {
                            return Employee::query()
                                ->where('is_active', true)
                                ->pluck('name', 'id');
                        }
                        return Employee::query()
                            ->where('department_id', $user->department_id)
                            ->where('is_active', true)
                            ->pluck('name', 'id');
                    })
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(fn() => $this->resetTable()),

            ]);
    }
    public function table(Table $table): Table
    {
        return $table->query(
            Attendance::query()
                ->where('employee_id', $this->employee_id)
                ->whereBetween('date', [$this->date_from, $this->date_to])
        )
            ->columns([
                TextColumn::make('date')
                    ->date('d F Y')
                    ->label(__('attendances.fields.date')),
                TextColumn::make('shift')
                    ->badge()
                    ->label(__('attendances.fields.shift')),
                TextColumn::make('checkin')
                    ->time('H:i')
                    ->label(__('attendances.fields.checkin')),
                TextColumn::make('checkout')
                    ->time('H:i')
                    ->label(__('attendances.fields.checkout')),
                TextColumn::make('breakout')
                    ->time('H:i')
                    ->label(__('attendances.fields.breakout')),
                TextColumn::make('breakin')
                    ->time('H:i')
                    ->label(__('attendances.fields.breakin')),
            ])
            ->actions([
                Action::make('viewPhoto')
                    ->label(__('attendances.actions.viewPhoto'))
                    ->icon('heroicon-o-camera')
                    ->color('info')
                    ->modalHeading(__('attendances.contents.modal_heading'))
                    ->slideOver()
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel(__('attendances.actions.close'))
                    ->modalContent(fn(Attendance $record): View => view(
                        'filament.pages.view-photo',
                        [
                            'data' => $record,
                        ],
                    )),
            ]);
    }
    public function getSelectedEmployeeProperty()
    {
        return Employee::find($this->employee_id);
    }
}
