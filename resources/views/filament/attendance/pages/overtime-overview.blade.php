@extends('layouts.admin')
@section('content')
    <div class="my-2 w-full">
        <div class="mx-4 flex gap-4">
            <p class="text-sm font-medium text-slate-600">Departmen : {{ $deptName }}</p>
            <p class="text-sm font-medium text-slate-600">From : {{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }}</p>
            <p class="text-sm font-medium text-slate-600">To : {{ \Carbon\Carbon::parse($toDate)->format('d M Y') }}</p>
        </div>
    </div>
    <div class="bg-white shadow-sm border border-gray-200 rounded-xl overflow-hidden mx-4">
        <div class="overflow-x-auto">
            <table class="w-full text-[11px] border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th
                            class="sticky left-0 z-30 bg-gray-50 px-4 py-3 border-r border-gray-100 font-bold text-gray-700">
                            NO</th>
                        <th
                            class="sticky left-0 z-30 bg-gray-50 px-4 py-3 border-r border-gray-100 font-bold text-gray-700 min-w-[180px]">
                            EMPLOYEE 员工</th>
                        <th
                            class="sticky left-[180px] z-30 bg-gray-50 px-2 py-3 border-r border-gray-100 font-bold text-gray-700 w-[60px]">
                            TYPE</th>
                        @foreach ($dates as $date)
                            <th
                                class="px-2 py-2 text-center border-r border-gray-100 min-w-[65px] {{ $date['is_weekend'] || $date['is_holiday'] ? 'bg-orange-100/40' : '' }}">
                                <div class="text-gray-400 font-medium">{{ $date['date'] }}</div>
                                <div class="text-gray-700 font-bold uppercase">{{ $date['day'] }}</div>
                            </th>
                        @endforeach
                        <th
                            class="sticky left-[180px] z-30 bg-gray-50 px-2 py-3 border-r border-gray-100 font-bold text-gray-700 w-[60px]">
                            Total OT 总额</th>
                        <th
                            class="sticky left-[180px] z-30 bg-gray-50 px-2 py-3 border-r border-gray-100 font-bold text-gray-700 w-[60px]">
                            Total CL/EL</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($employees as $emp)
                        {{-- Row 1: IN --}}
                        <tr class="hover:bg-gray-50/50">
                            <td rowspan="4"
                                class="sticky left-0 z-20 bg-white px-4 py-3 border-r border-gray-100 border-b font-medium text-gray-900 align-middle shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)]">
                                {{$loop->iteration}}
                            </td>
                            <td rowspan="4"
                                class="sticky left-0 z-20 bg-white px-4 py-3 border-r border-gray-100 border-b font-medium text-gray-900 align-middle shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)]">
                                <div class="text-[10px] text-gray-400 font-mono">{{ $emp['emp_id'] }}</div>
                                <div class="font-bold text-gray-800">{{ $emp['name'] }}</div>
                            </td>
                            <td
                                class="sticky left-[180px] z-20 bg-gray-50 px-2 py-1 border-r border-gray-100 border-b font-bold text-blue-600 text-center">
                                IN</td>
                            @foreach ($dates as $dateKey => $val)
                                <td
                                    class="px-2 py-1 text-center border-r border-gray-100 border-b {{ $val['is_weekend'] ? 'bg-gray-50/30' : '' }}">
                                    @if (isset($emp['data'][$dateKey]->clock_in))
                                        {{ \Carbon\Carbon::parse($emp['data'][$dateKey]->clock_in)->format('H:i') }}
                                    @else
                                        -
                                    @endif
                                </td>
                            @endforeach
                        </tr>

                        {{-- Row 2: OUT --}}
                        <tr class="hover:bg-gray-50/50">
                            <td
                                class="sticky left-[180px] z-20 bg-gray-50 px-2 py-1 border-r border-gray-100 border-b font-bold text-orange-600 text-center">
                                OUT</td>
                            @foreach ($dates as $dateKey => $val)
                                <td
                                    class="px-2 py-1 text-center border-r border-gray-100 border-b {{ $val['is_weekend'] ? 'bg-gray-50/30' : '' }}">
                                    @if (isset($emp['data'][$dateKey]->clock_in))
                                        {{ \Carbon\Carbon::parse($emp['data'][$dateKey]->clock_out)->format('H:i') }}
                                    @else
                                        -
                                    @endif
                                </td>
                            @endforeach
                        </tr>

                        {{-- Row 3: OT (Overtime) --}}
                        <tr class="hover:bg-gray-50/50">
                            <td
                                class="sticky left-[180px] z-20 bg-gray-50 px-2 py-1 border-r border-gray-100 border-b font-bold text-green-600 text-center">
                                OT</td>
                            @foreach ($dates as $dateKey => $val)
                                <td
                                    class="px-2 py-1 text-center border-r border-gray-100 border-b font-medium {{ $val['is_weekend'] ? 'bg-gray-50/30' : '' }}">
                                    {{ $emp['data'][$dateKey]->overtime_fix_hours ?? '0' }}
                                </td>
                            @endforeach
                        </tr>

                        {{-- Row 4: CL/EL (Late/Early) --}}
                        <tr class="hover:bg-gray-50/50 border-b-2 border-gray-300">
                            <td
                                class="sticky left-[180px] z-20 bg-gray-50 px-2 py-1 border-r border-gray-100 border-b font-bold text-red-600 text-center uppercase">
                                CL/EL</td>
                            @foreach ($dates as $dateKey => $val)
                                <td
                                    class="px-2 py-1 text-center border-r border-gray-100 border-b text-red-400 {{ $val['is_weekend'] ? 'bg-gray-50/30' : '' }}">
                                    {{ intval($emp['data'][$dateKey]->coming_late ?? 0) + intval($emp['data'][$dateKey]->early_leave ?? 0) }}
                                </td>
                            @endforeach
                            <td
                                class="sticky left-[180px] z-20 bg-gray-50 px-2 py-1 border-r border-gray-100 border-b font-bold text-green-600 text-center">
                                {{ $emp['data']->sum('overtime_fix_hours') }}
                            </td>
                            <td
                                class="sticky left-[180px] z-20 bg-gray-50 px-2 py-1 border-r border-gray-100 border-b font-bold text-red-600 text-center">
                                {{ $emp['data']->sum('coming_late') + $emp['data']->sum('early_leave') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div
                class="p-4 bg-white border-t border-gray-200 flex flex-wrap gap-4 text-[10px] text-gray-500 uppercase tracking-wider">
                <div class="flex items-center gap-1.5">
                    <span class="w-3 h-0.5 bg-blue-500"></span> IN - Jam Masuk 进入
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-3 h-0.5 bg-red-400"></span> OUT - Jam Keluar 回家
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-3 h-0.5 bg-green-400"></span> OT - Overtime / Lembur 加班
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-3 h-0.5 bg-red-500"></span> CL/EL - Coming Late / Early Leave 迟到/早退
                </div>
            </div>
            <div
                class="p-4 bg-yellow-200/50 border-t border-gray-200 flex flex-col gap-4 text-[10px] text-gray-500 uppercase tracking-wider">
                <div class="flex items-center gap-1.5">
                    <span class="w-3 h-0.5 bg-green-500"></span>
                    <p class="font-bold">OT yang ditampilkan sudah melalui perkalian (x1.5) untuk hari kerja dan (x2)
                        untuk hari libur 显示的加班时间已乘以（x1.5，工作日）和（x2，节假日）。</p>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-3 h-0.5 bg-red-500"></span>
                    <p class="font-bold">CL/EL yang ditampilkan adalah total dari keterlambatan masuk dan pulang cepat
                        (dalam jam) 显示的 CL/EL 是晚点到达和早退的总和（以小时为单位）。</p>
                </div>
            </div>
        </div>
    </div>
@endsection
