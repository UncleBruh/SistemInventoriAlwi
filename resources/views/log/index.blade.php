<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Log Aktivitas Mutasi Barang') }}
        </h2>
    </x-slot>

    <div class="py-6 flex-1 h-full">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <!-- Filter Section -->
                <div class="mb-3 bg-white rounded-lg border border-gray-200 p-4 sm:p-6">
                    <form action="{{ route('log.aktivitas') }}" method="GET" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Filter Produk -->
                            <div>
                                <label for="id_makanan" class="block text-sm font-medium text-gray-700 mb-2">Pilih Produk</label>
                                <select name="id_makanan" id="id_makanan" class="searchable-select border-gray-300 rounded-md shadow-sm block w-full text-sm py-2 px-3">
                                    <option value="">Semua Produk</option>
                                    @foreach($makanan as $item)
                                        <option value="{{ $item->id_makanan }}" {{ request('id_makanan') == $item->id_makanan ? 'selected' : '' }}>
                                            {{ $item->nama_makanan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Filter Jenis Aktivitas -->
                            <div>
                                <label for="jenis_aktivitas" class="block text-sm font-medium text-gray-700 mb-2">Jenis Aktivitas</label>
                                <select name="jenis_aktivitas" id="jenis_aktivitas" class="border-gray-300 rounded-md shadow-sm block w-full text-sm py-2 px-3">
                                    <option value="semua" {{ request('jenis_aktivitas', 'semua') == 'semua' ? 'selected' : '' }}>Semua Aktivitas</option>
                                    <option value="masuk" {{ request('jenis_aktivitas') == 'masuk' ? 'selected' : '' }}>➕ Barang Masuk</option>
                                    <option value="keluar" {{ request('jenis_aktivitas') == 'keluar' ? 'selected' : '' }}>➖ Barang Keluar</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="tgl_mulai" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                                <input type="date" name="tgl_mulai" id="tgl_mulai" value="{{ request('tgl_mulai') }}" class="border-gray-300 rounded-md shadow-sm block w-full text-sm py-2 px-3" />
                            </div>
                            <div>
                                <label for="tgl_akhir" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                                <input type="date" name="tgl_akhir" id="tgl_akhir" value="{{ request('tgl_akhir') }}" class="border-gray-300 rounded-md shadow-sm block w-full text-sm py-2 px-3" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-1 gap-4">
                            <!-- Sort -->
                            <div>
                                <label for="sort" class="block text-sm font-medium text-gray-700 mb-2">Urutkan</label>
                                <select name="sort" id="sort" class="border-gray-300 rounded-md shadow-sm block w-full text-sm py-2 px-3">
                                    <option value="terbaru" {{ request('sort') == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                                    <option value="terlama" {{ request('sort') == 'terlama' ? 'selected' : '' }}>Terlama</option>
                                    <option value="jumlah_desc" {{ request('sort') == 'jumlah_desc' ? 'selected' : '' }}>Jumlah Terbanyak</option>
                                    <option value="jumlah_asc" {{ request('sort') == 'jumlah_asc' ? 'selected' : '' }}>Jumlah Tersedikit</option>
                                </select>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row gap-3 pt-2">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium text-sm transition flex-1 sm:flex-none">
                                🔍 Terapkan Filter
                            </button>
                            @if(request('id_makanan') || request('tgl_mulai') || request('tgl_akhir') || request('jenis_aktivitas') || (request('sort') && request('sort') != 'terbaru'))
                                <a href="{{ route('log.aktivitas') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium text-sm transition border border-gray-300 text-center flex-1 sm:flex-none">
                                    ↺ Reset Filter
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse whitespace-nowrap">
                        <thead>
                            <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">
                                <th class="p-3 border">Waktu Input (Sistem)</th>
                                <th class="p-3 border bg-indigo-50 text-indigo-700">Tanggal Fisik</th>
                                <th class="p-3 border">Nama Barang</th>
                                <th class="p-3 border">Aktivitas</th>
                                <th class="p-3 border text-center">Perubahan</th>
                                <th class="p-3 border">Alasan</th>
                                <th class="p-3 border">Petugas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($semua_log as $log)
                            <tr class="hover:bg-gray-50 transition border-b border-gray-200">
                                <td class="p-3 text-sm text-gray-500">{{ $log->tgl_input }}</td>

                                <td class="p-3 font-bold bg-indigo-50/30 text-indigo-900 border-x">{{ $log->tgl_aktual }}</td>

                                <td class="p-3 font-semibold">{{ $log->nama_makanan }}</td>
                                <td class="p-3">
                                    @if($log->jenis == 'Barang Masuk')
                                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-bold">Masuk</span>
                                    @else
                                        <span class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs font-bold">Keluar</span>
                                    @endif
                                </td>
                                <td class="p-3 text-center font-bold text-lg {{ $log->jenis == 'Barang Masuk' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $log->jumlah }}
                                </td>
                                <td class="p-3 text-sm italic text-gray-500 max-w-xs truncate" title="{{ $log->alasan }}">
                                    {{ $log->alasan }}
                                </td>
                                <td class="p-3 text-sm font-medium">
                                    @if($log->petugas)
                                        {{ $log->petugas }}
                                    @else
                                        <span class="text-gray-400 italic">-</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="p-6 text-center text-gray-500 italic">Belum ada data mutasi yang dicatat.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($semua_log->count() > 0)
                    <div class="mt-8 pt-6 border-t border-gray-300">
                        {{ $semua_log->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
