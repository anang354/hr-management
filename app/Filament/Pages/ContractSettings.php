<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Exceptions\Halt;
use Filament\Support\Icons\Heroicon;


class ContractSettings extends Page implements HasForms
{
    use InteractsWithForms;
    protected string $view = 'filament.pages.contract-settings';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    public ?array $data = [];
    public ?\App\Models\ContractSetting $record = null;
    public function mount(): void
    {
        $this->record = \App\Models\ContractSetting::firstOrCreate([]);
        $this->form->fill($this->record->attributesToArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('hr_name'),
                Section::make('signed_document')
                    ->schema([
                        TextInput::make('sign_1'),
                        TextInput::make('position_1'),
                        TextInput::make('sign_2'),
                        TextInput::make('position_2'),
                    ])->columns(2),
                RichEditor::make('contract_template')
                    ->extraInputAttributes([
                        'style' => 'max-height: 600px; overflow-y: auto;'
                    ])
                    ->toolbarButtons([
                        ['undo', 'redo'],
                        ['bold', 'italic', 'underline', 'strike', 'subscript', 'superscript'],
                        ['h2', 'h3', 'h4', 'h5', 'paragraph'],
                        ['alignStart', 'alignCenter', 'alignEnd', 'alignJustify'],
                        ['bulletList', 'orderedList'],
                        ['table', 'clearFormatting'],
                    ])
                    ->floatingToolbars([
                        'paragraph' => [
                            'bold',
                            'italic',
                            'underline',
                            'alignStart',
                            'alignCenter',
                            'alignEnd',
                            'alignJustify',
                            'bulletList',
                            'orderedList',
                        ],

                    ]),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Settings')
                ->button()
                ->action('save'),
            Action::make('review')
                ->label('Preview Contract')
                ->color('info')
                ->icon('heroicon-o-document-magnifying-glass')
                ->url(route('contract-settings-preview'))
                ->openUrlInNewTab()
                ->button(),
        ];
    }
    public function save(): void
    {
        try {
            $data = $this->form->getState();
            $this->record->update($data);
        } catch (Halt $e) {
            return;
        }
        Notification::make()
            ->success()
            ->title('Successfully saved changes')
            ->send();
    }
}
