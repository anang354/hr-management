<?php

namespace App\Filament\Attendance\Resources\AttendanceUsers\Tables;

use App\Services\BiometricSyncService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AttendanceUsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('biometric_id')
                    ->searchable()
                    ->label(__('attendances.fields.biometric_id')),
                TextColumn::make('employee.name')
                    ->toggleable()
                    ->label(__('attendances.fields.employee')),
                TextColumn::make('display_name')
                    ->label(__('attendances.fields.display_name'))
                    ->searchable(),
                TextColumn::make('employee.department.name')
                    ->label(__('attendances.fields.department')),
                TextColumn::make('biometric_backups_count')
                ->counts('biometricBackups') // Nama fungsi relasi di model
                ->label(__('attendances.fields.registered_finger'))
                ->sortable()
                ->badge() // Opsional: agar tampil seperti lencana
                ->color(fn (int $state): string => match (true) {
                    $state === 0 => 'danger',
                    $state === 1 => 'info',
                    default => 'success',
                })
                ->icon('heroicon-o-finger-print'),
            ])
            ->filters([
                SelectFilter::make('department')
                    ->relationship('employee.department', 'name'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
                 // Tombol Sync Biometric
                //  Action::make('newSync')
                //     ->label('Sync')
                //     ->icon('heroicon-o-finger-print')
                //     ->color('info')
                //     ->requiresConfirmation()
                //     ->modalHeading('Sync Fingerprint to Devices')
                //     ->modalDescription(function ($record) {
                //         $fingerprintCount = $record->biometricBackups()->count();
                //         if ($fingerprintCount === 0) {
                //             return "User {$record->biometric_id} ({$record->display_name}) has no fingerprint templates. Please register fingerprint on device first.";
                //         }
                //         return "This will sync {$fingerprintCount} fingerprint(s) for user {$record->biometric_id} ({$record->display_name}) to all active biometric devices. Continue?";
                //     })
                //     ->modalSubmitActionLabel('Sync Now')
                //     ->visible(function ($record) {
                //         return $record->biometricBackups()->count() > 0;
                //     })
                //     ->action(function ($record) {
                //         try{
                //             $response = \Illuminate\Support\Facades\Http::timeout(30)->get("http://localhost:3000/api/sync/templates/{$record->biometric_id}");
                //             if($response->successful()) {
                //                 $apiData = $response->json();
                //                 $message = $apiData['message'] ??  'Successfull sync finger';
                //                 Notification::make()
                //                 ->title('Sync Successful')
                //                 ->body("User {$record->biometric_id} synced: {$message}")
                //                 ->success()
                //                 ->send();
                //             } else {
                //                 $apiData = $response->json();
                //                 $errorMessage = $apiData['message'] ??  'Failed sync finger';
                //                 Notification::make()
                //                 ->title('Sync Failed')
                //                 ->body("User {$record->biometric_id} synced: {$errorMessage}")
                //                 ->danger()
                //                 ->send();
                //             }
                //         } catch (\Exception $e) {
                //             Notification::make()
                //                 ->title('Sync Failed')
                //                 ->body("Could'nt connect to sync server {$e->getMessage()}")
                //                 ->danger()
                //                 ->send();
                //         }
                //     }),
                Action::make('syncBiometric')
                    ->label('Sync Fingerprint')
                    ->icon('heroicon-o-finger-print')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Sync Fingerprint to Devices')
                    ->modalDescription(function ($record) {
                        $fingerprintCount = $record->biometricBackups()->count();
                        if ($fingerprintCount === 0) {
                            return "User {$record->biometric_id} ({$record->display_name}) has no fingerprint templates. Please register fingerprint on device first.";
                        }
                        return "This will sync {$fingerprintCount} fingerprint(s) for user {$record->biometric_id} ({$record->display_name}) to all active biometric devices. Continue?";
                    })
                    ->modalSubmitActionLabel('Sync Now')
                    ->visible(function ($record) {
                        return $record->biometricBackups()->count() > 0;
                    })
                    ->action(function ($record, BiometricSyncService $syncService) {
                        $result = $syncService->syncUser($record->biometric_id, ['all']);

                        if ($result['success']) {
                            $data = $result['data'];
                            $uploaded = $data['summary']['templatesUploaded'] ?? 0;
                            $skipped = $data['summary']['templatesSkipped'] ?? 0;

                            Notification::make()
                                ->title('Sync Successful')
                                ->body("User {$record->biometric_id} synced: {$uploaded} uploaded, {$skipped} skipped")
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Sync Failed')
                                ->body($result['message'])
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // DeleteBulkAction::make(),
                ]),
            ]);
    }
}
