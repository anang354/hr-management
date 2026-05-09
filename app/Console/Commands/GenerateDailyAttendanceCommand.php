<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AttendanceService;

class GenerateDailyAttendanceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-daily-attendance-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(AttendanceService $attendanceService)
    {
        $employees = \App\Models\AttendanceUser::all();
        $today = now()->format('Y-m-d');
        // Cek apakah hari ini hari libur atau weekend
        $isOffDay = $attendanceService->isHolidayOrWeekend($today);

        foreach ($employees as $employee) {
            \App\Models\AttendanceData::firstOrCreate(
                ['user_id' => $employee->id, 'date' => $today],
                ['status' => $isOffDay ? 'Libur' : 'Alpha',]
            );
        }
    }
}
