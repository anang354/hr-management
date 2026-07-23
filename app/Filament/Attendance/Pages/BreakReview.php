<?php

namespace App\Filament\Attendance\Pages;

use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\ExportAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BreakReview extends Page implements HasTable
{
    use InteractsWithTable;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;
    public static function getNavigationGroup(): ?string
    {
        return 'Breaks';
    }

    public static function canAccess(): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->isHr();
    }
    protected string $view = 'filament.attendance.pages.break-review';

    protected function getHeaderActions(): array
    {
        return [
            ExportAction::make('download')
                ->columnMapping(false)
                ->exporter(\App\Filament\Exports\BreakViewExporter::class)
                ->label('Download Data')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->modalHeading('Download Breaks Data')
                ->modalWidth('sm')
                ->modifyQueryUsing(function (Builder $query, array $data) {
                    // $options = $data['options'] ?? [];
                    if (!empty($data['date_from']) && !empty($data['date_to'])) {
                        $query->whereBetween('tanggal', [$data['date_from'], $data['date_to']]);
                    }
                    if (!empty($data['department_id'])) {
                        $query->whereHas('attendanceUser.employee', function ($q) use ($data) {
                            $q->where('department_id', $data['department_id']);
                        });
                    }
                    return $query;
                }),
        ];
    }

    public function table(Table $table): Table
    {
        // 1. Buat fungsi helper untuk menghitung durasi dan menentukan warna
        $getBreakColor = function ($record, $outColumn, $inColumn): string {
            // Jika datanya belum lengkap (karyawan belum tap masuk kembali), biarkan warna abu-abu/biru
            if (!$record->$outColumn || !$record->$inColumn) {
                return '';
            }

            $outTime = Carbon::parse($record->$outColumn);
            $inTime = Carbon::parse($record->$inColumn);

            // Logika Lintas Hari: Jika jam masuk lebih kecil dari jam keluar (misal keluar 23:50, masuk 00:25)
            // Maka anggap jam masuk tersebut adalah hari esoknya agar selisih menitnya tidak minus.
            if ($inTime->lt($outTime)) {
                $inTime->addDay();
            }

            $diffMinutes = $outTime->diffInMinutes($inTime);

            // Jika lebih dari 32 menit kembalikan warna 'danger' (merah), jika aman kembalikan 'success' (hijau)
            return $diffMinutes > 32 ? 'danger' : 'success';
        };
        return $table
        ->query(\App\Models\BreakView::query()->where('tanggal', '>=', Carbon::now()->subMonths(2))->orderByDesc('tanggal'))
        ->columns([
            TextColumn::make('tanggal')
                ->label('Date')
                ->date('Y-m-d'),
            TextColumn::make('attendanceUser.display_name')
                ->label('Employee')
                ->searchable(),
            TextColumn::make('attendanceUser.employee.department.name')
                ->label('Department'),
            // --- KELOMPOK BREAK 1 ---
            TextColumn::make('break_out_1')
                ->label('Break Out 1')
                ->time('H:i')
                ->color(fn ($record) => $getBreakColor($record, 'break_out_1', 'break_in_1')),

            TextColumn::make('break_in_1')
                ->label('Break In 1')
                ->time('H:i')
                ->color(fn ($record) => $getBreakColor($record, 'break_out_1', 'break_in_1')),

            // --- KELOMPOK BREAK 2 ---
            TextColumn::make('break_out_2')
                ->label('Break Out 2')
                ->time('H:i')
                ->color(fn ($record) => $getBreakColor($record, 'break_out_2', 'break_in_2')),

            TextColumn::make('break_in_2')
                ->label('Break In 2')
                ->time('H:i')
                ->color(fn ($record) => $getBreakColor($record, 'break_out_2', 'break_in_2')),
        ])
        ->filters([
            SelectFilter::make('department')
                ->relationship('attendanceUser.employee.department', 'name'),
        ]);
    }
}
