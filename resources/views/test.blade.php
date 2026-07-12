@extends('layouts.admin')
@section('content')
<div class="my-2 w-full">
        <div class="mx-4 flex gap-4">
            <p class="text-sm font-medium text-slate-600">Departmen : </p>
            <p class="text-sm font-medium text-slate-600">From : </p>
            <p class="text-sm font-medium text-slate-600">To : </p>
        </div>
</div>
<div class="w-full max-h-96 overflow-auto border border-gray-200 rounded-lg">
  <table class="w-full border-collapse text-left text-sm text-gray-500">
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
            @for($day = 1; $day <= 31; $day++)
                <th
                    class="sticky top-0 z-10 px-2 py-2 text-center border-r border-gray-100 min-w-[65px]">
                    <div class="text-gray-400 font-medium">{{ $day }}</div>
                    <div class="text-gray-700 font-bold uppercase">{{ $day }}</div>
                </th>
            @endfor
            <th
                class="sticky left-[180px] z-30 bg-gray-50 px-2 py-3 border-r border-gray-100 font-bold text-gray-700 w-[60px]">
                Total OT 总额</th>
            <th
                class="sticky left-[180px] z-30 bg-gray-50 px-2 py-3 border-r border-gray-100 font-bold text-gray-700 w-[60px]">
                Total CL/EL</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-gray-200">
                    @for ($i = 0; $i < 20; $i++)
                        {{-- Row 1: IN --}}
                        <tr class="hover:bg-gray-50/50">
                            <td rowspan="4"
                                class="sticky left-0 z-20 bg-white px-4 py-3 border-r border-gray-100 border-b font-medium text-gray-900 align-middle shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)]">
                                {{$i}}
                            </td>
                            <td rowspan="4"
                                class="sticky left-0 z-20 bg-white px-4 py-3 border-r border-gray-100 border-b font-medium text-gray-900 align-middle shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)]">
                                <div class="text-[10px] text-gray-400 font-mono">123</div>
                                <div class="font-bold text-gray-800">Employee {{ $i }}</div>
                            </td>
                            <td
                                class="sticky left-[180px] z-20 bg-gray-50 px-2 py-1 border-r border-gray-100 border-b font-bold text-blue-600 text-center">
                                IN</td>
                            @for ($day = 1; $day <= 31; $day++)
                                <td
                                    class="px-2 py-1 text-center border-r border-gray-100 border-b">
                                    -
                                </td>
                            @endfor
                        </tr>

                        {{-- Row 2: OUT --}}
                        <tr class="hover:bg-gray-50/50">
                            <td
                                class="sticky left-[180px] z-20 bg-gray-50 px-2 py-1 border-r border-gray-100 border-b font-bold text-orange-600 text-center">
                                OUT</td>
                            @for ($day = 1; $day <= 31; $day++)
                                <td
                                    class="px-2 py-1 text-center border-r border-gray-100 border-b">
                                    -
                                </td>
                            @endfor
                        </tr>

                        {{-- Row 2: OUT --}}
                        <tr class="hover:bg-gray-50/50">
                            <td
                                class="sticky left-[180px] z-20 bg-gray-50 px-2 py-1 border-r border-gray-100 border-b font-bold text-orange-600 text-center">
                                OUT</td>
                            @for ($day = 1; $day <= 31; $day++)
                                <td
                                    class="px-2 py-1 text-center border-r border-gray-100 border-b">
                                    -
                                </td>
                            @endfor
                        </tr>

                        {{-- Row 3: OT (Overtime) --}}
                        <tr class="hover:bg-gray-50/50">
                            <td
                                class="sticky left-[180px] z-20 bg-gray-50 px-2 py-1 border-r border-gray-100 border-b font-bold text-green-600 text-center">
                                OT</td>
                            @for ($day = 1; $day <= 31; $day++)
                                <td
                                    class="px-2 py-1 text-center border-r border-gray-100 border-b font-medium">
                                    {{ $emp['data'][$day]->overtime_fix_hours ?? '0' }}
                                </td>
                            @endfor
                        </tr>

                        {{-- Row 4: CL/EL (Late/Early) --}}
                        <tr class="hover:bg-gray-50/50 border-b-2 border-gray-300">
                            <td
                                class="sticky left-[180px] z-20 bg-gray-50 px-2 py-1 border-r border-gray-100 border-b font-bold text-red-600 text-center uppercase">
                                CL/EL</td>
                            @for ($day = 1; $day <= 31; $day++)
                                <td
                                    class="px-2 py-1 text-center border-r border-gray-100 border-b text-red-400">
                                    0
                                </td>
                            @endfor
                            <td
                                class="sticky left-[180px] z-20 bg-gray-50 px-2 py-1 border-r border-gray-100 border-b font-bold text-green-600 text-center">
                                0
                            </td>
                            <td
                                class="sticky left-[180px] z-20 bg-gray-50 px-2 py-1 border-r border-gray-100 border-b font-bold text-red-600 text-center">
                                0
                            </td>
                        </tr>
                    @endfor
                </tbody>
  </table>
</div>
@endsection
