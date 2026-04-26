<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Laporan Barang Keluar') }}</h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 sm:p-6">
                <div class="mb-4 bg-blue-50 p-3 sm:p-4 rounded-lg border border-blue-200 text-xs sm:text-sm">
                    <p class="text-blue-700">
                        <strong>💡 Info:</strong> Tabel ini menampilkan barang keluar dari ETALASE. Stok gudang tidak terpengaruh.
                    </p>
                </div>

                <!-- Desktop Table View -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="p-3 border text-xs uppercase font-semibold">Tanggal</th>
                                <th class="p-3 border text-xs uppercase font-semibold">Nama Barang</th>
                                <th class="p-3 border text-center text-xs uppercase font-semibold">Jumlah Keluar</th>
                                <th class="p-3 border text-center text-xs uppercase font-semibold">Tipe</th>
                                <th class="p-3 border text-xs uppercase font-semibold">Keterangan</th>
                                <th class="p-3 border text-xs uppercase font-semibold">Petugas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $row)
                            <tr class="hover:bg-gray-50 border-b">
                                <td class="p-3 text-sm">{{ $row->tgl_mutasi }}</td>
                                <td class="p-3 font-medium text-sm">{{ $row->makanan->nama_makanan }}</td>
                                <td class="p-3 text-center text-red-600 font-bold">-{{ $row->jumlah_keluar }}</td>
                                <td class="p-3 text-center">
                                    @switch($row->tipe_keluar)
                                        @case('penjualan')
                                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">💰 Penjualan</span>
                                            @break
                                        @case('rusak')
                                            <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded-full text-xs font-medium">🔨 Rusak</span>
                                            @break
                                        @case('hilang')
                                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">❓ Hilang</span>
                                            @break
                                        @case('lainnya')
                                            <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-medium">📋 Lainnya</span>
                                            @break
                                    @endswitch
                                </td>
                                <td class="p-3 text-sm italic">{{ $row->alasan ?? '-' }}</td>
                                <td class="p-3 text-sm">{{ $row->pengguna->username }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="md:hidden space-y-3">
                    @foreach($data as $row)
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <p class="font-bold text-gray-800 text-base mb-1">{{ $row->makanan->nama_makanan }}</p>
                                    <p class="text-xs text-gray-600">{{ $row->tgl_mutasi }}</p>
                                </div>
                                <span class="text-red-600 font-bold text-lg">-{{ $row->jumlah_keluar }}</span>
                            </div>

                            <div class="mb-3 pb-3 border-b border-gray-200">
                                @switch($row->tipe_keluar)
                                    @case('penjualan')
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium inline-block">💰 Penjualan</span>
                                        @break
                                    @case('rusak')
                                        <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded-full text-xs font-medium inline-block">🔨 Rusak</span>
                                        @break
                                    @case('hilang')
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium inline-block">❓ Hilang</span>
                                        @break
                                    @case('lainnya')
                                        <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-medium inline-block">📋 Lainnya</span>
                                        @break
                                @endswitch
                            </div>

                            @if($row->alasan)
                                <p class="text-xs text-gray-600 mb-2"><strong>Keterangan:</strong> {{ $row->alasan }}</p>
                            @endif

                            <p class="text-xs text-gray-600"><strong>Petugas:</strong> {{ $row->pengguna->username }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
