<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Cetak Kartu Member</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 20px;
      background-color: #f4f4f4;
    }

    @media print {
      @page {
        size: A4 landscape;
        margin: 10mm;
      }
      .container {
        page-break-inside: avoid;
      }
      .box {
        page-break-inside: avoid;
        break-inside: avoid;
      }
    }

    .container {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      justify-content: flex-start;
    }

    .box {
      width: 85.60mm;
      height: 54mm;
      padding: 15px;
      border: 2px solid #333;
      background-color: #FFD700;
      position: relative;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      overflow: hidden;
      flex: 0 0 auto;
      margin: 12px; /* tambahkan margin 10px di semua sisi */
    }

    .logo {
      position: absolute;
      top: 10px;
      left: 10px;
      font-size: 14pt;
      font-weight: bold;
      color: #333;
    }
    .logo img {
      width: 40px;
      height: 40px;
      margin-right: 5px;
      vertical-align: middle;
    }
    .logo p {
      display: inline-block;
      margin: 0;
      font-size: 10pt;
    }

    .nama {
      margin-top: 40px;
      font-size: 18pt;
      font-weight: bold;
      color: #333;
      text-align: center;
    }

    .barcode {
      position: absolute;
      bottom: 10px;
      left: 50%;
      transform: translateX(-50%);
    }
    .barcode img {
      width: 45px;
      height: 45px;
    }
  </style>
</head>
<body>
  <div class="container">
    @foreach ($datamember as $chunk)
      @foreach ($chunk as $item)
        <div class="box">
          <!-- Logo dan Nama Perusahaan -->
          <div class="logo">
            <img src="{{ public_path($setting->path_logo) }}" alt="logo">
            <p>{{ $setting->nama_perusahaan }}</p>
          </div>
          <!-- Nama Member -->
          <div class="nama">{{ $item->nama }}</div>
          <!-- QR Code -->
          <div class="barcode">
            <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG($item->kode_member, 'QRCODE') }}" alt="QR Code">
          </div>
        </div>
      @endforeach
    @endforeach
  </div>
</body>
</html>
