<div class="flex h-screen bg-gray-100 p-6 gap-6 overflow-hidden" 
     x-data="{ 
        withWebcam: @entangle('withWebcam'),
        stream: null,
        initCamera() {
            if (!this.withWebcam) {
                this.stopCamera();
                return;
            }
            navigator.mediaDevices.getUserMedia({ video: { width: 400, height: 300 } })
                .then(s => { 
                    this.stream = s;
                    this.$refs.video.srcObject = s; 
                })
                .catch(err => {
                    console.error('Kamera gagal:', err);
                    this.withWebcam = false;
                });
        },
        stopCamera() {
            if (this.stream) {
                this.stream.getTracks().forEach(track => track.stop());
                this.stream = null;
            }
        },
        capture() {
            // Jika webcam mati, langsung proses teks saja
            if (!this.withWebcam) {
                $wire.processAttendance();
                return;
            }

            // Proses Capture Frame
            const canvas = document.createElement('canvas');
            canvas.width = 400;
            canvas.height = 300;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(this.$refs.video, 0, 0, canvas.width, canvas.height);
            
            // Konversi ke WebP (Kualitas 0.7 untuk hemat storage)
            const imageData = canvas.toDataURL('image/webp', 0.7);
            $wire.processAttendance(imageData);
        }
     }" 
     x-init="
        initCamera();
        $watch('withWebcam', value => initCamera());
        setInterval(() => { $refs.idCardInput.focus() }, 5000);
     "
     x-on:play-sound.window="new Audio('/sounds/' + $event.detail.type + '.wav').play()">

    <div class="w-1/3 flex flex-col gap-4">
        <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-200">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xs font-bold text-gray-400 uppercase tracking-widest">Input ID Card</h2>
                <button @click="
        let inputPass = prompt('Masukkan Kode Otoritas untuk mengubah status Webcam:');
        
        if (inputPass === 'admin123') { 
            withWebcam = !withWebcam;
            if(withWebcam) initCamera();
        } else if (inputPass !== null) {
            alert('Kode Salah! Akses ditolak.');
        }
    " 
    :class="withWebcam ? 'bg-green-500 ring-green-200' : 'bg-gray-300 ring-gray-100'"
    class="px-3 py-1 rounded-full text-[10px] text-white font-bold transition-all ring-4 uppercase focus:outline-none">
    Webcam: <span x-text="withWebcam ? 'On' : 'Off'"></span>
</button>
            </div>

            <div class="relative">
                <input wire:model="id_card" 
                       x-on:keydown.enter="capture()" 
                       x-ref="idCardInput"
                       @blur="$el.focus()"
                       wire:loading.attr="disabled"
                       type="text" 
                       placeholder="Scan kartu di sini..."
                       class="w-full border-2 border-gray-100 rounded-xl p-4 text-2xl font-mono focus:border-blue-500 focus:ring-0 transition-all disabled:bg-gray-50"
                       autofocus>
                
                <div wire:loading wire:target="processAttendance" class="absolute right-4 top-5">
                    <svg class="animate-spin h-6 w-6 text-blue-500" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2 mt-4">
                @foreach([
                    'checkin'  => ['label' => 'Check In', 'color' => 'peer-checked:bg-blue-600 peer-checked:border-blue-600'], 
                    'breakout' => ['label' => 'Istirahat Keluar', 'color' => 'peer-checked:bg-orange-500 peer-checked:border-orange-500'], 
                    'breakin'  => ['label' => 'Istirahat Masuk', 'color' => 'peer-checked:bg-green-600 peer-checked:border-green-600'], 
                    'checkout' => ['label' => 'Check Out', 'color' => 'peer-checked:bg-red-600 peer-checked:border-red-600']
                ] as $key => $data)
                    <label class="cursor-pointer">
                        <input type="radio" wire:model="action" value="{{ $key }}" class="sr-only peer">
                        <div class="border-2 border-gray-100 text-gray-500 p-2 rounded-lg text-center text-xs font-bold uppercase transition-all peer-checked:text-white {{ $data['color'] }}">
                            {{ $data['label'] }}
                        </div>
                    </label>
                @endforeach
            </div>

            @if (session()->has('error'))
                <div class="mt-4 p-3 bg-red-100 text-red-700 rounded-lg text-sm font-bold animate-pulse">
                    {{ session('error') }}
                </div>
            @endif
        </div>

        <div class="flex-1 bg-white rounded-2xl shadow-lg border border-gray-200 flex flex-col overflow-hidden">
            <div class="p-4 border-b border-gray-200 bg-gray-50">
                <h3 class="font-bold text-gray-700">Riwayat Hari Ini</h3>
            </div>
            <div class="flex-1 overflow-y-auto p-2">
                @foreach($history as $log)
                    <div class="flex bg-blue-50 items-center gap-4 p-3 hover:bg-blue-100 rounded-xl transition-colors border border-blue-100 mb-2">
                        <div class="w-16 h-16 bg-gray-200 rounded-full flex-shrink-0 overflow-hidden border-2 border-white shadow-sm">
                            @if($log->employee->photo)
                                <img src="{{ asset('storage/' . $log->employee->photo) }}" class="w-full h-full object-cover">
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="font-bold text-gray-800 truncate">
                            {{ $log->employee->name }}
                            @if($log->date != now()->toDateString() && $log->shift !== null)
                                <span class="text-[8px] bg-amber-200 text-amber-800 px-1.5 py-0.5 rounded-full font-black uppercase">Night</span>
                            @endif
                            </p>
                            <small class="font-bold text-gray-500 uppercase">{{ $log->employee->department->name }}</small>
                            <div class="flex flex-wrap gap-1 mt-1 text-[10px] font-mono">
                                @if($log->checkin) <span class="bg-blue-100 text-blue-600 px-1 rounded">IN: {{ substr($log->checkin, 0, 5) }}</span> @endif
                                @if($log->breakout) <span class="bg-orange-100 text-orange-600 px-1 rounded">B.OUT: {{ substr($log->breakout, 0, 5) }}</span> @endif
                                @if($log->breakin) <span class="bg-green-100 text-green-600 px-1 rounded">B.IN: {{ substr($log->breakin, 0, 5) }}</span> @endif
                                @if($log->checkout) <span class="bg-red-100 text-red-600 px-1 rounded">OUT: {{ substr($log->checkout, 0, 5) }}</span> @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="w-2/3 bg-white rounded-3xl shadow-2xl border border-gray-100 flex flex-col items-center justify-center relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-blue-50 rounded-full -mr-32 -mt-32 opacity-50"></div>
        
        <div class="absolute top-10 left-12 text-left">
            <div class="flex flex-row items-center gap-2">
                <img src="{{ asset('images/sne.png') }}" class="w-16 h-16">
                <div>
                    <h3 class="text-xl font-bold text-gray-800 uppercase tracking-tight">PT. DUNIA SOLAR INDONESIA</h3>
                    <p class="text-sm font-bold text-gray-400">Attendance Management</p>
                </div>
            </div>
        </div>

        <div class="absolute top-10 right-12 text-right" x-data="{
                time: '',
                date: '',
                updateClock() {
                    const now = new Date();
                    this.time = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false });
                    this.date = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
                }
            }" x-init="updateClock(); setInterval(() => updateClock(), 1000)">
            <h1 class="text-6xl font-black text-gray-800 font-mono tracking-tighter" x-text="time"></h1>
            <p class="text-lg text-gray-400 font-medium" x-text="date"></p>
        </div>

        @if($last_employee)
            <div class="text-center z-10 scale-110 transition-all">
                <div class="w-64 h-64 bg-gray-100 rounded-full mx-auto mb-8 border-[12px] border-white shadow-2xl overflow-hidden ring-1 ring-gray-100">
                    @if($last_employee->photo)
                        <img src="{{ asset('storage/' . $last_employee->photo) }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-300">
                            <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 20 20"><path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"></path></svg>
                        </div>
                    @endif
                </div>
                <h2 class="text-4xl font-black text-gray-800 uppercase tracking-tight">{{ $last_employee->name }}</h2>
                <div class="mt-2 space-y-2 bg-gray-50 py-4 px-8 rounded-2xl inline-block shadow-inner">
                    <p class="text-xs text-gray-400 uppercase font-bold tracking-widest">Departemen / Position</p>
                    <p class="text-xl font-bold text-blue-600 uppercase">{{ $last_employee->department->name }}</p>
                    <p class="text-lg font-bold text-gray-700">{{ $last_employee->job }}</p>
                    
                    @if (session('success'))
                        <div class="mt-4 p-3 bg-green-100 text-green-700 rounded-lg text-sm font-bold border border-green-200">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="mt-4 p-3 bg-red-100 text-red-700 rounded-lg text-sm font-bold border border-red-200">
                            {{ session('error') }}
                        </div>
                    @endif
                </div>
            </div>
        @else
            <div class="text-center opacity-20">
                <div class="w-64 h-64 bg-gray-100 rounded-full mx-auto mb-6 flex items-center justify-center border-4 border-dashed border-gray-300">
                    <svg class="w-32 h-32 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                </div>
                <p class="text-2xl font-bold text-gray-400 italic font-mono uppercase tracking-tighter">Menunggu Scan Kartu...</p>
            </div>
        @endif
    </div>

    <div x-show="withWebcam" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-10"
         class="fixed bottom-6 right-6 w-[450px] bg-black rounded-2xl shadow-2xl border-4 border-white overflow-hidden z-50">
        <video x-ref="video" autoplay playsinline class="w-full h-full object-cover aspect-video"></video>
        <div class="absolute top-2 left-2 bg-red-600 px-2 py-0.5 rounded text-[10px] text-white font-bold uppercase tracking-widest animate-pulse">
            Camera Live
        </div>
    <div class="absolute bottom-2 left-0 right-0 bg-blue-600 px-2 py-0.5 rounded text-[10px] text-white font-bold text-center uppercase tracking-widest">
     Saat absen posisikan wajah menghadap kamera
    </div>
    </div>
</div>    
