<x-filament-panels::page>

    <x-filament::section>
        {{ $this->schema }}
    </x-filament::section>

    <div class="grid grid-cols-2 gap-6">

        {{-- Attendance Table --}}
        <x-filament::section>

            <x-slot name="heading">
                Attendance Data
            </x-slot>

            <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-white/10">

                <table class="w-full divide-y divide-gray-200 dark:divide-white/10">

                    <thead class="bg-gray-50 dark:bg-white/5">

                        <tr>

                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-950 dark:text-white">
                                Date
                            </th>

                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-950 dark:text-white">
                                Employee
                            </th>

                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-950 dark:text-white">
                                Shift
                            </th>

                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-950 dark:text-white">
                                Jam
                            </th>

                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-950 dark:text-white">
                                OT
                            </th>
                        </tr>

                    </thead>

                    <tbody class="divide-y divide-gray-200 dark:divide-white/5 bg-white dark:bg-transparent">

                        @forelse ($this->attendanceData as $item)
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition">

                                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-200">
                                    {{ $item->date }}
                                </td>

                                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-200">
                                    {{ $item->attendance_user?->display_name }}
                                </td>

                                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-200">
                                    {{ $item->attendance_shift?->name }}
                                </td>

                                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-200">
                                        <small>{{ !is_null($item->clock_in) ? \Carbon\Carbon::parse($item->clock_in)->format('H:i') : '-' }}</small>
                                        <br />
                                        <small>{{ !is_null($item->clock_out) ? \Carbon\Carbon::parse($item->clock_out)->format('H:i') : '-' }}</small>
                                </td>

                                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-200">
                                    {{ $item->overtime_hours }}
                                </td>

                            </tr>
                        @empty

                            <tr>

                                <td colspan="6" class="px-4 py-10 text-center text-sm text-gray-500">
                                    No attendance data found.
                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

        </x-filament::section>

        {{-- Overtime Table --}}
        <x-filament::section>

            <x-slot name="heading">
                Overtime
            </x-slot>

            <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-white/10">

                <table class="w-full divide-y divide-gray-200 dark:divide-white/10">

                    <thead class="bg-gray-50 dark:bg-white/5">

                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-semibold">
                                Date
                            </th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">
                                Employee
                            </th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">
                                Jam
                            </th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">
                                OT
                            </th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">
                                Status
                            </th>
                        </tr>

                    </thead>

                    <tbody class="divide-y divide-gray-200 dark:divide-white/5">

                        @forelse ($this->overtimeData as $item)
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition">

                                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-200">
                                    {{ $item->overtimeRequest->overtime_date }}
                                </td>

                                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-200">
                                    {{ \Illuminate\Support\Str::limit($item->employee?->name, 10, '...') }}
                                </td>

                                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-200">
                                    <small>{{ \Carbon\Carbon::parse($item->start_time)->format('H:i') }}</small> <br />
                                    <small>{{ \Carbon\Carbon::parse($item->end_time)->format('H:i') }}</small>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-200">
                                    {{ $item->overtime_hours }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-200">
                                    @if ($item->status === \App\Enums\OvertimeItemStatus::Approved)
                                        <span
                                            class="bg-green-100 text-green-800 px-2 py-1 rounded-md text-xs font-medium">Approved</span>
                                    @elseif ($item->status === \App\Enums\OvertimeItemStatus::Pending)
                                        <span
                                            class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-md text-xs font-medium">Pending</span>
                                    @elseif ($item->status === \App\Enums\OvertimeItemStatus::Rejected)
                                        <span
                                            class="bg-red-100 text-red-800 px-2 py-1 rounded-md text-xs font-medium">Rejected</span>
                                    @else
                                        <span
                                            class="bg-gray-100 text-gray-800 px-2 py-1 rounded-md text-xs font-medium">Unknown</span>
                                    @endif

                                </td>

                            </tr>
                        @empty

                            <tr>

                                <td colspan="6" class="px-4 py-10 text-center text-sm text-gray-500">
                                    No overtime data found.
                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

        </x-filament::section>

    </div>

</x-filament-panels::page>
