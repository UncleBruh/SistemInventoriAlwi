<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pengelolaan Stok Etalase</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
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
        }
        .header h2 {
            margin: 0;
            padding: 0;
            font-size: 18px;
            text-transform: uppercase;
        }
        .report-info {
            text-align: center;
            font-size: 11px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #999;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
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
<body>

    <div class="header">
        <h2>Laporan Barang Keluar</h2>
        <p>Sistem Inventori Alwi / Warung Biebie</p>
    </div>

    <!-- Data Table -->
    <table>
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="15%" class="text-left">Tanggal</th>
                <th width="30%" class="text-left">Nama Jajanan</th>
                <th width="15%" class="text-left">Alasan</th>
                <th width="20%" class="text-left">Petugas</th>
                <th width="15%" class="text-center">Jumlah Keluar</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($mutasiKeluar as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-left">{{ \Carbon\Carbon::parse($item->tgl_mutasi)->format('d M Y') }}</td>
                    <td class="text-left">{{ $item->makanan->nama_makanan ?? 'Data Terhapus' }}</td>
                    <td class="text-left">{{ ucfirst(str_replace('_', ' ', $item->tipe_keluar)) ?? '-' }}</td>
                    <td class="text-left">{{ $item->pengguna->name ?? $item->pengguna->username ?? 'Admin' }}</td>
                    <td class="text-center text-red">-{{ $item->jumlah_perubahan ?? 0 }} Pcs</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data pengelolaan stok etalase.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
