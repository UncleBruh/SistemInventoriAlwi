<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Laporan Barang Keluar') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="mb-4 bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <p class="text-sm text-blue-700">
                        <strong>💡 Informasi:</strong> Tabel ini menampilkan barang yang keluar dari ETALASE saja.
                        Stok gudang tidak terpengaruh oleh pengeluaran ini.
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="p-3 border">Tanggal</th>
                                <th class="p-3 border">Nama Barang</th>
                                <th class="p-3 border text-center">Jumlah Keluar</th>
                                <th class="p-3 border text-center">Tipe</th>
                                <th class="p-3 border">Keterangan</th>
                                <th class="p-3 border">Petugas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $row)
                            <tr class="hover:bg-gray-50">
                                <td class="p-3 border">{{ $row->tgl_mutasi }}</td>
                                <td class="p-3 border font-medium">{{ $row->makanan->nama_makanan }}</td>
                                <td class="p-3 border text-center text-red-600 font-bold">-{{ $row->jumlah_keluar }}</td>
                                <td class="p-3 border text-center">
                                    @switch($row->tipe_keluar)
                                        @case('penjualan')
                                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">💰 Penjualan</span>
                                            @break
                                        @case('rusak')
                                            <span class="px-3 py-1 bg-orange-100 text-orange-800 rounded-full text-sm font-medium">🔨 Rusak</span>
                                            @break
                                        @case('hilang')
                                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-medium">❓ Hilang</span>
                                            @break
                                        @case('lainnya')
                                            <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm font-medium">📋 Lainnya</span>
                                            @break
                                    @endswitch
                                </td>
                                <td class="p-3 border text-sm italic">{{ $row->alasan ?? '-' }}</td>
                                <td class="p-3 border">{{ $row->pengguna->username }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
