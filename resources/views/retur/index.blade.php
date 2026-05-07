<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg sm:text-xl text-gray-800 leading-tight">{{ __('Pengelolaan Retur') }}</h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-3 sm:p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg text-sm flex items-start gap-2">
                    <span>✅</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-3 sm:p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg text-sm flex items-start gap-2">
                    <span>❌</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <div class="mb-6 bg-blue-50 p-3 sm:p-4 rounded-lg border border-blue-200 text-xs sm:text-sm">
                <p class="text-blue-700">
                    <strong>📋 Informasi:</strong> Kelola retur produk dari transaksi penjualan. Setiap retur akan mengurangi pendapatan dari transaksi tersebut.
                </p>
            </div>

            <div class="mt-6 mb-6 sm:mt-8 flex justify-center">
                <a href="{{ route('retur.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg font-medium text-sm sm:text-base shadow-sm transition">
                    ➕ Input Retur Baru
                </a>
            </div>

            <!-- Filter Section -->
            <div class="mb-6 bg-white rounded-lg border border-gray-200 p-4 sm:p-6">
                <form action="{{ route('retur.index') }}" method="GET" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Filter Produk -->
                        <div>
                            <label for="nama_produk" class="block text-sm font-medium text-gray-700 mb-2">Nama Produk</label>
                            <input type="text" name="nama_produk" id="nama_produk" value="{{ request('nama_produk') }}" placeholder="Cari nama jajanan..." class="border-gray-300 rounded-md shadow-sm block w-full text-sm py-2 px-3" />
                        </div>

                        <!-- Filter Kode Transaksi -->
                        <div>
                            <label for="kode_transaksi" class="block text-sm font-medium text-gray-700 mb-2">Kode Transaksi</label>
                            <input type="text" name="kode_transaksi" id="kode_transaksi" value="{{ request('kode_transaksi') }}" placeholder="Cari kode transaksi..." class="border-gray-300 rounded-md shadow-sm block w-full text-sm py-2 px-3" />
                        </div>
                    </div>

                    <!-- Filter Tanggal -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                            <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" class="border-gray-300 rounded-md shadow-sm block w-full text-sm py-2 px-3" />
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                            <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" class="border-gray-300 rounded-md shadow-sm block w-full text-sm py-2 px-3" />
                        </div>
                    </div>

                    <!-- Sorting -->
                    <div>
                        <label for="sort" class="block text-sm font-medium text-gray-700 mb-2">Urutkan</label>
                        <select name="sort" id="sort" class="border-gray-300 rounded-md shadow-sm block w-full text-sm py-2 px-3">
                            <option value="terbaru" {{ request('sort') == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                            <option value="terlama" {{ request('sort') == 'terlama' ? 'selected' : '' }}>Terlama</option>
                            <option value="terbanyak" {{ request('sort') == 'terbanyak' ? 'selected' : '' }}>Total Retur Terbesar</option>
                            <option value="tersedikit" {{ request('sort') == 'tersedikit' ? 'selected' : '' }}>Total Retur Terkecil</option>
                        </select>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3 pt-2">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium text-sm transition flex-1 sm:flex-none">
                            🔍 Terapkan Filter
                        </button>
                        @if(request('nama_produk') || request('kode_transaksi') || request('start_date') || request('end_date') || (request('sort') && request('sort') != 'terbaru'))
                            <a href="{{ route('retur.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium text-sm transition border border-gray-300 text-center flex-1 sm:flex-none">
                                ↺ Reset Filter
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Desktop Table View -->
            @if($retur->count() > 0)
                <div class="hidden md:block bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Tanggal Retur</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Kode Transaksi</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Produk</th>
                                    <th class="px-4 py-3 text-center font-semibold text-gray-700">Jumlah Retur</th>
                                    <th class="px-4 py-3 text-right font-semibold text-gray-700">Total Retur</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Alasan</th>
                                    <th class="px-4 py-3 text-center font-semibold text-gray-700">Petugas</th>
                                    <th class="px-4 py-3 text-center font-semibold text-gray-700">AKSI</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($retur as $item)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-3 text-sm">
                                        {{ $item->tgl_retur->format('d M Y H:i') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm font-mono">
                                        {{ $item->penjualan->kode_transaksi ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-800">
                                        {{ $item->makanan->nama_makanan ?? 'Produk Terhapus' }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center justify-center w-8 h-8 bg-red-100 text-red-800 rounded-full font-bold text-sm">
                                            {{ $item->jumlah_retur }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right font-semibold text-gray-800">
                                        Rp {{ number_format($item->total_retur, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        {{ $item->alasan_retur }}
                                    </td>
                                    <td class="px-4 py-3 text-center text-sm text-gray-600">
                                        {{ $item->pengguna->username ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-center space-x-2">
                                        <a href="{{ route('retur.show', $item->id_retur) }}" class="inline-flex items-center gap-1 bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded text-xs font-medium transition">
                                            👁️ Detail
                                        </a>
                                        @if(Auth::user()->role === 'Pemilik')
                                            <form action="{{ route('retur.destroy', $item->id_retur) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus retur ini? Data akan dikembalikan ke keadaan sebelum retur.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center gap-1 bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs font-medium transition">
                                                    🗑️ Hapus
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile Card View -->
                <div class="md:hidden grid grid-cols-1 gap-3 sm:gap-4">
                    @foreach($retur as $item)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                        <div class="mb-3 pb-3 border-b border-gray-200">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="font-bold text-gray-800">{{ $item->makanan->nama_makanan ?? 'Produk Terhapus' }}</h3>
                                <span class="inline-flex items-center justify-center w-7 h-7 bg-red-100 text-red-800 rounded-full font-bold text-xs">
                                    {{ $item->jumlah_retur }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500">{{ $item->tgl_retur->format('d M Y H:i') }}</p>
                        </div>

                        <div class="mb-3 space-y-2 text-sm">
                            <p><strong>Kode Transaksi:</strong> {{ $item->penjualan->kode_transaksi ?? '-' }}</p>
                            <p><strong>Alasan:</strong> {{ $item->alasan_retur }}</p>
                            <p><strong>Total Retur:</strong> <span class="font-bold text-red-600">Rp {{ number_format($item->total_retur, 0, ',', '.') }}</span></p>
                            <p><strong>Petugas:</strong> {{ $item->pengguna->username ?? '-' }}</p>
                            @if($item->keterangan)
                                <p><strong>Catatan:</strong> {{ $item->keterangan }}</p>
                            @endif
                        </div>

                        <div class="flex gap-2 pt-3 border-t border-gray-200">
                            <a href="{{ route('retur.show', $item->id_retur) }}" class="flex-1 text-center bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-2 rounded text-xs font-medium transition">
                                👁️ Detail
                            </a>
                            @if(Auth::user()->role === 'Pemilik')
                                <form action="{{ route('retur.destroy', $item->id_retur) }}" method="POST" class="flex-1" onsubmit="return confirm('Yakin hapus retur ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded text-xs font-medium transition">
                                        🗑️ Hapus
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($retur->count() > 0)
                    <div class="mt-8 pt-6 border-t border-gray-300">
                        {{ $retur->links() }}
                    </div>
                @endif
            @else
                <div class="bg-white rounded-lg shadow-sm p-8 sm:p-12 text-center">
                    <p class="text-3xl sm:text-4xl mb-4">📭</p>
                    <p class="text-base sm:text-lg font-medium text-gray-800 mb-2">Belum Ada Retur</p>
                    <p class="text-sm sm:text-base text-gray-600 mb-6">Catat retur produk yang dikembalikan oleh pelanggan untuk menyesuaikan pendapatan.</p>
                    <a href="{{ route('retur.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium text-sm transition">
                        ➕ Input Retur Pertama Anda
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
