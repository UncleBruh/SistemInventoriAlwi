<x-app-layout>
    <x-slot name="header">
        {{ __('Data Jajanan') }}
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg shadow-sm text-sm sm:text-base">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 sm:p-6 border border-gray-200">

            <div class="flex flex-col gap-4 mb-6">
                <form action="{{ route('makanan.index') }}" method="GET" class="flex flex-col gap-3 w-full">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau barcode..."
                               class="border-gray-300 rounded-md shadow-sm w-full text-sm py-2 px-3">

                        <select name="kategori" class="border-gray-300 rounded-md shadow-sm w-full sm:w-1/3 text-sm py-2 px-3">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $kat)
                                <option value="{{ $kat->id_kategori }}" {{ request('kategori') == $kat->id_kategori ? 'selected' : '' }}>
                                    {{ $kat->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3">
                        <select name="sort" class="border-gray-300 rounded-md shadow-sm w-full sm:flex-1 text-sm py-2 px-3">
                            <option value="terbaru" {{ request('sort') == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                            <option value="terlama" {{ request('sort') == 'terlama' ? 'selected' : '' }}>Terlama</option>
                            <option value="stok_terbanyak" {{ request('sort') == 'stok_terbanyak' ? 'selected' : '' }}>Stok Total Terbanyak</option>
                            <option value="stok_sedikit" {{ request('sort') == 'stok_sedikit' ? 'selected' : '' }}>Stok Total Tersedikit</option>
                            <option value="gudang_desc" {{ request('sort') == 'gudang_desc' ? 'selected' : '' }}>Stok Gudang Terbanyak</option>
                            <option value="gudang_asc" {{ request('sort') == 'gudang_asc' ? 'selected' : '' }}>Stok Gudang Tersedikit</option>
                            <option value="etalase_desc" {{ request('sort') == 'etalase_desc' ? 'selected' : '' }}>Stok Etalase Terbanyak</option>
                            <option value="etalase_asc" {{ request('sort') == 'etalase_asc' ? 'selected' : '' }}>Stok Etalase Tersedikit</option>
                        </select>

                        <select name="filter_lokasi" class="border-gray-300 rounded-md shadow-sm w-full sm:flex-1 text-sm py-2 px-3">
                            <option value="">Semua Lokasi</option>
                            <option value="etalase" {{ request('filter_lokasi') == 'etalase' ? 'selected' : '' }}>🌟 Tersedia di Etalase</option>
                            <option value="gudang" {{ request('filter_lokasi') == 'gudang' ? 'selected' : '' }}>📦 Tersedia di Gudang</option>
                        </select>

                        <div class="flex gap-2">
                            <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition font-medium text-sm whitespace-nowrap flex-1 sm:flex-none">Filter</button>
                            @if($search || request('kategori') || request('filter_lokasi') || (request('sort') && request('sort') != 'terbaru'))
                                <a href="{{ route('makanan.index') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200 transition text-center font-medium border border-gray-300 text-sm whitespace-nowrap">Reset</a>
                            @endif
                        </div>
                    </div>
                </form>

                <a href="{{ route('makanan.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-bold text-sm text-white hover:bg-indigo-700 transition shadow-sm">
                    <span class="mr-2">➕</span> Daftar Jajanan Baru
                </a>
            </div>

            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-100 text-gray-600 text-xs uppercase tracking-wider">
                            <th class="p-3 border">Barcode</th>
                            <th class="p-3 border">Kategori</th>
                            <th class="p-3 border">Nama Jajanan</th>
                            <th class="p-3 border text-right">Harga</th>
                            <th class="p-3 border text-center">Total Stok</th>
                            <th class="p-3 border text-center">Gudang</th>
                            <th class="p-3 border text-center">Etalase</th>
                            <th class="p-3 border text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($makanan as $item)
                        <tr class="hover:bg-gray-50 transition border-b border-gray-200">
                            <td class="p-3 text-xs text-gray-500 font-mono">{{ $item->barcode ?? '-' }}</td>
                            <td class="p-3 text-sm font-semibold text-indigo-600">
                                {{ $item->kategori->nama_kategori ?? 'Umum' }}
                            </td>
                            <td class="p-3 font-bold text-gray-800 text-sm">{{ $item->nama_makanan }}</td>
                            <td class="p-3 text-right font-medium text-sm">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                            <td class="p-3 text-center font-bold {{ $item->stok < 10 ? 'text-red-600' : 'text-green-600' }}">
                                {{ $item->stok }}
                            </td>
                            <td class="p-3 text-center">
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-bold">
                                    📦 {{ $item->stok_gudang }}
                                </span>
                            </td>
                            <td class="p-3 text-center">
                                <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded-full text-xs font-bold">
                                    🏪 {{ $item->stok_etalase }}
                                </span>
                            </td>
                            <td class="p-3 text-center space-x-1">
                                <a href="{{ route('makanan.edit', $item->id_makanan) }}" class="text-blue-600 hover:text-blue-800 text-xs font-bold bg-blue-50 px-2 py-1 rounded inline-block">Edit</a>

                                <form action="{{ route('makanan.destroy', $item->id_makanan) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus jajanan ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-bold bg-red-50 px-2 py-1 rounded">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="p-6 text-center text-gray-500 italic text-sm">Belum ada data jajanan yang sesuai.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="md:hidden space-y-4">
                @forelse($makanan as $item)
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex-1">
                                <h3 class="font-bold text-base text-gray-800 mb-1">{{ $item->nama_makanan }}</h3>
                                <p class="text-xs text-indigo-600 font-semibold mb-2">{{ $item->kategori->nama_kategori ?? 'Umum' }}</p>
                                @if($item->barcode)
                                    <p class="text-xs text-gray-500 font-mono">{{ $item->barcode }}</p>
                                @endif
                            </div>
                            <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-sm font-bold whitespace-nowrap ml-2">
                                Rp {{ number_format($item->harga, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="grid grid-cols-3 gap-3 mb-4 pb-4 border-b border-gray-200">
                            <div class="text-center">
                                <p class="text-xs text-gray-600 mb-1">Total Stok</p>
                                <p class="font-bold text-lg {{ $item->stok < 10 ? 'text-red-600' : 'text-green-600' }}">{{ $item->stok }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-xs text-gray-600 mb-1">Gudang</p>
                                <p class="font-bold text-lg text-blue-600">📦 {{ $item->stok_gudang }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-xs text-gray-600 mb-1">Etalase</p>
                                <p class="font-bold text-lg text-orange-600">🏪 {{ $item->stok_etalase }}</p>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <a href="{{ route('makanan.edit', $item->id_makanan) }}" class="flex-1 text-center text-blue-600 hover:text-blue-800 text-sm font-bold bg-blue-50 px-3 py-2 rounded">Edit</a>

                            <form action="{{ route('makanan.destroy', $item->id_makanan) }}" method="POST" class="flex-1" onsubmit="return confirm('Yakin ingin menghapus jajanan ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full text-red-600 hover:text-red-800 text-sm font-bold bg-red-50 px-3 py-2 rounded">Hapus</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-gray-500 italic text-sm">Belum ada data jajanan yang sesuai.</div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $makanan->links() }}
            </div>

        </div>
    </div>
</x-app-layout>
