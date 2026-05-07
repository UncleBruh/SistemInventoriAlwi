<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg sm:text-xl text-gray-800 leading-tight">{{ __('Alokasi Gudang ke Etalase') }}</h2>
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
                    <strong>📋 Informasi:</strong> Menampilkan riwayat alokasi barang dari gudang ke etalase. Gunakan untuk tracking stok yang telah dialokasikan.
                </p>
            </div>

            <div class="mt-6 mb-6 sm:mt-8 flex justify-center">
                <a href="{{ route('alokasi-gudang-etalase.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg font-medium text-sm sm:text-base shadow-sm transition">
                    ➕ Alokasikan Barang
                </a>
            </div>

            <!-- Filter Section -->
            <div class="mb-6 bg-white rounded-lg border border-gray-200 p-4 sm:p-6">
                <form action="{{ route('alokasi-gudang-etalase.index') }}" method="GET" class="space-y-4">
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

                    <!-- Filter Tanggal -->
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

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3 pt-2">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium text-sm transition flex-1 sm:flex-none">
                            🔍 Terapkan Filter
                        </button>
                        @if(request('id_makanan') || request('tgl_mulai') || request('tgl_akhir') || (request('sort') && request('sort') != 'terbaru'))
                            <a href="{{ route('alokasi-gudang-etalase.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium text-sm transition border border-gray-300 text-center flex-1 sm:flex-none">
                                ↺ Reset Filter
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            @if($data->count() > 0)
                <!-- Desktop Table View -->
                <div class="hidden md:block bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Produk</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Jumlah Dialokasi</th>
                                    <th colspan="2" class="px-4 py-3 text-center font-semibold text-gray-700">📦 GUDANG</th>
                                    <th colspan="2" class="px-4 py-3 text-center font-semibold text-gray-700">🏪 ETALASE</th>
                                    <th class="px-4 py-3 text-center font-semibold text-gray-700">Petugas</th>
                                    <th class="px-4 py-3 text-center font-semibold text-gray-700">Tanggal</th>
                                    <th class="px-4 py-3 text-center font-semibold text-gray-700">AKSI</th>
                                </tr>
                                <tr class="bg-gray-50 border-b border-gray-200">
                                    <th class="px-4 py-2 text-left font-medium text-gray-600 text-xs"></th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-600 text-xs"></th>
                                    <th class="px-4 py-2 text-center font-medium text-gray-600 text-xs">SEBELUM</th>
                                    <th class="px-4 py-2 text-center font-medium text-gray-600 text-xs">SESUDAH</th>
                                    <th class="px-4 py-2 text-center font-medium text-gray-600 text-xs">SEBELUM</th>
                                    <th class="px-4 py-2 text-center font-medium text-gray-600 text-xs">SESUDAH</th>
                                    <th class="px-4 py-2 text-center font-medium text-gray-600 text-xs"></th>
                                    <th class="px-4 py-2 text-center font-medium text-gray-600 text-xs"></th>
                                    <th class="px-4 py-2 text-center font-medium text-gray-600 text-xs"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($data as $row)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-3 font-medium text-gray-800">
                                        {{ $row->makanan->nama_makanan ?? 'N/A' }}
                                        @if(!$row->makanan)
                                            <span class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded ml-2">Produk tidak ada</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-800 rounded-full font-bold text-sm">
                                            {{ $row->jumlah_dialokasi }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center font-medium text-gray-700">{{ $row->stok_gudang_sebelum }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center px-2 py-1 bg-red-100 text-red-800 rounded font-semibold text-sm">
                                            {{ $row->stok_gudang_sesudah }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center font-medium text-gray-700">{{ $row->stok_etalase_sebelum }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-800 rounded font-semibold text-sm">
                                            {{ $row->stok_etalase_sesudah }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center text-sm text-gray-600">
                                        {{ $row->pengguna->username ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-center text-sm text-gray-600">
                                        {{ $row->tgl_alokasi->format('d M Y H:i') }}
                                    </td>
                                    <td class="px-4 py-3 text-center space-x-2">
                                        <a href="{{ route('alokasi-gudang-etalase.show', $row->id_alokasi) }}" class="inline-flex items-center gap-1 bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded text-xs font-medium transition">
                                            👁️ Detail
                                        </a>
                                        @if(Auth::user()->role === 'Pemilik')
                                            <form action="{{ route('alokasi-gudang-etalase.destroy', $row->id_alokasi) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan alokasi ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center gap-1 bg-orange-600 hover:bg-orange-700 text-white px-3 py-1 rounded text-xs font-medium transition">
                                                    ↩️ Batal
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
                    @foreach($data as $row)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition">
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-4 py-3 border-b border-gray-200">
                            <h3 class="font-bold text-gray-800 mb-1">{{ $row->makanan->nama_makanan ?? 'N/A' }}</h3>
                            <p class="text-xs text-gray-500">{{ $row->tgl_alokasi->format('d M Y H:i') }} • Petugas: {{ $row->pengguna->username ?? '-' }}</p>
                        </div>
                        <div class="px-4 py-4 space-y-4">
                            <!-- Gudang Section -->
                            <div class="space-y-2">
                                <p class="text-xs font-semibold text-gray-600 uppercase">📦 GUDANG</p>
                                <div class="grid grid-cols-3 gap-2 items-center">
                                    <div class="text-center">
                                        <p class="text-xs text-gray-500">Sebelum</p>
                                        <p class="text-lg font-bold text-gray-700">{{ $row->stok_gudang_sebelum }}</p>
                                    </div>
                                    <div class="text-center text-red-500">➖</div>
                                    <div class="text-center">
                                        <p class="text-xs text-gray-500">Sesudah</p>
                                        <p class="text-lg font-bold text-red-600 bg-red-50 px-2 py-1 rounded">{{ $row->stok_gudang_sesudah }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Arrow -->
                            <div class="text-center text-blue-400 text-2xl">⬇️</div>

                            <!-- Etalase Section -->
                            <div class="space-y-2">
                                <p class="text-xs font-semibold text-gray-600 uppercase">🏪 ETALASE</p>
                                <div class="grid grid-cols-3 gap-2 items-center">
                                    <div class="text-center">
                                        <p class="text-xs text-gray-500">Sebelum</p>
                                        <p class="text-lg font-bold text-gray-700">{{ $row->stok_etalase_sebelum }}</p>
                                    </div>
                                    <div class="text-center text-green-500">➕</div>
                                    <div class="text-center">
                                        <p class="text-xs text-gray-500">Sesudah</p>
                                        <p class="text-lg font-bold text-green-600 bg-green-50 px-2 py-1 rounded">{{ $row->stok_etalase_sesudah }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Info -->
                            <div class="text-xs text-gray-600 pt-2 border-t border-gray-200">
                                <p><strong>Jumlah Dialokasi:</strong> {{ $row->jumlah_dialokasi }} pcs</p>
                                @if($row->keterangan)
                                    <p><strong>Catatan:</strong> {{ $row->keterangan }}</p>
                                @endif
                            </div>

                            <!-- Actions -->
                            <div class="flex gap-2 pt-3 border-t border-gray-200">
                                <a href="{{ route('alokasi-gudang-etalase.show', $row->id_alokasi) }}" class="flex-1 text-center bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-2 rounded text-xs font-medium transition">
                                    👁️ Detail
                                </a>
                                @if(Auth::user()->role === 'Pemilik')
                                    <form action="{{ route('alokasi-gudang-etalase.destroy', $row->id_alokasi) }}" method="POST" class="flex-1" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan alokasi ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white px-3 py-2 rounded text-xs font-medium transition">
                                            ↩️ Batal
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                @if($data->count() > 0)
                    <div class="mt-8 pt-6 border-t border-gray-300">
                        {{ $data->links() }}
                    </div>
                @endif
            @else
                <div class="bg-white rounded-lg shadow-sm p-8 sm:p-12 text-center">
                    <p class="text-3xl sm:text-4xl mb-4">📭</p>
                    <p class="text-base sm:text-lg font-medium text-gray-800 mb-2">Belum Ada Alokasi</p>
                    <p class="text-sm sm:text-base text-gray-600 mb-6">Mulai alokasikan barang dari gudang ke etalase untuk mempersiapkan penjualan.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>