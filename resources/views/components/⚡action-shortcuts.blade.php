<?php

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Livewire\Component;

new class extends Component implements HasActions {
    use InteractsWithActions;

    public function goToPanel(): Action
    {
        return Action::make('gotopanel')
            ->iconButton()
            ->icon('heroicon-o-arrows-right-left')
            ->keyBindings(['command+m', 'ctrl+m'])
            ->tooltip(fn() => filament()->getCurrentPanel()->getId() === 'admin' ? 'Go to Attendance Panel' : 'Go to Admin Panel')
            // ->extraAttributes(['class' => 'w-full'])
            // ->label(fn() => filament()->getCurrentPanel()->getId() === 'admin' ? 'Go to Attendance' : 'Go to Admin')
            ->url(fn() => filament()->getCurrentPanel()->getId() === 'admin' ? url('/attendance') : url('/admin'));
    }
};
?>

<div>
    {{-- Smile, breathe, and go slowly. - Thich Nhat Hanh --}}
    {{ $this->goToPanel() }}
</div>
