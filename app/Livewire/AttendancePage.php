<?php
namespace App\Livewire;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class AttendancePage extends Component
{
    public $id_card = '';
    public $action = 'checkin';
    public $last_employee = null;
    public $withWebcam = true;

    public function processAttendance($imageData = null)
    {
        $employee = Employee::where('employee_number', $this->id_card)->first();

        if (!$employee) {
            $this->dispatch('play-sound', type: 'error');
            session()->flash('error', 'ID Card tidak terdaftar!');
            $this->id_card = '';
            return;
        }

        // SET FOTO DIKANAN: Pastikan ini terisi setiap kali kartu berhasil di-scan
        $this->last_employee = $employee;

        date_default_timezone_set('Asia/Jakarta');
        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();
        $time = now()->toTimeString();
        $photoPath = null;
        // Logika Penyimpanan Foto (WebP)
        function savePhoto($imageData, $employeeId, $date, $action, $withWebCam)
        {
            if ($withWebCam) {
                $image = str_replace('data:image/webp;base64,', '', $imageData);
                $image = str_replace(' ', '+', $image);
                $photoName = 'attendance/' . $date . '/' . $employeeId . '_' . $action . '.webp';

                Storage::disk('public')->put($photoName, base64_decode($image));
            }
        }

        // 2. Ambil record hari ini
        // $attendance = Attendance::where('employee_id', $employee->id)
        //     ->where('date', $today)
        //     ->first();
        if ($this->action === 'checkin') {
            $attendance = Attendance::where('employee_id', $employee->id)
                ->where('date', $today)
                ->first();
        } else {
            $attendance = Attendance::where('employee_id', $employee->id)
                ->whereIn('date', [$today, $yesterday])
                ->whereNull('checkout')
                ->latest('date')
                ->first();
        }

        // 3. Logika Transaksi
        if ($this->action === 'checkin') {
            if ($attendance) {
                session()->flash('error', 'Sudah Check-in hari ini!');
            } else {
                savePhoto($imageData, $employee->id, $today, $this->action, $this->withWebcam);
                Attendance::create([
                    'employee_id' => $employee->id,
                    'date' => $today,
                    'checkin' => $time,
                    'status' => 'Present',
                    'shift' => now()->hour >= 18 || now()->hour < 6 ? 'Night' : 'Day',
                ]);
                $this->dispatch('play-sound', type: 'success');
                session()->flash('success', 'Check-in Berhasil!');
            }
        } else {
            if (!$attendance) {
                $this->dispatch('play-sound', type: 'error');
                session()->flash('error', 'Belum Check-in! Silakan Check-in terlebih dahulu.');
            } else {
                savePhoto($imageData, $employee->id, $today, $this->action, $this->withWebcam);
                // Update kolom (breakout, breakin, atau checkout)
                $attendance->update([
                    $this->action => $time
                ]);
                $this->dispatch('play-sound', type: 'success');
                session()->flash('success', strtoupper($this->action) . ' Berhasil!');
            }
        }

        $this->id_card = ''; // Reset untuk scan selanjutnya
    }

    public function render()
    {
        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();
        return view('livewire.attendance-page', [
            // RIWAYAT DIKIRI: Urutkan berdasarkan 'updated_at' agar aksi terbaru (apapun itu) muncul paling atas
            'history' => Attendance::with('employee')
                ->whereNotNull('checkin')
                ->where(function ($query) use ($today, $yesterday) {
                    // Tampilkan semua yang tanggal kerjanya hari ini
                    $query->where('date', $today)
                        // ATAU yang tanggal kerjanya kemarin tapi baru saja di-update hari ini (shift malam)
                        ->orWhere(function ($q) use ($yesterday) {
                        $q->where('date', $yesterday)
                            ->where('updated_at', '>=', now()->startOfDay());
                    });
                })
                ->latest('updated_at')
                ->take(15)
                ->get()
        ])->layout('app');
    }
}
