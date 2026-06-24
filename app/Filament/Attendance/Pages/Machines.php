<?php

namespace App\Filament\Attendance\Pages;

use App\Libs\ZKLibrary;
use App\Models\AttendanceUser;
use App\Models\Machine;
use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;

class Machines extends Page implements HasTable, HasForms
{
    use InteractsWithTable, InteractsWithForms;
    protected string $view = 'filament.attendance.pages.machines';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::Calculator;
    public static function canAccess(): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->isHr();
    }
    protected function getMachineFormSchema(): array
    {
        return [
            Grid::make(2)->schema([
                TextInput::make('name')->required()->label('Nama Mesin'),
                TextInput::make('serial_number')->required()->label('Nomor Seri')->unique(ignoreRecord: true),
                TextInput::make('ip_address')->required()->label('Alamat IP')->unique(ignoreRecord: true),
                TextInput::make('port')->required()->label('Port'),
                TextInput::make('type')->label('Tipe Mesin'),
                TextInput::make('mac_address')->label('Alamat MAC'),
                TextInput::make('location')->label('Lokasi Mesin'),
                Toggle::make('is_active')->label('Aktif')->inline(false),
            ])
            ];
    }
    protected function getHeaderActions(): array
    {
        return [
            Action::make('createMachine')
                ->label('Tambah Mesin')
                ->icon('heroicon-m-plus')
                ->modalHeading('Tambah Mesin')
                ->slideOver()
                ->form($this->getMachineFormSchema())
                ->action(function (array $data) {
                    \App\Models\Machine::create($data);
                    Notification::make()->title('Mesin baru Berhasil Ditambahkan')->success()->send();
                }),
        ];
    }
    public function table(Table $table): Table
    {
        return $table->query(Machine::query())
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Mesin'),
                TextColumn::make('serial_number')
                    ->label('Nomor Seri'),
                TextColumn::make('ip_address')
                    ->label('Alamat IP'),
                TextColumn::make('port')
                    ->label('Port'),
                TextColumn::make('type')
                    ->label('Tipe Mesin'),
                TextColumn::make('mac_address')
                    ->label('Alamat MAC')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('location')
                    ->label('Lokasi Mesin'),
                BooleanColumn::make('is_active')
                    ->label('Aktif'),
            ])
            ->filters([])
            ->actions([
                EditAction::make()
                    ->label('Edit')
                    ->modalHeading('Ubah Mesin')
                    ->slideOver()
                    ->visible(fn() => auth()->user()->role === 'admin')
                    ->form($this->getMachineFormSchema()),
                DeleteAction::make()
                    ->visible(fn() => auth()->user()->role === 'admin'),
                Action::make('log_data')
                    ->label('Ambil Log')
                    ->icon('heroicon-m-circle-stack')
                    ->color('success')
                    ->action(function (Machine $record) {
                        // Implementasikan logika untuk menguji koneksi ke mesin
                        $zk = new ZKLibrary($record->ip_address, $record->port);
                        try {
                            $zk->connect();
                            $getAttendance = $zk->getAttendance();
                            $zk->disconnect();
                            if (empty($getAttendance)) {
                                Notification::make()->title('Tidak ada data log.')->warning()->send();
                                return;
                            }

                            $usersMap = AttendanceUser::pluck('display_name', 'biometric_id')->toArray();

                            // Gunakan OpenSpout bawaan Filament untuk membuat Excel stream langsung
                            $fileName = 'Raw_Log_' . time() . '.xlsx';
                            $filePath = storage_path('app/public/' . $fileName);

                            $writer = new Writer();
                            $writer->openToFile($filePath);

                            // Tulis Header
                            $writer->addRow(Row::fromValues(['ID Biometrik', 'Nama Karyawan', 'Tanggal', 'Waktu Finger']));

                            // Tulis Data isi loop
                            foreach ($getAttendance as $log) {
                                $biometricId = $log[1] ?? null;
                                $dateTimeStr = $log[3] ?? null;

                                if ($biometricId && $dateTimeStr) {
                                    $carbonDt = Carbon::parse($dateTimeStr);
                                    $employeeName = $usersMap[$biometricId] ?? 'Tidak Terdaftar';

                                    $writer->addRow(Row::fromValues([
                                        $biometricId,
                                        $employeeName,
                                        $carbonDt->format('Y-m-d'),
                                        $carbonDt->format('H:i:s')
                                    ]));
                                }
                            }

                            $writer->close();

                            // Download file yang berhasil dibuat
                            return response()->download($filePath)->deleteFileAfterSend(true);
                        } catch (\Exception $e) {
                            Notification::make()->title('Gagal terhubung ke mesin: ' . $record->name)->danger()->send();
                        }
                    }),
                Action::make('clear_log')
                    ->label('Hapus Log')
                    ->color('danger')
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation()
                    ->action(function (Machine $record) {
                        // Implementasikan logika untuk menguji koneksi ke mesin
                        $zk = new ZKLibrary($record->ip_address, $record->port);
                        try {
                            $zk->connect();
                            $getAttendance = $zk->getAttendance();
                            $zk->clearAttendance();
                            $zk->disconnect();
                            if (empty($getAttendance)) {
                                Notification::make()->title('Log Berhasil dihapus dan Tidak ada data log tersedia.')->warning()->send();
                                return;
                            }

                            $usersMap = AttendanceUser::pluck('display_name', 'biometric_id')->toArray();

                            // Gunakan OpenSpout bawaan Filament untuk membuat Excel stream langsung
                            $fileName = 'Raw_Log_' . time() . '.xlsx';
                            $filePath = storage_path('app/public/' . $fileName);

                            $writer = new Writer();
                            $writer->openToFile($filePath);

                            // Tulis Header
                            $writer->addRow(Row::fromValues(['ID Biometrik', 'Nama Karyawan', 'Tanggal', 'Waktu Finger']));

                            // Tulis Data isi loop
                            foreach ($getAttendance as $log) {
                                $biometricId = $log[1] ?? null;
                                $dateTimeStr = $log[3] ?? null;

                                if ($biometricId && $dateTimeStr) {
                                    $carbonDt = Carbon::parse($dateTimeStr);
                                    $employeeName = $usersMap[$biometricId] ?? 'Tidak Terdaftar';

                                    $writer->addRow(Row::fromValues([
                                        $biometricId,
                                        $employeeName,
                                        $carbonDt->format('Y-m-d'),
                                        $carbonDt->format('H:i:s')
                                    ]));
                                }
                            }

                            $writer->close();

                            // Download file yang berhasil dibuat
                            Notification::make()->title('Log Berhasil dihapus!')->success()->send();
                            return response()->download($filePath)->deleteFileAfterSend(true);
                        } catch (\Exception $e) {
                            Notification::make()->title('Gagal terhubung ke mesin: ' . $record->name)->danger()->send();
                        }
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
