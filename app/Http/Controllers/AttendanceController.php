<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)  {
        $type = $request->type;
        $deptId = $request->query('department_id');
        $deptName = \App\Models\Department::where('id', $deptId)->first()->name ?? '';
        $fromDate = $request->query('from', now()->startOfMonth()->toDateString());
        $toDate = $request->query('to', now()->endOfMonth()->toDateString());

        $start = \Carbon\Carbon::parse($fromDate);
        $end = \Carbon\Carbon::parse($toDate);

        $holidays = \App\Models\Holiday::whereBetween('holiday_date', [$fromDate, $toDate])
        ->get()
        ->mapWithKeys(function ($item) {
            // Memastikan key hanya YYYY-MM-DD
            $key = \Carbon\Carbon::parse($item->holiday_date)->format('Y-m-d');
            return [$key => $item->description];
        })
        ->toArray();
        $dates = [];
        $tempStart = $start->copy(); // Gunakan copy agar tidak merubah variabel start asli
        while ($tempStart <= $end) {
            $dateKey = $tempStart->toDateString();
            $holidayName = $holidays[$dateKey] ?? null;
            $dates[$dateKey] = [
                'day' => $tempStart->format('D'),
                'date' => $tempStart->format('M d'),
                'is_weekend' => $tempStart->isWeekend(),
                'is_holiday' => !empty($holidayName),
                'holiday_name' => $holidayName,
            ];
            $tempStart->addDay();
        }

        if($type === 'kehadiran') {
            // QUERY PERBAIKAN: Menggunakan whereHas untuk filter department melalui employee
            $employeesQuery = \App\Models\AttendanceUser::query()
                ->with(['employee.department']); // Eager load agar tidak N+1 query

            if ($deptId) {
                $employeesQuery->whereHas('employee', function ($query) use ($deptId) {
                    $query->where('department_id', $deptId);
                });
            }

            $employeesData = $employeesQuery->get()->map(function ($attendanceUser) use ($fromDate, $toDate) {
                return [
                    'id' => $attendanceUser->employee?->employee_code ?? '-',
                    'name' => $attendanceUser->display_name,
                    // Pastikan fungsi ini mengembalikan array dengan format: ['2026-05-01' => 'P', '2026-05-02' => 'W/P', ...]
                    'attendance' => $this->getAttendanceStatus($attendanceUser->id, $fromDate, $toDate),
                ];
            });

            return view('filament.attendance.pages.attendance-overview', [
                'fromDate' => $fromDate,
                'toDate' => $toDate,
                'dates' => $dates,
                'deptName' => $deptName,
                'employeesData' => $employeesData,
            ]);
        } else {
            // 2. Ambil Data Karyawan & AttendanceData
            $employees = \App\Models\AttendanceUser::query()
                ->with(['employee.department', 'attendanceData' => function($query) use ($fromDate, $toDate) {
                    $query->whereBetween('date', [$fromDate, $toDate]);
                }])
                ->when($deptId, function ($query) use ($deptId) {
                    $query->whereHas('employee', fn($q) => $q->where('department_id', $deptId));
                })
                ->get()
                ->map(function ($user) {
                    // Kelompokkan data attendance berdasarkan tanggal agar aksesnya cepat
                    $attendanceByDate = $user->attendanceData->keyBy('date');

                    return [
                        'name' => $user->display_name,
                        'emp_id' => $user->employee?->employee_number ?? '-',
                        'data' => $attendanceByDate
                    ];
                });

            return view('filament.attendance.pages.overtime-overview', [
                'fromDate' => $fromDate,
                'toDate' => $toDate,
                'dates' => $dates,
                'deptName' => $deptName,
                'employees' => $employees,
            ]);
        }
    }

    protected function getAttendanceStatus($userId, $fromDate, $toDate)
{
    // Sesuaikan query ini dengan struktur tabel absensi Anda
    $statuses = \App\Models\AttendanceData::where('user_id', $userId)
        ->whereBetween('date', [$fromDate, $toDate])
        ->orderBy('date')
        ->pluck('status', 'date') // Ambil status berdasarkan tanggal
        ->toArray();

    return $statuses; // Array ['2024-09-01' => 'P', '2024-09-02' => 'SL']
}
}
