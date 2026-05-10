<?php

namespace App\Filament\Pages;

use App\Models\Attendance as AttendanceModel;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Contracts\View\View;

class Attendance extends Page implements HasTable
{
    use InteractsWithTable;
    protected string $view = 'filament.pages.attendance';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDateRange;

    public static function canAccess(): bool
    {
        return false; // Fitur dinonaktifkan total untuk sementara
    }

    public static function getNavigationGroup(): ?string
    {
        return __('attendances.navigation_group') ?? null;
    }

    public function getTitle(): string
    {
        return __('attendances.title');
    }
    public static function getNavigationLabel(): string
    {
        return __('attendances.navigation_label');
    }



    public function table(Table $table): Table
    {
        return $table
            ->query(
                AttendanceModel::query()
                    ->with('employee.department')
                    ->latest('created_at')
            )
            ->deferLoading()
            ->columns([
                TextColumn::make('date')
                    ->label(__('attendances.fields.date')),
                TextColumn::make('employee.name')
                    ->label(__('attendances.fields.employee'))
                    ->searchable(),
                TextColumn::make('employee.department.name')
                    ->label(__('attendances.fields.department')),
                TextColumn::make('shift')
                    ->label(__('attendances.fields.shift'))
                    ->badge(),
                TextColumn::make('checkin')
                    ->label(__('attendances.fields.checkin'))
                    ->time('H:i')
                    ->color(function ($record) {
                        if ($record->shift === 'Day') {
                            return $record->checkin > '08:00' ? 'danger' : 'black';
                        } else {
                            return $record->checkin > '20:00' ? 'danger' : 'black';
                        }
                    }),
                TextColumn::make('checkout')
                    ->label(__('attendances.fields.checkout'))
                    ->time('H:i'),
                TextColumn::make('breakout')
                    ->label(__('attendances.fields.breakout'))
                    ->time('H:i'),
                TextColumn::make('breakin')
                    ->label(__('attendances.fields.breakin'))
                    ->time('H:i'),
            ])
            ->filters([
                Filter::make('date')
                    ->label(__('attendances.fields.date'))
                    ->form([
                        DatePicker::make('from')
                            ->label(__('attendances.filters.from')),
                        DatePicker::make('until')
                            ->label(__('attendances.filters.until')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn($q) => $q->whereDate('date', '>=', $data['from']))
                            ->when($data['until'], fn($q) => $q->whereDate('date', '<=', $data['until']));
                    }),
                SelectFilter::make('department')
                    ->label(__('attendances.fields.department'))
                    ->relationship('employee.department', 'name')
                    ->multiple(),
                SelectFilter::make('shift')
                    ->label(__('attendances.fields.shift'))
                    ->options([
                        'Day' => 'Day',
                        'Night' => 'Night',
                    ]),
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
                    ->modalContent(fn(AttendanceModel $record): View => view(
                        'filament.pages.view-photo',
                        [
                            'data' => $record,
                        ],
                    )),
            ]);
    }
}
