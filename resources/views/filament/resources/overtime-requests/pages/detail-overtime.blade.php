<x-filament-panels::page>
  {{-- Ini akan merender Infolist (data header) otomatis --}}
  @if ($this->hasInfolist())
    {{ $this->infolist }}
  @endif
  {{ __('overtime_request.fields.overtime_date') }} :
  {{ \Illuminate\Support\Carbon::parse($this->record->overtime_date)
  ->isoFormat('dddd, D MMMM YYYY')
    }}
  <br />
  {{ __('overtime_request.contents.submitted_by') }} : {{ $this->record->user->name }}
  <br />
  {{ __('overtime_request.fields.department') }} : {{ $this->record->user->department->name }}

  <div class="mt-8">
    {{ $this->table }}
  </div>
</x-filament-panels::page>
