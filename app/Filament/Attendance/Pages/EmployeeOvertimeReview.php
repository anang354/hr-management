<?php
namespace App\Filament\Attendance\Pages;

use App\Enums\OvertimeItemStatus;
use App\Models\Employee;
use App\Models\OvertimeRequestItem;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;


class EmployeeOvertimeReview extends Page implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    protected string $view = 'filament.attendance.pages.employee-overtime-review';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::ChartPie;
    public static function getNavigationGroup(): ?string
    {
        return __('overtime_request.navigation_group') ?? null;
    }


    public function getTitle(): string|Htmlable
    {
        return __('employee_overtime_review.title');
    }
    public static function getNavigationLabel(): string
    {
        return __('employee_overtime_review.navigation_label');
    }

    // Property untuk menampung ID karyawan yang dipilih
    public ?int $employee_id = null;

    /**
     * Bagian Kiri: Form Select Karyawan
     */
    public function schema(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('employee_id')
                    ->label(__('employee_overtime_review.content.select_employee'))
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
                    ->live() // Memicu update Livewire seketika saat dipilih
                    ->afterStateUpdated(fn() => $this->resetTable()), // Reset pagination tabel
            ]);
    }

    /**
     * Bagian Kanan: Tabel Overtime Items
     */
    public function table(Table $table): Table
    {
        return $table
            // Query otomatis reload saat $this->employee_id berubah
            ->query(
                OvertimeRequestItem::query()
                    ->where('employee_id', $this->employee_id)
                    ->orderByDesc('created_at')
            )
            ->columns([
                TextColumn::make('overtimeRequest.overtime_date')
                    ->date('d F Y')
                    ->label(__('overtime_request.fields.overtime_date')),
                TextColumn::make('start_time')
                    ->time('H:i')
                    ->label(__('overtime_items.fields.start_time')),
                TextColumn::make('end_time')
                    ->time('H:i')
                    ->label(__('overtime_items.fields.end_time')),
                TextColumn::make('overtime_hours')
                    ->label(__('overtime_items.fields.overtime_hours'))
                    ->badge()
                    ->color('info'),
                TextColumn::make('status')
                    ->label(__('overtime_items.fields.status'))
                    ->badge(),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Approve')
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
                    ->label('Reject')
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
            ])
            ->emptyStateHeading(__('employee_overtime_review.content.no_data'))
            ->emptyStateDescription(__('employee_overtime_review.content.no_data_description'));
    }

    /**
     * Helper untuk mengambil data detail karyawan terpilih
     */
    public function getSelectedEmployeeProperty()
    {
        return Employee::find($this->employee_id);
    }
}
