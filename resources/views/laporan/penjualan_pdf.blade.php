<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan</title>
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
        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .total-row th {
            background-color: #e5e7eb;
            font-size: 12px;
            padding: 12px 10px;
        }
        .total-amount {
            font-weight: bold;
            color: #166534;
            font-size: 13px;
        }

        ul.detail-list {
            margin: 0;
            padding-left: 15px;
            font-size: 10px;
        }
        ul.detail-list li {
            margin-bottom: 2px;
        }

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
            @page { margin: 1cm; size: landscape; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="pdf-header">
        <div>
            <img src="file://{{ public_path('foto/logobimbel.png') }}" alt="Logo" />
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
        <h2>LAPORAN PENJUALAN</h2>
    </div>

    <!-- Report Info -->
    <div class="report-info">
        <p>
            <strong>Periode:</strong>
            {{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->format('d M Y') : 'Semua Transaksi' }}
            s/d
            {{ request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->format('d M Y') : 'Hari Ini' }}
        </p>
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->format('d M Y, H:i') }} WIB</p>
    </div>

    <!-- Data Table -->
    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 5%;">No</th>
                <th style="width: 15%;">No. Nota</th>
                <th style="width: 15%;">Waktu</th>
                <th style="width: 15%;">Kasir</th>
                <th style="width: 35%;">Detail Jajanan</th>
                <th class="text-right" style="width: 15%;">Total Belanja</th>
            </tr>
        </thead>
        <tbody>
            @forelse($penjualan as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $item->no_nota ?? '-' }}</strong><br>
                        <span style="font-size: 10px; color: #666;">{{ $item->kode_transaksi ?? '' }}</span>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal_penjualan)->format('d-m-Y H:i') }}</td>
                    <td>{{ $item->pengguna->name ?? $item->pengguna->username ?? 'Unknown' }}</td>
                    <td>
                        @if($item->detail && $item->detail->count() > 0)
                            <ul class="detail-list">
                                @foreach($item->detail as $det)
                                    <li>
                                        <strong>{{ $det->makanan->nama_makanan ?? 'Terhapus' }}</strong>
                                        ({{ $det->jumlah }}x @ Rp {{ number_format($det->harga_satuan, 0, ',', '.') }})
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <span style="color: #9ca3af; font-style: italic;">Data format lama / Kosong</span>
                        @endif
                    </td>
                    <td class="text-right"><strong>Rp {{ number_format($item->total_harga, 0, ',', '.') }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 20px;">Tidak ada transaksi pada periode tanggal ini.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <th colspan="5" class="text-right">TOTAL PENDAPATAN BERSIH :</th>
                <th class="text-right total-amount">Rp {{ number_format($total_pendapatan, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
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

</body>
</html>
