<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>ID Card Landscape</title>
  <style>
    @page {
      size: A4 potrait;
      margin: 0;
    }

    body {
      font-family: sans-serif;
      margin: 0;
      padding: 0;
      width: 210mm;
      height: 297mm;
    }

    table.page {
      width: 100%;
      border-collapse: collapse;
    }

    td.idcell {
      width: 50%;
      height: 6.5cm;
      padding: 0.2cm;
      vertical-align: top;
    }

    .idcard {
      width: 9cm;
      height: 6cm;
      border: 1px solid #000;
      padding: 0.3cm;
      box-sizing: border-box;
      text-align: center;
    }

    .body-box {
      clear: both;
      display: block;
    }

    .photo {
      background-color: #ccc;
      margin: 10px auto;
      width: 2.5cm;
      height: 3cm;
    }

    .biodata {
      font-size: 14px;
      font-weight: bold;
      margin-left: 10px;
    }

    .biodata h2 {
      text-transform: uppercase;
    }

    .biodata p {
      font-size: 9pt;
      margin: 10px 0;
      text-transform: uppercase;
    }

    .page-break {
      page-break-after: always;
    }

    .header {
      width: 100%;
      display: block;
      border-bottom: 1px solid #000;
      padding: 10px 0;
    }

    .logo {
      float: left;
    }

    .kop {
      width: auto;
      text-align: center;
    }

    .kop h4 {
      font-size: 10pt;
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
      @for($i = 0; $i < ceil(count($employees) / 2); $i++) {{-- baris --}}
        <tr>
          @for($j = 0; $j < 2; $j++) {{-- kolom --}}
            @php
              $index = ($i * 2) + $j;
              $item = $chunk[$index] ?? null;
            @endphp
            <td class="idcell">
              @if($item)
                @php
                  $empImage = null;
                  $path = public_path('storage/' . $item['image']);

                  if (!empty($item['image']) && file_exists($path) && is_file($path)) {
                    $type = pathinfo($path, PATHINFO_EXTENSION);
                    $data = file_get_contents($path);
                    $empImage = 'data:image/' . $type . ';base64,' . base64_encode($data);
                  }
                @endphp
                <div class="idcard">
                  <div class="header">
                    <div class="logo">
                      <img src="{{$logo}}" alt="" width="50">
                    </div>
                    <div class="kop">
                      <h4>PT. Dunia Solar Indonesia</h4>
                      <h2>ID CARD</h2>
                    </div>
                  </div>
                  <table>
                    <tbody>
                      <tr>
                        <td style="vertical-align: middle">
                          <div class="photo">
                            <img src="{{ $empImage }}" width="100" alt="Foto">
                          </div>
                        </td>
                        <td>
                          <div class="biodata">
                            <h2>{{ $item['idcard'] }}</h2>
                            <p>department : {{$item['department']}}</p>
                            <p>name : {{ $item['fullname'] }}</p>
                            <p>Posisi : {{$item['posisi']}}</p>
                            <p>Date of join : {{date('d F Y', strtotime($item['date_of_join']))}}</p>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
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
