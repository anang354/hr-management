<x-filament-panels::page>
    <style>
        /* CSS Manual untuk memastikan layout tetap terbagi dua di layar besar */
        @media (min-width: 1024px) {
            .custom-wrapper {
                display: flex;
                align-items: flex-start;
                gap: 1.5rem;
            }

            .mt-2 {
                margin-top: 15px;
            }

            .left-side {
                width: 33.333%;
                /* Sama dengan col-span-4 */
                flex-shrink: 0;
            }

            .right-side {
                flex-grow: 1;
                /* Mengambil sisa ruang */
            }
        }

        .photo {
            width: 230px;
            height: 230px;
            margin: 15px auto;
            border-radius: 50%;
            object-fit: cover;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Layout untuk Mobile (Stack Vertikal) */
        @media (max-width: 1023px) {
            .custom-wrapper {
                display: flex;
                flex-direction: column;
                gap: 1.5rem;
            }
        }
    </style>

    <div class="custom-wrapper">

        <div class="left-side">
            <x-filament::section>
                <div class="space-y-6">
                    {{-- Select Search --}}
                    <div>
                        {{ $this->schema }}
                    </div>

                    @if($this->employee_id && $employee = $this->selected_employee)
                        <div class="flex flex-col items-center pt-6 border-t border-gray-100 dark:border-white/10">
                            <div class="mb-4">
                                @if($employee->photo)
                                    <img src="{{ asset('storage/' . $employee->photo) }}" class="photo">
                                @else
                                    <div
                                        class="w-32 h-32 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center border-4 border-gray-200 dark:border-gray-700">
                                        <x-heroicon-o-user class="w-16 h-16 text-gray-400" />
                                    </div>
                                @endif
                            </div>

                            <div class="w-full mt-2 space-y-3">
                                <div class="flex justify-between text-sm">
                                    <span
                                        class="text-gray-500 dark:text-gray-400">{{ __('employee_overtime_review.content.department') }}:</span>
                                    <span
                                        class="font-semibold text-gray-900 dark:text-white">{{ $employee->department?->name ?? '-' }}</span>
                                </div>
                                <div class="flex mt-2 justify-between text-sm">
                                    <span
                                        class="text-gray-500 dark:text-gray-400">{{ __('employee_overtime_review.content.position') }}:</span>
                                    <span
                                        class="font-semibold text-gray-900 dark:text-white">{{ $employee->job ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="py-4 mt-2 text-center text-gray-400 italic text-sm">
                            Silahkan pilih karyawan di atas...
                        </div>
                    @endif
                </div>
            </x-filament::section>
        </div>

        <div class="right-side">
            <x-filament::section>
                <x-slot name="heading">
                    Attendance of
                    @if($this->employee_id && $employee = $this->selected_employee)
                        {{ $employee->name }}
                    @endif
                </x-slot>

                <div class="mt-4">
                    {{ $this->table }}
                </div>
            </x-filament::section>
        </div>

    </div>
</x-filament-panels::page>
