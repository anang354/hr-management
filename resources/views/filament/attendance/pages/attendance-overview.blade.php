@extends('layouts.admin')
@section('content')
    <div class="p-6 bg-gray-50 min-h-screen">
        <div class="mb-2 w-full flex gap-4">
            <p class="text-sm font-medium text-slate-600">Departmen : {{ $deptName }}</p>
            <p class="text-sm font-medium text-slate-600">From : {{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }}</p>
            <p class="text-sm font-medium text-slate-600">To : {{ \Carbon\Carbon::parse($toDate)->format('d M Y') }}</p>
        </div>
        <div class="max-w-full mx-auto bg-white shadow-sm border border-gray-200 rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th
                                class="sticky left-0 z-20 bg-gray-50 px-4 py-3 font-semibold text-gray-600 border-r border-gray-200 min-w-[200px]">
                                Employee
                            </th>
                            @foreach ($dates as $date)
                                <th
                                    class="px-2 py-2 text-center border-r border-gray-100 min-w-[45px] {{ $date['is_holiday'] || $date['is_weekend'] ? 'bg-orange-100/40' : '' }}">
                                    <div class="text-gray-400 font-medium">{{ $date['date'] }}</div>
                                    <div class="text-gray-600 font-bold uppercase">{{ $date['day'] }}</div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($employeesData as $emp)
                            <tr class="hover:bg-blue-50/30">
                                <td class="sticky left-0 z-10 bg-white px-4 py-3 border-r border-gray-200">
                                    <span class="text-gray-400 font-mono mr-2">{{ $emp['id'] }}</span>
                                    <span class="text-gray-700 font-medium">{{ $emp['name'] }}</span>
                                </td>

                                {{-- Looping berdasarkan tanggal yang sudah kita siapkan di Controller --}}
                                @foreach ($dates as $dateKey => $dateInfo)
                                    @php
                                        $statusEnum = $emp['attendance'][$dateKey] ?? null;
                                        // Logika warna background sel
                                        $bgColor = '';
                                        if ($dateInfo['is_holiday'] || $dateInfo['is_weekend']) {
                                            $bgColor = 'bg-orange-100/40'; // Oranye muda untuk weekend
                                        }

                                        // Tentukan warna garis bawah
                                        // Jika libur nasional tapi karyawan tetap hadir (Lembur), beri warna hijau
                                        $underlineColor =
                                            $statusEnum instanceof \App\Enums\AttendanceStatus
                                                ? $statusEnum->getColorClass()
                                                : 'border-transparent';

                                        // Jika hari libur dan tidak ada status, otomatis beri warna kuning (Libur)
                                        if ($dateInfo['is_holiday'] && !$statusEnum) {
                                            $underlineColor = 'border-yellow-400';
                                        }
                                    @endphp

                                    <td title="{{ $dateInfo['holiday_name'] ?? '' }}"
                                        class="px-1 py-3 text-center border-r border-gray-50 {{ $bgColor }}">

                                        <div class="flex flex-col items-center justify-center h-full">
                                            <span class="text-gray-600 mb-1 font-bold text-[10px]">
                                                {{ $statusEnum instanceof \App\Enums\AttendanceStatus ? $statusEnum->shortLabel() : '-' }}
                                            </span>
                                            <div class="w-4 border-b-2 {{ $underlineColor }}"></div>
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div
                class="p-4 bg-white border-t border-gray-200 flex flex-wrap gap-4 text-[10px] text-gray-500 uppercase tracking-wider">
                <div class="flex items-center gap-1.5">
                    <span class="w-3 h-0.5 bg-green-500"></span> H - Hadir
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-3 h-0.5 bg-blue-400"></span> OT - Overtime
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-3 h-0.5 bg-gray-400"></span> Off - Libur
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-3 h-0.5 bg-red-500"></span> A - Alpha
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-3 h-0.5 bg-orange-400"></span> C - Cuti
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-3 h-0.5 bg-orange-400"></span> S - Sakit
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-3 h-0.5 bg-orange-400"></span> I -Izin
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Mengatur scrollbar agar lebih tipis dan modern */
        .overflow-x-auto::-webkit-scrollbar {
            height: 8px;
        }

        .overflow-x-auto::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
@endsection
