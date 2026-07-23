<?php

namespace App\Filament\Exports;

use App\Models\AttendanceData;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Number;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Border;
use OpenSpout\Common\Entity\Style\BorderPart;
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Common\Entity\Style\CellVerticalAlignment;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;

class AttendanceDataExporter extends Exporter
{
    protected static ?string $model = AttendanceData::class;

    public function getXlsxHeaderCellStyle(): ?Style
    {
        return (new Style())
            ->setFontBold()
            ->setFontItalic()
            ->setFontSize(12)
            ->setFontColor(Color::rgb(0,0,0))
            ->setBackgroundColor(Color::rgb(138, 189, 255))
            ->setCellAlignment(CellAlignment::CENTER)
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER);
    }

    public function makeXlsxRow(array $values, ?Style $style = null): Row
    {
        $border = new Border(
            new BorderPart(Border::BOTTOM, Color::rgb(0, 0, 0), Border::WIDTH_THIN),
            new BorderPart(Border::TOP, Color::rgb(0, 0, 0), Border::WIDTH_THIN),
            new BorderPart(Border::LEFT, Color::rgb(0, 0, 0), Border::WIDTH_THIN),
            new BorderPart(Border::RIGHT, Color::rgb(0, 0, 0), Border::WIDTH_THIN)
        );
        $cells = [];
        foreach (array_keys($this->columnMap) as $columnIndex => $column) {
            $cells[] = match ($column) {
            default => Cell::fromValue(
                    $values[$columnIndex],
                    (new Style())
                        ->setFontSize(12)
                        ->setBorder($border)
                    ),
            };
        };

        return new Row($cells, $style);
    }

    public static function getOptionsFormComponents(): array
    {
        return [
            Grid::make()
            ->columns(2)
            ->schema([
                DatePicker::make('date_from')
                ->required()
                ->native(false)
                ->label('From Date'),

            DatePicker::make('date_to')
                ->required()
                ->native(false)
                ->label('To Date'),

            Select::make('department_id')
                ->helperText('Optional : leave blank if you want to select all departments')
                ->label('Department')
                ->disabled(fn (Get $get) => !empty($get('user_id')))
                ->options(\App\Models\Department::pluck('name', 'id')->toArray()),

            Select::make('user_id')
                ->helperText('Optional : leave blank if you want to select all employees')
                ->label('Employee')
                ->searchable()
                ->preload()
                ->live()
                ->afterStateUpdated(function(Set $set) {
                    $set('department_id', null);
                })
                ->options(\App\Models\AttendanceUser::pluck('display_name', 'id')->toArray()),
            ])
        ];
    }

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('date')
                ->label('Date 日期'),
            ExportColumn::make('attendance_shift.name')
                ->label('Shift 班次'),
            ExportColumn::make('attendance_user.display_name')
                ->label('Employee 员工'),
            ExportColumn::make('attendance_user.employee.employee_number')
                ->label('ID Card 员工编号'),
            ExportColumn::make('attendance_user.employee.department.name')
                ->label('Department 部门'),
            ExportColumn::make('clock_in')
                ->label('Clock-In 上班打卡')
                ->formatStateUsing(fn($state) => date('H:i', strtotime($state))),
            ExportColumn::make('clock_out')
                ->label('Clock-Out 下班打卡')
                ->formatStateUsing(fn($state) => date('H:i', strtotime($state))),
            ExportColumn::make('coming_late')
                ->label('Coming Late 迟到'),
            ExportColumn::make('early_leave')
                ->label('Early Leave 早退'),
            ExportColumn::make('overtime_hours')
                ->label('Overtime Hours 加班时数'),
            ExportColumn::make('overtime_fix_hours')
                ->label('Overtime Fix Hours 加班时数(修正)'),
            ExportColumn::make('working_hours')
                ->label('Working Hours 工作时数'),
            ExportColumn::make('status')
                ->label('Status 状态'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your attendance data export has completed and ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
