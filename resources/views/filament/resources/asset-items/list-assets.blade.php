<x-filament-panels::page>
    {{-- Mempertahankan Filter / Search jika ada --}}
    @if ($this->hasTableHeader())
        @php
            $filters = $this->table->getFilters() ?? null;
        @endphp
        <form method="GET" class="mb-4 flex gap-2 items-center">
            <input
                type="search"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search brand or model"
                class="rounded-md border border-gray-700 px-3 py-2"
            />
            <button type="submit" class="flex bg-primary-600 text-white hover:bg-primary-500 px-4 py-2 rounded-md transition-all duration-200">
                <x-heroicon-o-magnifying-glass class="w-5 h-5 stroke-2 mr-2" /> Search</button>
            <a href="{{ url()->current() }}" class="bg-gray-200 text-gray-800 hover:bg-gray-300 px-4 py-2 rounded-md transition-all duration-200 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600">Clear</a>
        </form>
        {{-- @if ($filters)
            <div class="mb-4">
                @if (is_string($filters))
                    {!! $filters !!}
                @elseif (is_object($filters) && method_exists($filters, 'render'))
                    {!! $filters->render() !!}
                @elseif (is_array($filters))
                    @foreach ($filters as $filter)
                        @if (is_string($filter))
                            {!! $filter !!}
                        @elseif (is_object($filter) && method_exists($filter, 'render'))
                            {!! $filter->render() !!}
                        @endif
                    @endforeach
                @endif
            </div>
        @endif --}}
    @endif

    {{-- Grid Layout Card List --}}
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        @forelse ($this->getTableRecords() as $record)
            <div class="overflow-hidden transition-all duration-200 bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md dark:bg-gray-900 dark:border-gray-800">

                {{-- Container Gambar / Image Asset --}}
                <div class="flex items-center justify-center w-full h-48 bg-gray-100 dark:bg-gray-800 border-b border-gray-100 dark:border-gray-800">
                        @if ($record->photo)
                        <img src="{{ Storage::url($record->photo) }}" alt="{{ $record->photo }}" class="object-cover w-full h-full">
                    @else
                        {{-- Placeholder jika tidak ada foto --}}
                        <div class="flex flex-col items-center justify-center text-gray-400">
                            <x-heroicon-o-photo class="w-12 h-12 stroke-1" />
                            <span class="mt-1 text-xs font-medium uppercase tracking-wider">IMAGE</span>
                        </div>
                    @endif
                </div>

                {{-- Informasi Aset / Barang --}}
                <div class="p-4">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white line-clamp-1">
                        {{ $record->brand }} {{ $record->model }}
                    </h3>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        {{ $record->category->name ?? 'Uncategorized' }}
                    </p>

                    {{-- Action Buttons (Edit / View / Delete) --}}
                    <div class="flex items-center justify-between gap-2 pt-3 mt-4 border-t border-gray-100 dark:border-gray-800">
                        <p class="text-sm">Total Items: {{ $record->asset_count }}</p>
                        <a href="{{ static::$resource::getUrl('edit', ['record' => $record]) }}"
                           class="text-sm font-medium text-primary-600 hover:underline dark:text-primary-400">
                            Edit
                        </a>
                    </div>
                </div>

            </div>
        @empty
            <div class="col-span-full">
                <div class="p-12 text-center bg-white rounded-xl border border-gray-200 dark:bg-gray-900 dark:border-gray-800">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada aset / barang yang terdaftar.</p>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Pagination Filament --}}
    <div class="mt-6">
        {{ $this->getTableRecords()->links() }}
    </div>
</x-filament-panels::page>
