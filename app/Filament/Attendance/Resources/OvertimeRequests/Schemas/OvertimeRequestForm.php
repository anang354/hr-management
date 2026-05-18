<?php

namespace App\Filament\Attendance\Resources\OvertimeRequests\Schemas;

use App\Models\Employee;
use Filament\Forms\Components;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Actions\Action;

class OvertimeRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Lembur')
                    ->schema([
                        Components\DatePicker::make('overtime_date')->required()->native(false),
                        Components\TextInput::make('content')->required()->maxLength(255),
                        Components\Hidden::make('user_id')->default(auth()->id()),
                    ])->columns(2),
                Section::make('Daftar Karyawan Lembur')
                    ->columnSpanFull()
                    ->headerActions([
                        Action::make('syncFirstRow')
                            ->label('Terapkan Baris Pertama ke Semua')
                            ->icon('heroicon-m-arrow-path')
                            ->color('info')
                            ->tooltip('Menyalin jam & total jam dari baris pertama ke semua baris di bawahnya')
                            ->requiresConfirmation() // Opsional: Agar tidak sengaja menimpa data yang sudah ada
                            ->action(function (Set $set, Get $get) {
                                // 1. Ambil semua data dari repeater bernama 'items'
                                $items = $get('items');

                                // 2. Pastikan ada minimal 2 baris untuk dilakukan sinkronisasi
                                if (is_array($items) && count($items) > 1) {

                                    // 3. Ambil data dari baris paling atas (index pertama)
                                    $firstKey = array_key_first($items);
                                    $firstItem = $items[$firstKey];

                                    $startTime = $firstItem['start_time'] ?? null;
                                    $endTime = $firstItem['end_time'] ?? null;
                                    $otHours = $firstItem['overtime_hours'] ?? null;

                                    // 4. Iterasi semua baris dan set nilainya sesuai baris pertama
                                    foreach ($items as $key => $item) {
                                        // Kita lewati baris pertama karena itu adalah sumbernya
                                        if ($key === $firstKey)
                                            continue;

                                        $set("items.{$key}.start_time", $startTime);
                                        $set("items.{$key}.end_time", $endTime);
                                        $set("items.{$key}.overtime_hours", $otHours);
                                    }
                                }
                            })
                    ])
                    ->schema([
                        Components\Repeater::make('items')
                            ->relationship()
                            ->itemLabel(function (array $state, $component): string {
                                $items = $component->getState();

                                // Cari nama karyawan berdasarkan ID yang dipilih (jika ada)
                                $employeeName = "";
                                if (!empty($state['employee_id'])) {
                                    $employeeName = Employee::find($state['employee_id'])?->name;
                                }

                                return ($employeeName ?: 'Pilih Karyawan...');
                            })
                            ->collapsible()
                            ->schema([
                                Components\Select::make('employee_id')
                                    ->searchable()
                                    ->preload()
                                    ->options(function () {
                                        $user = auth()->user();

                                        // Ambil department_id dari employee milik user yang login
                                        $departmentId = optional($user->department)->id;

                                        if (!$departmentId) {
                                            return [];
                                        }

                                        return Employee::where('department_id', $departmentId)
                                            ->where('is_active', true)
                                            ->pluck('name', 'id');
                                    })
                                    ->getOptionLabelUsing(fn($value) => Employee::find($value)?->name)
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                    ->required(),
                                Components\TimePicker::make('start_time')
                                    ->required()
                                    ->native(false) // Enables JS picker
                                    ->format('H:i') // Sets 24h format
                                    ->displayFormat('H:i') // Sets 24h display
                                    ->seconds(false),
                                Components\TimePicker::make('end_time')
                                    ->required()
                                    ->native(false) // Enables JS picker
                                    ->format('H:i') // Sets 24h format
                                    ->displayFormat('H:i') // Sets 24h display
                                    ->seconds(false),
                                Components\TextInput::make('overtime_hours')
                                    ->numeric()
                                    ->required(),
                            ])->columns(4)
                            ->columnSpanFull(),
                    ])
            ]);
    }
}
