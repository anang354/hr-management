<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ProcessAttendanceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-attendance-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(\App\Services\AttendanceProcessor $processor)
    {
        // 1. Ambil log mentah yang belum diproses
        $unprocessedLogs = \App\Models\AttendanceLog::whereNull('processed_at')->limit(200)->get();

        foreach ($unprocessedLogs as $log) {
            // Kirim biometric_id, waktu log, dan tipe log (0 atau 1)
            $processor->process($log->biometric_id, $log->attendance_time, $log->type);

            $log->update(['processed_at' => now()]);
        }
        $this->info('Berhasil memproses ' . $unprocessedLogs->count() . ' data log.');
    }
}
