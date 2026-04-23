<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Console\Command;

class GenerateAbsentRecords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-absent-records';

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
        $targetDate = now()->subDay()->toDateString(); // cek data kemarin

        // 1. Ambil semua karyawan yang seharusnya masuk (Aktif)
        $employees = Employee::where('is_active', true)->get();

        foreach ($employees as $employee) {
            // 2. Cek apakah sudah ada record attendance hari ini
            $exists = Attendance::where('employee_id', $employee->id)
                ->where('date', $targetDate)
                ->exists();

            // 4. Jika tidak ada record DAN tidak sedang cuti, buat record "ABSENT"
            if (!$exists) {
                Attendance::create([
                    'employee_id' => $employee->id,
                    'date' => $targetDate,
                    'shift' => null,
                    'status' => null,
                    'checkin' => null,
                    'breakout' => null,
                    'breakin' => null,
                    'checkout' => null,
                ]);
            }
        }

        $this->info('Penyisiran data absen selesai.');
    }
}
