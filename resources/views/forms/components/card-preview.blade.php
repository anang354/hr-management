<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
  <div x-data="{ state: $wire.$entangle('{{ $getStatePath() }}') }">
    <!-- Interact with the `state` property in Alpine.js -->
    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
      <div class="flex flex-col md:flex-row gap-6 justify-around items-center">
        {{-- Contoh Gambar Vertikal --}}
        <div class="text-center">
          <img src="https://placehold.co/100x160/E0E7FF/3B82F6?text=Potrait" alt="Contoh ID Card Potrait"
            class="rounded-lg shadow-md border border-gray-300 dark:border-gray-500 mb-2 mx-auto"
            style="width: 100px; height: 160px; object-fit: cover;">
        </div>

        {{-- Contoh Gambar Horizontal --}}
        <div class="text-center">
          <img src="https://placehold.co/160x100/E0E7FF/3B82F6?text=Landscape" alt="Contoh ID Card Landscape"
            class="rounded-lg shadow-md border border-gray-300 dark:border-gray-500 mb-2 mx-auto"
            style="width: 160px; height: 100px; object-fit: cover;">
        </div>
      </div>
    </div>

  </div>
</x-dynamic-component>
