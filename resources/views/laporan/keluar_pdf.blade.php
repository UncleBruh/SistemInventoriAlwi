<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Barang Keluar</title>
    <style>
        * { margin: 0; padding: 0; }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 12px;
            color: #333;
            padding: 20px;
        }

        /* Header Styles */
        .pdf-header {
            display: flex;
            align-items: center;
            gap: 15px;
            text-align: left;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #333;
        }
        .pdf-header img {
            width: 60px;
            height: 60px;
            object-fit: contain;
        }
        .pdf-header-text h1 {
            margin: 0;
            font-size: 20px;
            font-weight: bold;
            color: #1e3a8a;
        }
        .pdf-header-text p {
            margin: 3px 0;
            font-size: 11px;
            color: #555;
        }

        /* Report Title and Info */
        .report-title {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        .report-title h2 {
            margin: 0 0 5px 0;
            font-size: 16px;
            color: #1e3a8a;
        }
        .report-info {
            text-align: center;
            font-size: 11px;
            color: #666;
        }
        .report-info p {
            margin: 2px 0;
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px 10px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #f3f4f6;
            font-weight: bold;
            color: #374151;
            font-size: 12px;
        }
        td { font-size: 11px; }
        .text-center { text-align: center; }
        .text-red { color: #d9534f; font-weight: bold; }

        /* Signature Section */
        .signature-section {
            margin-top: 40px;
            text-align: right;
        }
        .signature-section p {
            margin: 5px 0;
            font-size: 11px;
        }

        @media print {
            body { padding: 0; }
            @page { margin: 1cm; }
        }
    </style>
</head>
<body onload="window.print()">

    <!-- Header -->
    <div class="pdf-header">
        <div>
            <img src="{{ public_path('foto/logobimbel.png') }}" alt="Logo" />
        </div>
        <div class="pdf-header-text">
            <h1>BIMBEL ALWI COLLEGE</h1>
            <p>Jalan Kebun Manggis Gang Salam 619 CD RT 04</p>
            <p>Kelurahan Kepandean Baru, Kecamatan Ilir Timur</p>
            <p>📞 0899-4432-225</p>
        </div>
    </div>

    <!-- Report Title -->
    <div class="report-title">
        <h2>LAPORAN BARANG KELUAR</h2>
    </div>

    <!-- Report Info -->
    <div class="report-info">
        @if(request('start_date') && request('end_date'))
            <p>Periode: {{ \Carbon\Carbon::parse(request('start_date'))->format('d M Y') }} s/d {{ \Carbon\Carbon::parse(request('end_date'))->format('d M Y') }}</p>
        @else
            <p>Periode: Semua Waktu</p>
        @endif
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->format('d M Y, H:i') }} WIB</p>
    </div>

    <!-- Data Table -->
    <table>
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="15%">Tanggal</th>
                <th width="30%">Nama Jajanan</th>
                <th width="15%">Alasan</th>
                <th width="20%">Petugas</th>
                <th width="15%" class="text-center">Jumlah Keluar</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($mutasiKeluar as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tgl_mutasi)->format('d M Y') }}</td>
                    <td>{{ $item->makanan->nama_makanan ?? '-' }}</td>
                    <td>{{ $item->alasan ?? '-' }}</td>
                    <td>{{ $item->pengguna->name ?? $item->pengguna->username ?? 'Admin' }}</td>
                    <td class="text-center text-red">-{{ $item->jumlah_keluar ?? 0 }} Pcs</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 20px;">Tidak ada riwayat data transaksi barang keluar.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Signature Section -->
    <div class="signature-section">
        <p>Mengetahui,</p>
        <br><br><br>
        <p><strong>( ______________________ )</strong></p>
        <p>Pemilik Bimbel Alwi College</p>
    </div>

</body>
</html>
        </tbody>
    </table>

</body>
</html>
