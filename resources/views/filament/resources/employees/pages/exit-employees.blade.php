<x-filament-panels::page>
    <form wire:submit.prevent="save">

        <div class="mt-6 flex justify-end gap-3">
            @foreach ($this->getFormActions() as $action)
                {{ $action }}
            @endforeach
        </div>
    </form>
    {{ $this->form }}
</x-filament-panels::page>
