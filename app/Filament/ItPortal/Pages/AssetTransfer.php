<?php

namespace App\Filament\ItPortal\Pages;

use App\Models\Asset;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class AssetTransfer extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.it-portal.pages.asset-transfer';

    protected static ?string $model = Asset::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowsRightLeft;
    public ?array $data = [];
    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Repeater::make('asset_transfers')
                    ->label('Transfer Asset')
                    ->schema([
                        Select::make('asset_code')
                            ->label(__('it_portal.assets.asset_code'))
                            ->options(Asset::pluck('asset_code', 'id'))
                            ->live()
                            ->searchable()
                            ->preload()
                            ->required()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $asset = Asset::find($state);
                                if ($asset) {
                                    $set('asset_location_id', $asset->asset_location_id);
                                    $set('user', $asset->user);
                                    $set('ip_address', $asset->ip_address);
                                }
                            }),
                        Select::make('asset_location_id')
                            ->label(__('it_portal.assets.location'))
                            ->searchable()
                            ->preload()
                            ->options(\App\Models\AssetLocation::pluck('name', 'id'))
                            ->afterStateHydrated(function ($state, callable $set) {
                                $set('location_id', $state);
                            })
                            ->required(),
                        TextInput::make('user')
                            ->label(__('it_portal.assets.user'))
                            ->afterStateHydrated(function ($state, callable $set) {
                                $set('user', $state);
                            }),
                        TextInput::make('ip_address')
                            ->label(__('it_portal.assets.ip_address'))
                            ->afterStateHydrated(function ($state, callable $set) {
                                $set('ip_address', $state);
                            }),
                    ])
                    ->columns(4)
                    ->addActionLabel('Add Asset')
                    ->reorderable(false)
            ])
            ->statePath('data')
            ->model(Asset::class);
    }
    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Process Transfer')
                ->icon('heroicon-o-arrows-right-left')
                ->submit('save')
                ->color('primary'),
        ];
    }

    public function save(): void
    {
        $input = $this->form->getState()['asset_transfers'];
        $count = 0;

        foreach ($input as $item) {
            $count++;
            Asset::where('id', $item['asset_code'])->update([
                'asset_location_id' => $item['asset_location_id'],
                'user' => $item['user'],
                'ip_address' => $item['ip_address'],
            ]);
        }

        Notification::make()
            ->title('Berhasil memproses ' . $count . ' transfer aset')
            ->success()
            ->send();

        $this->form->fill(); // Reset form
    }
}
