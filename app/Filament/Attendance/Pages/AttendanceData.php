<?php

namespace App\Filament\Attendance\Pages;

use App\Enums\AttendanceStatus;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AttendanceData extends Page implements HasTable
{
    use InteractsWithTable;
    protected string $view = 'filament.attendance.pages.attendance-data';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;
    public static function canAccess(): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->isHrAll() || auth()->user()->isHr();
    }

    public static function getNavigationGroup(): ?string
    {
        return __('attendances.navigation_group');
    }

    public static function getNavigationLabel(): string
    {
        return __('attendances.attendance_data');
    }

    protected function getHeaderActions(): array
    {
        return [
            \App\Filament\Actions\Attendances\AttendanceOverview::make(),
            \Filament\Actions\ExportAction::make('download')
                ->columnMapping(false)
                ->exporter(\App\Filament\Exports\AttendanceDataExporter::class)
                ->modalHeading('Download Attendance Data to Excel')
                ->label('Download Excel')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->modalWidth('2xl')
                ->modifyQueryUsing(function (Builder $query, array $data) {
                    if(!empty($data['date_from']) && !empty($data['date_to'])) {
                        $query->whereBetween('date', [$data['date_from'], $data['date_to']]);
                    }
                    if(!empty($data['department_id'])) {
                        $query->whereHas('attendance_user.employee', function ($q) use ($data) {
                            $q->where('department_id', $data['department_id']);
                        });
                    }
                    if(!empty($data['user_id'])) {
                        $query->where('user_id', $data['user_id']);
                    }
                    return $query;
                }),
                // ->extraAttributes(['class' => '!text-white']),
        ];
    }

    public function table(Table $table): Table
    {
        return $table->query(\App\Models\AttendanceData::query()->where('date', '>=', \Carbon\Carbon::now()->subMonths(2)))
            ->columns([
                TextColumn::make('date')->label('Tanggal')
                ->label(__('attendances.fields.date'))
                ->date('d M Y'),
                TextColumn::make('attendance_user.display_name')
                    ->searchable()
                    ->label(__('attendances.fields.employee')),
                TextColumn::make('attendance_user.employee.department.name')
                    ->toggleable()
                    ->label(__('attendances.fields.department')),
                TextColumn::make('attendance_shift.name')
                    ->badge()
                    ->label(__('attendances.fields.shift'))
                    ->color(function ($record) {
                        if($record->attendance_shift->name === 'Day') {
                            return 'warning';
                        } elseif($record->attendance_shift->name === 'Night') {
                            return 'primary';
                        } else {
                            return 'info';
                        }
                    })
                    ->icon(function ($record) {
                        if($record->attendance_shift->name === 'Day') {
                            return 'heroicon-o-sun';
                        } elseif($record->attendance_shift->name === 'Night') {
                            return 'heroicon-o-moon';
                        } else {
                            return 'heroicon-o-sun';
                        }
                    }),
                TextColumn::make('clock_in')
                    ->label(__('attendances.fields.checkin'))
                    ->formatStateUsing(fn($state) => date('H:i', strtotime($state)))
                    ->color(fn($record) => $record->coming_late > 0 ? 'danger' : ''),
                TextColumn::make('clock_out')
                    ->label(__('attendances.fields.checkout'))
                    ->formatStateUsing(fn($state) => date('H:i', strtotime($state)))
                    ->color(fn($record) => $record->early_leave > 0 ? 'danger' : ''),
                TextInputColumn::make('coming_late')
                    // ->color(fn($record) => $record->coming_late > 0 ? 'danger' : '')
                    ->width('5%')
                    ->label(__('attendances.fields.late')),
                TextInputColumn::make('early_leave')
                    // ->color(fn($record) => $record->early_leave > 0 ? 'danger' : '')
                    ->width('5%')
                    ->label(__('attendances.fields.early_leave')),
                TextInputColumn::make('overtime_hours')
                    ->width('5%')
                    ->label(__('attendances.fields.overtime')),
                TextColumn::make('overtime_fix_hours')
                    ->label(__('attendances.fields.overtime_fix_hours')),
                TextColumn::make('working_hours')
                    ->toggleable(true, isToggledHiddenByDefault: false)
                    ->label(__('attendances.fields.working_hours')),
                TextColumn::make('status')
                    ->badge()
                    ->label(__('attendances.fields.status')),
            ])
            ->filters([
                SelectFilter::make('department')
                    ->relationship('attendance_user.employee.department', 'name')
                    ->label(__('attendances.fields.department')),
                SelectFilter::make('attendance_shift_id')
                    ->label(__('attendances.fields.shift'))
                    ->relationship('attendance_shift', 'name'),
                SelectFilter::make('status')
                    ->label('Status')
                    ->multiple()
                    ->options([
                        'Hadir' => 'Hadir',
                        'Sakit' => 'Sakit',
                        'Izin' => 'Izin',
                        'Cuti' => 'Cuti',
                        'Lembur' => 'Lembur',
                        'Alpha' => 'Alpha',
                        'Libur' => 'Libur',
                    ]),
            ])
            ->actions([
                \Filament\Actions\Action::make('koreksi')
                    ->label('Koreksi')
                    ->iconButton()
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning')
                    // ->visible(fn($record) => $record->status === AttendanceStatus::Hadir || $record->status === AttendanceStatus::Lembur)
                    ->form([
                        \Filament\Forms\Components\Select::make('shift_id')
                            ->label('Pilih Shift yang Benar')
                            ->options(\App\Models\AttendanceShift::pluck('name', 'id'))
                            ->required()
                            ->default(fn ($record) => $record->shift_id),
                    ])
                    ->action(function ($record, array $data, \App\Services\AttendanceProcessor $processor) {
                        $processor->reprocessManual($record->id, $data['shift_id']);

                        \Filament\Notifications\Notification::make()
                            ->title('Data Berhasil Dikoreksi')
                            ->success()
                            ->send();
                    })
            ])
            ->defaultSort('created_at', 'desc');
    }
}
