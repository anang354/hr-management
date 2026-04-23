<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>ID Card Potrait</title>
  <style>
    @page {
      size: A4 landscape;
      margin: 0;
    }

    .page-break {
      page-break-after: always;
    }

    body {
      font-family: sans-serif;
      margin: 0;
      padding: 0;
      width: 297mm;
      height: 210mm;
    }

    table.page {
      width: 100%;
      border-collapse: collapse;
    }

    td.idcell {
      width: 50%;
      height: 9.5cm;
      padding: 0.2cm;
      vertical-align: top;
    }

    .idcard {
      width: 6.5cm;
      height: 9cm;
      border: 1px solid #000;
      padding: 0.3cm;
      box-sizing: border-box;
      text-align: center;
    }

    .photo {
      width: 3cm;
      height: 4cm;
      background-color: #ccc;
      margin: 10px auto;
    }

    .biodata {
      font-size: 14px;
      font-weight: bold;
      border-top: 1px solid #000;
    }

    .biodata h2 {
      text-transform: uppercase;
      font-size: 16pt;
      margin: 0;
      padding: 5px 0;
    }

    .biodata p {
      font-size: 9pt;
      margin: 8px 0;
      text-transform: uppercase;
    }

    .page-break {
      page-break-after: always;
    }

    .header {
      width: 100%;
      display: block;
      border-bottom: 1px solid #000;
    }

    .logo {
      float: left;
    }

    .kop {
      width: auto;
      text-align: center;
    }

    .kop::after {
      clear: both;
    }

    .kop h4 {
      font-size: 9pt;
      padding: 0;
      margin: 0;
    }

    .kop h2 {
      font-size: 16pt;
      padding: 0;
      margin: 0;
    }
  </style>
</head>

<body>
  @foreach(collect($employees)->chunk(count($employees)) as $chunk)
    <table class="page">
      @for($i = 0; $i < ceil(count($employees) / 3); $i++) {{-- baris --}}
        <tr>
          @for($j = 0; $j < 3; $j++) {{-- kolom --}}
            @php
              $index = ($i * 3) + $j;
              $item = $chunk[$index] ?? null;
            @endphp
            <td class="idcell">
              @if($item)

                @php
                  $empImage = null;
                  $path = public_path() . '/storage/' . $item['image'];


                  if (!empty($item['image']) && file_exists($path) && is_file($path)) {
                    $type = pathinfo($path, PATHINFO_EXTENSION);
                    $data = file_get_contents($path);
                    $empImage = 'data:image/' . $type . ';base64,' . base64_encode($data);
                  }
                @endphp
                <div class="idcard">
                  <div class="header">
                    <div class="logo">
                      <img src="{{$logo}}" alt="" width="35">
                    </div>
                    <div class="kop">
                      <h4>PT. Dunia Solar Indonesia</h4>
                      <h2>ID CARD</h2>
                    </div>
                  </div>
                  <div class="photo">
                    <img src="{{ $empImage }}" width="113" alt="Foto">
                  </div>
                  <div class="biodata">
                    <h2>{{ $item['idcard'] }}</h2>
                    <p>department : {{$item['department']}}</p>
                    <p>name : {{ $item['fullname'] }}</p>
                    <p>Posisi : {{ $item['posisi'] }}</p>
                    <p>Date of join : {{date('d F Y', strtotime($item['date_of_join']))}}</p>
                  </div>
                </div>
              @endif
            </td>
          @endfor
        </tr>
      @endfor
    </table>

    @if (!$loop->last)
      <div class="page-break"></div>
    @endif
  @endforeach

</body>

</html>
