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
        $employees = \App\Models\AttendanceUser::with('employee')->get();
        $today = now()->format('Y-m-d');
        // Cek apakah hari ini hari libur atau weekend
        $isOffDay = $attendanceService->isHolidayOrWeekend($today);

        foreach ($employees as $emp) {
            //cek apakah ada leave_request
            if($emp->employee) {
                $leaveRequest = \App\Models\LeaveRequest::where('employee_id', $emp->employee->id)->where('status', 'approved')->where('start_date', '<=', $today)->where('end_date', '>=', $today)->first();
                if ($leaveRequest) {
                    \App\Models\AttendanceData::firstOrCreate(
                        ['user_id' => $emp->id, 'date' => $today],
                        ['status' => \App\Enums\AttendanceStatus::Cuti,]
                    );
                    continue;
                }
            }
            \App\Models\AttendanceData::firstOrCreate(
                ['user_id' => $emp->id, 'date' => $today],
                ['status' => $isOffDay ? 'Libur' : 'Alpha',]
            );
        }
    }
}
