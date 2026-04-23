<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contract</title>
  <style>
    @font-face {
      font-family: 'Tinos', serif;
      font-style: normal;
      font-weight: normal;
      src: url('https://fonts.googleapis.com/css2?family=Tinos:ital,wght@0,400;0,700;1,400;1,700&display=swap') format('truetype');
    }

    body {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: "Tinos", serif;
    }

    p {
      font-size: 13px;
      margin: 3px 0;
    }

    ol,
    li {
      font-size: 13px;
      margin: 3px 0;
    }

    h5,
    h6 {
      margin: 10px 0;
    }

    table tr {
      height: 5px;
    }

    .text-end {
      text-align: right;
    }

    .bordered-table {
      font-size: 12px;
      border-collapse: collapse;
    }

    .bordered-table tr,
    .bordered-table td {
      border: 1px solid #000;
    }

    .text-bold {
      font-weight: bold;
    }

    .text-center {
      text-align: center;
    }

    .my-2 {
      margin-top: 10px;
      margin-bottom: 10px;
    }

    ul li p,
    ol li p {
      margin: 0;
      padding: 0;
    }

    .date_sign {
      width: 100%;
      text-align: right;
    }

    .kop-surat {
      width: 100%;
      padding: 10px 0;
      border-bottom: 2px solid #000;
    }

    .kop-surat img {
      float: left;
    }

    .kop-surat h1 {
      font-size: 16pt;
      font-weight: bold;
      text-transform: uppercase;
      margin: 0;
    }
  </style>
</head>

<body>
  <div class="kop-surat">
    <img src="{{ $image }}" width="72px" alt="">
    <div class="text-center">
      <h1>PT. DUNIA SOLAR INDONESIA</h1>
      <p>Jl. Kerapu No.12, Tj. Sengkuang, Kec. Batu Ampar, Kota Batam, Kepulauan Riau</p>
      <p>Telp. 082365439976 | Email : duniasolarindo@gmail.com</p>
    </div>
  </div>
  <div style="clear:both"></div>
  <div class="text-center my-2">
    <h4 style="margin: 0; text-decoration: underline;">
      {{ $data['contract_type'] === 'pkwt' ? 'PERJANJIAN KERJA WAKTU TERTENTU' : 'JOB TRAINING' }}
    </h4>
    <p>No: {{ $data['contract_number'] }}</p>
  </div>
  {!! $contractSetting->contract_template !!}
  <!-- {{ $contractSetting->contract_template }} -->
  <div class="date_sign">
    <p>Batam, {{ \Carbon\Carbon::parse($data['start_date'])->locale('id')->isoFormat('DD MMMM YYYY') }}</p>
  </div>
  <table class="m-2" style="width: 100%">
    <tr>
      <td class="text-center" style="width: 33%">
        <p>HR</p>
        <br>
        <br>
        <br>
        <br>
        <br>
        <p>{{ $data['snapshot_metadata']['signatories']['hr']['name'] }}</p>
      </td>
      <td class="text-center" style="width: 33%">
        <p>{{ $data['snapshot_metadata']['signatories']['sign1']['position'] }}</p>
        <br>
        <br>
        <br>
        <br>
        <br>
        <p>{{ $data['snapshot_metadata']['signatories']['sign1']['name'] }}</p>
      </td>
      <td class="text-center" style="width: 33%">
        <p>Karyawan</p>
        <br>
        <br>
        <br>
        <br>
        <br>
        <p>{{ $data['employee']['name'] }}</p>
      </td>
    </tr>
  </table>
</body>

</html>
