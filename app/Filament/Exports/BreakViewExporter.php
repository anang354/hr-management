<?php

namespace App\Filament\Exports;

use App\Models\BreakView;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Illuminate\Support\Number;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Border;
use OpenSpout\Common\Entity\Style\BorderPart;
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Common\Entity\Style\CellVerticalAlignment;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Options;

class BreakViewExporter extends Exporter
{
    protected static ?string $model = BreakView::class;

    public static function getOptionsFormComponents(): array
    {
        return [
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
                ->options(\App\Models\Department::pluck('name', 'id')->toArray()),
        ];
    }

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
    public function getXlsxWriterOptions(): ?Options
    {
        $options = new Options();
        $options->setColumnWidth(20, 15);
        $options->setColumnWidthForRange(12, 2, 3);

        return $options;
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

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('attendanceUser.display_name')
                ->label('Employee 员工'),
            ExportColumn::make('attendanceUser.employee.employee_number')
                ->label('ID Card 员工编号'),
            ExportColumn::make('attendanceUser.employee.department.name')
                ->label('Department 部门'),
            ExportColumn::make('tanggal')
                ->label('Date 日期'),
            ExportColumn::make('break_out_1')
                ->formatStateUsing(fn ($state) => $state ? date('H:i', strtotime($state)) : null)
                ->label('Break-Out 爆发 1'),
            ExportColumn::make('break_in_1')
                ->formatStateUsing(fn ($state) => $state ? date('H:i', strtotime($state)) : null)
                ->label('Break-In 打破 1'),
            ExportColumn::make('break_out_2')
                ->formatStateUsing(fn ($state) => $state ? date('H:i', strtotime($state)) : null)
                ->label('Break-Out 爆发 2'),
            ExportColumn::make('break_in_2')
                ->formatStateUsing(fn ($state) => $state ? date('H:i', strtotime($state)) : null)
                ->label('Break-In 爆发 2')
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your break view export has completed and ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
