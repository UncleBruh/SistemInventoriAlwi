<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan - Warung Biebie</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 13px; color: #333; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 15px; }
        .header h2 { margin: 0; font-size: 24px; color: #1e3a8a; }
        .header p { margin: 5px 0 0; color: #555; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px 10px; text-align: left; vertical-align: top; }
        th { background-color: #f3f4f6; font-weight: bold; color: #374151; font-size: 14px;}
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row th { background-color: #e5e7eb; font-size: 15px; padding: 12px 10px; }
        .text-green { color: #166534; }
        ul.detail-list { margin: 0; padding-left: 15px; font-size: 12px; }
        ul.detail-list li { margin-bottom: 3px; }
        @media print {
            body { padding: 0; }
            @page { margin: 1cm; size: landscape; } /* Diubah ke lanskap agar tabel luas tidak terpotong */
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <h2>LAPORAN PENJUALAN - WARUNG BIEBIE</h2>
        <p>
            <strong>Periode:</strong>
            {{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->format('d M Y') : 'Semua Transaksi' }}
            s/d
            {{ request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->format('d M Y') : 'Hari Ini' }}
        </p>
        <p><small>Dicetak pada: {{ \Carbon\Carbon::now()->format('d M Y, H:i') }} WIB</small></p>
    </div>

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
                        <strong>{{ $item->no_nota ?? '-' }}</strong>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal_penjualan)->format('d-m-Y H:i') }}</td>
                    <td>{{ $item->pengguna->name ?? $item->pengguna->username ?? 'Unknown' }}</td>

                    <!-- Menampilkan Detail Barang -->
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
                            <span style="color: #9ca3af; font-style: italic; font-size: 12px;">Data format lama / Kosong</span>
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
                <th class="text-right text-green">Rp {{ number_format($total_pendapatan, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 50px; text-align: right;">
        <p>Mengetahui,</p>
        <br><br><br>
        <p><strong>( ______________________ )</strong></p>
        <p>Pemilik Warung Biebie</p>
    </div>

</body>
</html>
