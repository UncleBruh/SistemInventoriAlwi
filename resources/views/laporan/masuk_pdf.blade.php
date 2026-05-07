<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Barang Masuk</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2 { margin: 0; font-size: 18px; }
        .header p { margin: 5px 0 0 0; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #333; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN BARANG MASUK</h2>
        <p>Bimbingan Belajar Alwi College</p>
        @if(request('start_date') && request('end_date'))
            <p>Periode: {{ \Carbon\Carbon::parse(request('start_date'))->format('d M Y') }} s/d {{ \Carbon\Carbon::parse(request('end_date'))->format('d M Y') }}</p>
        @else
            <p>Periode: Semua Waktu</p>
        @endif
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
                        <td>{{ \Carbon\Carbon::parse($item->tgl_mutasi)->format('d M Y') }}</td>
                        <td>{{ $item->makanan->nama_makanan ?? '-' }}</td>

                        <!-- Perbaikan 1: Menampilkan nama penginput (Coba cari username jika name tidak ada) -->
                        <td>{{ $item->pengguna->name ?? $item->pengguna->username ?? 'Admin' }}</td>

                        <!-- Perbaikan 2: Panggil variabel jumlah_masuk -->
                        <td class="text-center">+{{ $item->jumlah_masuk ?? 0 }} Pcs</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada data transaksi.</td>
                    </tr>
                @endforelse
            </tbody>
    </table>
</body>
</html>
