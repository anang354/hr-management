<div class="flex flex-col items-center justify-center p-4">
  @if($data)
    <div class="grid grid-cols-2 gap-4">
      <div class="flex flex-col items-center">
        <div class="w-full py-1 text-xl text-center border rounded-sm" style="background-color: blue; color: white;">
          {{ __('attendances.fields.checkin') }}
        </div>
        <img src="{{ asset('storage/attendance/' . $data['date'] . '/' . $data['employee_id'] . '_checkin.webp') }}"
          alt="Foto Kehadiran" class="w-full h-auto rounded-2xl shadow-2xl border-1 border-white ring-1 ring-gray-200">
      </div>
      <div class="flex flex-col items-center">
        <div class="w-full mb-1 py-1 text-xl text-center border" style="background-color: red; color: white;">
          {{ __('attendances.fields.checkout') }}
        </div>
        <img src="{{ asset('storage/attendance/' . $data['date'] . '/' . $data['employee_id'] . '_checkout.webp') }}"
          alt="Foto Kehadiran" class="w-full h-auto rounded-2xl shadow-2xl border-1 border-white ring-1 ring-gray-200">
      </div>
      <div class="flex flex-col items-center">
        <div class="w-full mb-1 py-1 text-xl text-center border rounded-sm"
          style="background-color: orange; color: white;">{{ __('attendances.fields.breakout') }}</div>
        <img src="{{ asset('storage/attendance/' . $data['date'] . '/' . $data['employee_id'] . '_breakout.webp') }}"
          alt="Foto Kehadiran" class="w-full h-auto rounded-2xl shadow-2xl border-1 border-white ring-1 ring-gray-200">
      </div>
      <div class="flex flex-col items-center">
        <div class="w-full mb-1 py-1 text-xl text-center border rounded-sm"
          style="background-color: green; color: white;">{{ __('attendances.fields.breakin') }}</div>
        <img src="{{ asset('storage/attendance/' . $data['date'] . '/' . $data['employee_id'] . '_breakin.webp') }}"
          alt="Foto Kehadiran" class="w-full h-auto rounded-2xl shadow-2xl border-1 border-white ring-1 ring-gray-200">
      </div>
    </div>
  @else
    <div class="flex flex-col items-center justify-center py-12 text-gray-400">
      <x-heroicon-o-camera class="w-16 h-16 mb-4 opacity-20" />
      <p class="italic text-sm text-center">Foto tidak tersedia atau fitur webcam dinonaktifkan saat absen.</p>
    </div>
  @endif
</div>
