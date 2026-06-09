<?php

namespace App\Filament\Resources\Employees\Pages;

use App\Filament\Resources\Employees\EmployeeResource;
use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeePos;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ChangeCardNumber extends Page implements HasForms
{
    use InteractsWithRecord, InteractsWithForms;

    protected static string $resource = EmployeeResource::class;

    protected string $view = 'filament.resources.employees.pages.change-card-number';

    public ?array $data = [];

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
        $this->form->fill([
            'department_id' => $this->record->department_id,
            'employee_pos_id' => $this->record->employee_pos_id,
            'employee_number' => $this->record->employee_number,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->model(Employee::class)
            ->statePath('data')
            ->components([
            Section::make('')
                ->label('Data Karyawan')
                ->schema([
                    Placeholder::make('name')
                    ->content(fn () => $this->record->name)
                    ->label(__('employee.fields.name')),
                Placeholder::make('department')
                    ->content(fn () => $this->record->department->name)
                    ->label(__('employee.fields.department')),
                Placeholder::make('position')
                    ->content(function() {
                        return $this->record->employeePos->name . ' - ' . $this->record->employeePos->code;
                    })
                    ->label(__('employee.fields.position')),
                Placeholder::make('current_card_number')
                    ->content(fn () => $this->record->employee_number),
                ]),
            Section::make('')
                ->label('Ganti Nomor Kartu')
                ->schema([
                    Select::make('department_id')
                        ->label('Department')
                        ->relationship('department', 'name')
                        ->getOptionLabelFromRecordUsing(fn (Department $record) => "{$record->name}-{$record->code}")
                        ->default($this->record->department_id),
                    Select::make('employee_pos_id')
                        ->label('Position')
                        ->relationship('employeePos', 'name')
                        ->getOptionLabelFromRecordUsing(fn (EmployeePos $record) => "{$record->name}-{$record->code}")
                        ->default($this->record->employee_pos_id),
                    TextInput::make('employee_number')
                        ->label('Nomor Kartu baru')
                        ->default($this->record->employee_number)
                        ->required()
                        ->unique(Employee::class, 'employee_number', ignoreRecord: true)
                        ->maxLength(255),
                ]),
        ])->columns(2);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $this->record->update([
            'department_id' => $data['department_id'] ?? $this->record->department_id,
            'employee_pos_id' => $data['employee_pos_id'] ?? $this->record->employee_pos_id,
            'employee_number' => $data['employee_number'] ?? $this->record->employee_number,
        ]);

        Notification::make()
            ->title('Nomor kartu berhasil diperbarui')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(__('employee.actions.process_now'))
                ->icon('heroicon-o-arrow-path')
                ->submit('save')
                ->color('primary'),
        ];
    }
}
