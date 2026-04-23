<?php

namespace App\Filament\Resources\Contracts\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Illuminate\Support\HtmlString;

class ContractForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make()
                    ->columnSpanFull()
                    ->columns(6)
                    ->schema([
                        Section::make('Contract')
                            ->columnSpan(4)
                            ->columns(2)
                            ->schema([
                                TextInput::make('contract_number')
                                    ->label('Contract Number')
                                    ->default(fn() => \App\Models\Contract::generateNextContractNumber())
                                    ->readOnly()
                                    ->required(),
                                Select::make('employee_id')
                                    ->label('Employee')
                                    ->relationship('employee', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->live() // Kunci utama reaktivitas
                                    ->afterStateUpdated(fn($state, Set $set) => $set('employee_id', $state))
                                    ->required(),
                                Section::make('Periode of Contract')
                                    ->columnSpanFull()
                                    ->columns(4)
                                    ->schema([
                                        DatePicker::make('start_date')
                                            ->label('Start Date')
                                            ->required(),
                                        DatePicker::make('end_date')
                                            ->label('End Date')
                                            ->required(),
                                        TextInput::make('contract_periode')
                                            ->label('Contract Periode')
                                            ->numeric()
                                            ->suffix('Bulan')
                                            ->required(),
                                        Radio::make('contract_type')
                                            ->label('Contract Type')
                                            ->options([
                                                'pkwt' => 'PKWT',
                                                'job_training' => 'Job Training',
                                            ])
                                            ->required(),
                                    ]),
                                Section::make('Detail Salary')
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->schema([
                                        TextInput::make('gaji_pokok')
                                            ->label('Gaji Pokok')
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                $salary = $state;
                                                $tunjangan_jabatan = $get('tunjangan_jabatan');
                                                $tunjangan_lain = $get('tunjangan_lainnya');
                                                $tunjangan_transportasi = $get('tunjangan_transportasi');
                                                $tunjangan_keahlian = $get('tunjangan_keahlian');
                                                $tunjangan_bahasa = $get('tunjangan_bahasa');
                                                $total = $salary + $tunjangan_jabatan + $tunjangan_bahasa + $tunjangan_keahlian + $tunjangan_lain + $tunjangan_transportasi;
                                                $set('total_gaji', $total);
                                            })
                                            ->numeric()
                                            ->required(),
                                        TextInput::make('tunjangan_jabatan')
                                            ->label('Tunjangan Jabatan')
                                            ->default(0)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                $tunjangan_jabatan = $state;
                                                $tunjangan_lain = $get('tunjangan_lainnya');
                                                $tunjangan_keahlian = $get('tunjangan_keahlian');
                                                $tunjangan_bahasa = $get('tunjangan_bahasa');
                                                $tunjangan_transportasi = $get('tunjangan_transportasi');
                                                $salary = $get('gaji_pokok');
                                                $total = $salary + $tunjangan_jabatan + $tunjangan_bahasa + $tunjangan_keahlian + $tunjangan_lain + $tunjangan_transportasi;
                                                $set('total_gaji', $total);
                                            })
                                            ->numeric()
                                            ->required(),
                                        TextInput::make('tunjangan_bahasa')
                                            ->label('Tunjangan Bahasa')
                                            ->default(0)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                $tunjangan_jabatan = $get('tunjangan_jabatan');
                                                $tunjangan_lain = $get('tunjangan_lainnya');
                                                $tunjangan_keahlian = $get('tunjangan_keahlian');
                                                $tunjangan_bahasa = $get('tunjangan_bahasa');
                                                $tunjangan_transportasi = $get('tunjangan_transportasi');
                                                $salary = $get('gaji_pokok');
                                                $total = $salary + $tunjangan_jabatan + $tunjangan_bahasa + $tunjangan_keahlian + $tunjangan_lain + $tunjangan_transportasi;
                                                $set('total_gaji', $total);
                                            })
                                            ->numeric()
                                            ->required(),
                                        TextInput::make('tunjangan_keahlian')
                                            ->label('Tunjangan Keahlian')
                                            ->default(0)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                $tunjangan_jabatan = $get('tunjangan_jabatan');
                                                $tunjangan_lain = $get('tunjangan_lainnya');
                                                $tunjangan_keahlian = $get('tunjangan_keahlian');
                                                $tunjangan_bahasa = $get('tunjangan_bahasa');
                                                $tunjangan_transportasi = $get('tunjangan_transportasi');
                                                $salary = $get('gaji_pokok');
                                                $total = $salary + $tunjangan_jabatan + $tunjangan_bahasa + $tunjangan_keahlian + $tunjangan_lain + $tunjangan_transportasi;
                                                $set('total_gaji', $total);
                                            })
                                            ->numeric()
                                            ->required(),
                                        TextInput::make('tunjangan_transportasi')
                                            ->label('Tunjangan Transportasi')
                                            ->default(0)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                $tunjangan_jabatan = $get('tunjangan_jabatan');
                                                $tunjangan_lain = $get('tunjangan_lainnya');
                                                $tunjangan_keahlian = $get('tunjangan_keahlian');
                                                $tunjangan_bahasa = $get('tunjangan_bahasa');
                                                $tunjangan_transportasi = $get('tunjangan_transportasi');
                                                $salary = $get('gaji_pokok');
                                                $total = $salary + $tunjangan_jabatan + $tunjangan_bahasa + $tunjangan_keahlian + $tunjangan_lain + $tunjangan_transportasi;
                                                $set('total_gaji', $total);
                                            })
                                            ->numeric()
                                            ->required(),
                                        TextInput::make('tunjangan_lainnya')
                                            ->label('Tunjangan Lainnya')
                                            ->default(0)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                $tunjangan_jabatan = $get('tunjangan_jabatan');
                                                $tunjangan_lain = $get('tunjangan_lainnya');
                                                $tunjangan_keahlian = $get('tunjangan_keahlian');
                                                $tunjangan_bahasa = $get('tunjangan_bahasa');
                                                $tunjangan_transportasi = $get('tunjangan_transportasi');
                                                $salary = $get('gaji_pokok');
                                                $total = $salary + $tunjangan_jabatan + $tunjangan_bahasa + $tunjangan_keahlian + $tunjangan_lain + $tunjangan_transportasi;
                                                $set('total_gaji', $total);
                                            })
                                            ->numeric()
                                            ->required(),
                                        TextInput::make('total_gaji')
                                            ->label('Total Gaji')
                                            ->columnSpanFull()
                                            ->mask(RawJs::make('$money($input)'))
                                            ->stripCharacters(',')
                                            ->readOnly()
                                            ->numeric()
                                            ->required(),
                                    ]),

                                Toggle::make('is_active')
                                    ->label('Is Active')
                                    ->default(true)
                                    ->required(),
                            ]),
                        Section::make('Detail Kontrak Terakhir')
                            ->collapsible()
                            ->columnSpan(2)
                            ->visibleOn('create')
                            ->hidden(fn(Get $get) => !$get('employee_id')) // Hanya muncul jika karyawan sudah dipilih
                            ->schema([
                                Placeholder::make('latest_contract_info')
                                    ->label('')
                                    ->content(function (Get $get) {
                                        $employeeId = $get('employee_id');

                                        if (!$employeeId)
                                            return null;

                                        // Ambil kontrak terakhir karyawan
                                        $latestContract = \App\Models\Contract::where('employee_id', $employeeId)
                                            ->latest('start_date')
                                            ->first();

                                        if (!$latestContract) {
                                            return new HtmlString('
                        <div class="text-danger-600 bg-danger-50 p-4 rounded-lg border border-danger-100 dark:bg-danger-900/10 dark:border-danger-900/20">
                            <x-heroicon-m-x-circle class="w-5 h-5" />
                            <span class="font-medium text-sm">Karyawan ini belum memiliki data kontrak atau merupakan karyawan baru.</span>
                        </div>
                    ');
                                        }

                                        // Tampilan ala Infolist di dalam Form
                                        return new HtmlString("
                    <div class='grid grid-cols-2 gap-4 text-sm'>
                        <div class='col-span-2 space-y-1'>
                            <p class='text-gray-500 dark:text-gray-400'>No. Kontrak:</p>
                            <p class='font-bold text-gray-900 dark:text-white'>{$latestContract->contract_number}</p>
                        </div>
                        <div class='col-span-2 space-y-1'>
                            <p class='text-gray-500 dark:text-gray-400'>Masa Kontrak:</p>
                            <p class='font-medium text-gray-900 dark:text-white'>
                                {$latestContract->start_date->format('d M Y')} - {$latestContract->end_date->format('d M Y')} (<span class='text-primary-600 dark:text-primary-400'>{$latestContract->contract_periode} Bulan</span>)
                            </p>
                        </div>
                        <div class='col-span-2 border-t pt-2 mt-2 dark:border-gray-700'>
                            <p class='text-xs font-bold uppercase text-primary-600 dark:text-primary-400 mb-2'>Rincian Gaji</p>
                        </div>
                        <div class='space-y-1'>
                            <p class='text-gray-500 dark:text-gray-400'>Gaji Pokok:</p>
                            <p class='font-bold text-gray-900 dark:text-white'>" . number_format($latestContract->gaji_pokok) . "</p>
                        </div>
                        <div class='space-y-1'>
                            <p class='text-gray-500 dark:text-gray-400'>Tunjangan Jabatan:</p>
                            <p class='font-bold text-gray-900 dark:text-white'>" . number_format($latestContract->tunjangan_jabatan) . "</p>
                        </div>
                        <div class='space-y-1'>
                            <p class='text-gray-500 dark:text-gray-400'>Tunjangan Keahlian:</p>
                            <p class='font-bold text-gray-900 dark:text-white'>" . number_format($latestContract->tunjangan_keahlian) . "</p>
                        </div>
                        <div class='space-y-1'>
                            <p class='text-gray-500 dark:text-gray-400'>Tunjangan Transportasi:</p>
                            <p class='font-bold text-gray-900 dark:text-white'>" . number_format($latestContract->tunjangan_transportasi) . "</p>
                        </div>
                        <div class='space-y-1'>
                            <p class='text-gray-500 dark:text-gray-400'>Tunjangan Lainnya:</p>
                            <p class='font-bold text-gray-900 dark:text-white'>" . number_format($latestContract->tunjangan_lainnya) . "</p>
                        </div>
                        <div class='space-y-1'>
                            <p class='text-gray-500 dark:text-gray-400'>Total Gaji:</p>
                            <p class='font-bold text-success-600 dark:text-success-400'>" . number_format($latestContract->total_gaji) . "</p>
                        </div>
                    </div>
                ");
                                    })
                            ]),
                    ]),



            ]);
    }
}
