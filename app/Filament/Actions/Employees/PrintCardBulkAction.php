<?php

namespace App\Filament\Actions\Employees;


use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\ViewField;
use Illuminate\Database\Eloquent\Collection;

class PrintCardBulkAction
{
  public static function make(): BulkAction
  {
    return BulkAction::make('print_id_cards')
      ->label('Cetak ID Card')
      ->color('info')
      ->icon('heroicon-o-printer')
      ->form([
        Radio::make('orientation')
          ->label('Pilih Orientasi ID Card')
          ->options([
            'potrait' => 'Potrait',
            'landscape' => 'Landscape',
          ])
          ->default('potrait') // Orientasi default
          ->required(),
        ViewField::make('preview')
          ->view('forms.components.card-preview')
      ])
      ->action(function (Collection $records, array $data) {
        $orientation = $data['orientation']; // Ambil pilihan orientasi
  
        // Kumpulkan data karyawan yang dipilih
        $employees = $records->map(function ($record) {

          return [
            'fullname' => $record->name,
            'idcard' => $record->idcard,
            'image' => $record->photo,
            'posisi' => $record->job,
            'date_of_join' => $record->join_date,
            'department' => $record->department->name,
            'employee_id' => $record->id,
          ];
        })->toArray();

        // Tentukan template berdasarkan orientasi
        $templateView = 'cards.' . $orientation; // Contoh: 'idcards.template_potrait' atau 'idcards.template_landscape'
  
        try {
          $path = public_path() . '/images/sne.png';
          $type = pathinfo($path, PATHINFO_EXTENSION);
          $data = file_get_contents($path);
          $logo = 'data:image/' . $type . ';base64,' . base64_encode($data);
          $pdf = Pdf::loadView($templateView, [
            'employees' => $employees,
            'logo' => $logo
          ]);
          return response()->stream(function () use ($pdf, $orientation) {
            echo $pdf->stream('id_cards_' . $orientation . '.pdf');
          }, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="id_cards_' . $orientation . '.pdf"',
          ]);
        } catch (\Exception $e) {
          // Tangani error jika view tidak ditemukan atau ada masalah rendering PDF
          \Filament\Notifications\Notification::make()
            ->title('Gagal mencetak ID Card')
            ->body('Terjadi kesalahan: ' . $e->getMessage())
            ->danger()
            ->send();
          return; // Hentikan eksekusi action
        }
      });
  }
}
