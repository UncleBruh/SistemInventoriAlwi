<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Barang Keluar</title>
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
        .text-red { color: #d9534f; font-weight: bold; }
    </style>
</head>
<body>

    <div class="header">
        <h2>Laporan Barang Keluar</h2>
        <p>Sistem Inventori Alwi / Warung Biebie</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="15%" class="text-left">Tanggal</th>
                <th width="30%" class="text-left">Nama Jajanan</th>
                <th width="15%" class="text-left">Alasan</th>
                <th width="20%" class="text-left">Penginput</th>
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

</body>
</html>