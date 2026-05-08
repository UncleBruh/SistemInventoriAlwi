<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Barang Masuk</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 0;
            padding: 0;
            font-size: 18px;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 12px;
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
        }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-green { color: #28a745; font-weight: bold; }
    </style>
</head>
<body>

    <div class="header">
        <h2>Laporan Barang Masuk</h2>
        <p>Tanggal Cetak: {{ \Carbon\Carbon::now()->format('d M Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th class="text-left">Tanggal Masuk</th>
                <th class="text-left">Nama Jajanan</th>
                <th class="text-left">Tujuan Lokasi</th>
                <th class="text-left">Petugas</th>
                <th class="text-center">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($mutasiMasuk as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-left">{{ \Carbon\Carbon::parse($item->tgl_mutasi)->format('d M Y') }}</td>
                    <td class="text-left">{{ $item->makanan->nama_makanan ?? 'Data Terhapus' }}</td>
                    <td class="text-left">{{ ucfirst($item->lokasi_tujuan) ?? 'Gudang' }}</td>
                    <td class="text-left">{{ $item->pengguna->name ?? $item->pengguna->username ?? 'Admin' }}</td>
                    <td class="text-center text-green">+{{ $item->jumlah_masuk ?? 0 }} Pcs</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data barang masuk.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 30px; text-align: right;">
        <p>Hormat,</p>
        <p style="margin-top: 40px;">____________________</p>
    </div>

</body>
</html>
