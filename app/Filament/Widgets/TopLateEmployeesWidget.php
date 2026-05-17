<?php

namespace App\Filament\Widgets;

use App\Models\AttendanceUser;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TopLateEmployeesWidget extends TableWidget
{

    protected bool $isCollapsible = true;
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = [
        'md' => 3,
        'xl' => 4,
    ];
    public function table(Table $table): Table
    {
        $startOfMonth = now()->startOfMonth()->format('Y-m-d');
        $today = now()->format('Y-m-d');
        return $table
            ->query(
                // 1. Query utama dari tabel attendance_users
                AttendanceUser::query()
                    ->where('is_active', 1)

                    // 2. Hitung TOTAL JAM terlambat dari kolom coming_late
                    ->withSum(['attendanceData as total_late_hours' => function (Builder $query) use ($startOfMonth, $today) {
                        $query->whereBetween('date', [$startOfMonth, $today])
                              ->where('coming_late', '>', 0);
                    }], 'coming_late')

                    // 3. Hitung TOTAL HARI terlambat (berapa kali barisnya memiliki coming_late > 0)
                    ->withCount(['attendanceData as total_late_days' => function (Builder $query) use ($startOfMonth, $today) {
                        $query->whereBetween('date', [$startOfMonth, $today])
                              ->where('coming_late', '>', 0);
                    }])
                    // Filter hanya menampilkan yang pernah terlambat
                    ->having('total_late_days', '>', 0)
                    // Urutkan berdasarkan akumulasi hari terlambat terbanyak
                    ->orderBy('total_late_days', 'desc')
                    // Batasi hanya 5 orang teratas (Top 5)
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('display_name')
                    ->label(__('attendances.fields.employee'))
                    ->weight('bold'),
                // Kolom Departemen (Mengambil via relasi ke model Employee Anda)
                TextColumn::make('employee.department') // Sesuaikan nama relasi & kolom departemen Anda
                    ->label(__('attendances.fields.department'))
                    ->color('gray'),
                // Kolom Total Hari Terlambat
                TextColumn::make('total_late_days')
                    ->label(__('attendances.fields.total_hari'))
                    ->badge()
                    ->color('danger')
                    ->suffix(' Hari')
                    ->alignCenter(),
                // Kolom Total Jam Terlambat
                TextColumn::make('total_late_hours')
                    ->label(__('attendances.fields.total_jam'))
                    ->suffix(' Jam')
                    ->alignCenter(),
            ])->paginated(false);
    }
}
