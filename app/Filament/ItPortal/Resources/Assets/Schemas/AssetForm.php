<?php

namespace App\Filament\ItPortal\Resources\Assets\Schemas;

use App\Models\Asset;
use App\Models\AssetItem;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class AssetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Select::make('asset_item_id')
                    ->relationship('item', 'model')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->category->name . ' - ' . $record->brand . ' ' . $record->model)
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Set $set, $state, $livewire) {
                        if ($livewire && $livewire->record) {
                            return;
                        }
                        $set('specifications', []);

                        if (! $state) {
                            $set('asset_code', null);
                            return;
                        }

                        $item = AssetItem::with('category')->find($state);
                        if (! $item || ! filled($item->category->code)) {
                            $set('asset_code', null);
                            return;
                        }

                        $categoryCode = strtoupper($item->category->code);
                        $sequence = Asset::whereHas('item.category', function ($query) use ($categoryCode) {
                            $query->where('code', $categoryCode);
                        })->count() + 1;

                        $set('asset_code', sprintf('DSI-%s-%03d', $categoryCode, $sequence));
                    }),
                Select::make('asset_location_id')
                    ->relationship('location', 'name')
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->required(),
                TextInput::make('asset_code')
                    // disable saat menu edit, enable saat menu create
                    // ->disabled(fn ($livewire) => $livewire && $livewire->record !== null)
                    ->default(function (Get $get) {
                        $itemId = $get('asset_item_id');
                        if (! $itemId) {
                            return null;
                        }

                        $item = AssetItem::with('category')->find($itemId);
                        if (! $item || ! filled($item->category->code)) {
                            return null;
                        }

                        $categoryCode = strtoupper($item->category->code);
                        $sequence = Asset::whereHas('item.category', function ($query) use ($categoryCode) {
                            $query->where('code', $categoryCode);
                        })->count() + 1;

                        return sprintf('DSI-%s-%03d', $categoryCode, $sequence);
                    }),
                Section::make('Specifications')
                    ->columnSpanFull()
                    ->description('Please fill in the specifications based on the selected item.')
                    ->schema(function (Get $get) {
                    $itemId = $get('asset_item_id');

                    if (! $itemId) {
                        return [];
                    }

                    $item = \App\Models\AssetItem::with('category')->find($itemId);
                    $itemType = strtolower($item->category->name ?? '');

                    return match (true) {
                        str_contains($itemType, 'pc') || str_contains($itemType, 'laptop') => [
                            TextInput::make('specifications.processor')->label('Processor')->dehydrated(),
                            TextInput::make('specifications.ram')->label('Kapasitas RAM (GB)')->numeric()->dehydrated(),
                            TextInput::make('specifications.storage')->label('Kapasitas Storage')->dehydrated(),
                        ],

                        str_contains($itemType, 'router') || str_contains($itemType, 'switch') => [
                            TextInput::make('specifications.total_ports')->label('Total Port')->numeric()->dehydrated(),
                        ],

                        str_contains($itemType, 'monitor') => [
                            TextInput::make('specifications.screen_size')->label('Ukuran Layar (Inch)')->numeric()->dehydrated(),
                        ],

                        default => [
                            KeyValue::make('specifications.custom_specs')
                                ->label('Spesifikasi Lainnya')
                                ->keyLabel('Nama Spec (Misal: Warna)')
                                ->valueLabel('Nilai (Misal: Hitam)')->dehydrated(),
                        ],
                    };
                })
                ->visible(fn (Get $get) => filled($get('asset_item_id'))),
                Radio::make('status')
                    ->required()
                    ->default('active')
                    ->options(\App\Models\Asset::STATUS),
                Radio::make('condition')
                    ->required()
                    ->default('good')
                    ->options(\App\Models\Asset::CONDITION),
                TextInput::make('user'),
                TextInput::make('ip_address'),
                DatePicker::make('date_of_entry')
                    ->native(false),
                DatePicker::make('damage_date')
                    ->native(false),
                TextInput::make('notes'),
            ]);
    }
}
