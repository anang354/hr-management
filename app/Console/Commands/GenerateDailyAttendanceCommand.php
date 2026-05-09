<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

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
    public function handle()
    {
        $employees = \App\Models\AttendanceUser::all();
        $today = now()->format('Y-m-d');

        foreach ($employees as $employee) {
            \App\Models\AttendanceData::firstOrCreate(
                ['user_id' => $employee->id, 'date' => $today],
                ['status' => 'not_attended']
            );
        }
    }
}
