<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Laporan Pengeluaran Gudang') }}</h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-4">
                <a href="{{ route('pengeluaran_gudang.create') }}" class="inline-flex items-center px-4 py-2 bg-orange-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700 focus:bg-orange-700 active:bg-orange-900 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    ➕ Tambah Pengeluaran Gudang
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 sm:p-6">
                <div class="mb-4 bg-orange-50 p-3 sm:p-4 rounded-lg border border-orange-200 text-xs sm:text-sm">
                    <p class="text-orange-700">
                        <strong>💡 Info:</strong> Tabel ini menampilkan pengeluaran barang dari GUDANG (rusak, expired, digigit tikus, dll). Hanya stok gudang yang berkurang, stok etalase tetap.
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
                                <th class="p-3 border text-center text-xs uppercase font-semibold">Alasan</th>
                                <th class="p-3 border text-xs uppercase font-semibold">Keterangan</th>
                                <th class="p-3 border text-xs uppercase font-semibold">Petugas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $row)
                            <tr class="hover:bg-gray-50 border-b">
                                <td class="p-3 text-sm">{{ $row->tgl_pengeluaran }}</td>
                                <td class="p-3 font-medium text-sm">{{ $row->makanan->nama_makanan }}</td>
                                <td class="p-3 text-center text-red-600 font-bold">-{{ $row->jumlah_keluar }}</td>
                                <td class="p-3 text-center">
                                    @switch($row->alasan)
                                        @case('expired')
                                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">📅 Expired</span>
                                            @break
                                        @case('tikus')
                                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">🐭 Tikus</span>
                                            @break
                                        @case('rusak')
                                            <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded-full text-xs font-medium">🔨 Rusak</span>
                                            @break
                                        @case('lainnya')
                                            <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-medium">📋 Lainnya</span>
                                            @break
                                    @endswitch
                                </td>
                                <td class="p-3 text-sm italic">{{ $row->keterangan ?? '-' }}</td>
                                <td class="p-3 text-sm">
                                    @if($row->pengguna)
                                        {{ $row->pengguna->username }}
                                    @else
                                        <span class="text-gray-400 italic">-</span>
                                    @endif
                                </td>
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
                                    <p class="text-xs text-gray-600">{{ $row->tgl_pengeluaran }}</p>
                                </div>
                                <span class="text-red-600 font-bold text-lg">-{{ $row->jumlah_keluar }}</span>
                            </div>

                            <div class="mb-3 pb-3 border-b border-gray-200">
                                @switch($row->alasan)
                                    @case('expired')
                                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium inline-block">📅 Expired</span>
                                        @break
                                    @case('tikus')
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium inline-block">🐭 Tikus</span>
                                        @break
                                    @case('rusak')
                                        <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded-full text-xs font-medium inline-block">🔨 Rusak</span>
                                        @break
                                    @case('lainnya')
                                        <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-medium inline-block">📋 Lainnya</span>
                                        @break
                                @endswitch
                            </div>

                            @if($row->keterangan)
                                <p class="text-xs text-gray-600 mb-2"><strong>Keterangan:</strong> {{ $row->keterangan }}</p>
                            @endif

                            <p class="text-xs text-gray-600"><strong>Petugas:</strong>
                                @if($row->pengguna)
                                    {{ $row->pengguna->username }}
                                @else
                                    <span class="text-gray-400 italic">-</span>
                                @endif
                            </p>
                        </div>
                    @endforeach
                </div>

                @if(count($data) == 0)
                    <div class="text-center py-8">
                        <p class="text-gray-500">Tidak ada data pengeluaran gudang. <a href="{{ route('pengeluaran_gudang.create') }}" class="text-orange-600 hover:text-orange-700 font-semibold">Buat yang baru</a></p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
