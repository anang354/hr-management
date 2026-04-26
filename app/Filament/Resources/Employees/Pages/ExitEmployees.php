<?php

namespace App\Filament\Resources\Employees\Pages;

use App\Filament\Resources\Employees\EmployeeResource;
use App\Models\Employee;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ExitEmployees extends Page implements HasForms
{
    use InteractsWithForms;
    protected static string $resource = EmployeeResource::class;

    protected string $view = 'filament.resources.employees.pages.exit-employees';
    // public static function canAccess(): bool
    // {
    //     return auth()->user()->role === 'admin' || auth()->user()->role === 'hr';
    // }

    public ?array $data = [];
    public function mount(): void
    {
        $this->form->fill();
    }
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Repeater::make('exit_list')
                    ->label('Daftar Karyawan Keluar')
                    ->schema([
                        Select::make('employee_id')
                            ->label(__('employee.fields.name'))
                            ->options(Employee::where('is_active', true)->pluck('name', 'id'))
                            ->searchable()
                            ->required()
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                        DatePicker::make('exit_date')
                            ->label(__('employee.fields.exit_date'))
                            ->required()
                            ->default(now()),

                        TextInput::make('exit_reason')
                            ->label(__('employee.fields.exit_reason'))
                            ->required(),
                    ])
                    ->columns(3)
                    ->addActionLabel('Tambah Karyawan')
                    ->reorderable(false)
            ])
            ->statePath('data')
            ->model(Employee::class);
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(__('employee.actions.process_now'))
                ->submit('save')
                ->color('danger'),
        ];
    }

    public function save(): void
    {
        $input = $this->form->getState()['exit_list'];
        $count = 0;

        foreach ($input as $item) {
            $count++;
            Employee::where('id', $item['employee_id'])->update([
                'exit_date' => $item['exit_date'],
                'exit_reason' => $item['exit_reason'],
                'is_active' => false, // Otomatis menonaktifkan karyawan
            ]);
        }

        Notification::make()
            ->title('Berhasil memproses ' . $count . ' karyawan keluar')
            ->success()
            ->send();

        $this->form->fill(); // Reset form
    }

}
