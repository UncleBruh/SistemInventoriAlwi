<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penjualan</title>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }
        .header p {
            margin: 5px 0;
            font-size: 12px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table thead {
            background-color: #f0f0f0;
        }
        table th {
            border: 1px solid #999;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            font-size: 12px;
        }
        table td {
            border: 1px solid #999;
            padding: 6px;
            font-size: 11px;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .total-row {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .summary {
            margin-top: 30px;
            border-top: 2px solid #333;
            padding-top: 15px;
        }
        .summary h3 {
            font-size: 14px;
            margin: 10px 0;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }
        .summary-table th {
            background-color: #fff9c4;
            border: 1px solid #999;
            padding: 8px;
            font-size: 12px;
            text-align: left;
            font-weight: bold;
        }
        .summary-table td {
            border: 1px solid #999;
            padding: 6px;
            font-size: 11px;
        }
        .summary-table tr:nth-child(even) {
            background-color: #fffde7;
        }
        .grand-total {
            background-color: #fff3cd !important;
            font-weight: bold;
            font-size: 13px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📊 LAPORAN PENJUALAN</h1>
        <p>Warung Biebie</p>
        <p>Tanggal Cetak: {{ \Carbon\Carbon::now()->format('d M Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Transaksi</th>
                <th>Tanggal</th>
                <th>Petugas</th>
                <th>Detail Belanjaan</th>
                <th class="text-right">Total Bayar</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($penjualan as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td><strong>{{ $item->kode_transaksi ?? 'N/A' }}</strong></td>
                    <td>{{ \Carbon\Carbon::parse($item->tgl_penjualan)->format('d M Y') }}</td>
                    <td>{{ $item->pengguna->name ?? $item->pengguna->username ?? 'Admin' }}</td>
                    <td>
                        @foreach($item->detail as $detail)
                            {{ $detail->makanan->nama_makanan ?? 'Produk Terhapus' }} - {{ $detail->jumlah }} pcs x Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}<br>
                        @endforeach
                    </td>
                    <td class="text-right"><strong>Rp {{ number_format($item->total_harga, 0, ',', '.') }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data penjualan untuk periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Total Penghasilan Per Tanggal -->
    @if($penjualan->count() > 0)
        <div class="summary">
            <h3>📊 TOTAL PENGHASILAN PER TANGGAL</h3>
            <table class="summary-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th class="text-right">Total Penghasilan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($totalPerTanggal as $tanggal => $total)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($tanggal)->format('d M Y (l)') }}</td>
                            <td class="text-right"><strong>Rp {{ number_format($total, 0, ',', '.') }}</strong></td>
                        </tr>
                    @endforeach
                    <tr class="grand-total">
                        <td><strong>TOTAL KESELURUHAN</strong></td>
                        <td class="text-right"><strong>Rp {{ number_format($penjualan->sum('total_harga'), 0, ',', '.') }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
    @endif
</body>
</html>
