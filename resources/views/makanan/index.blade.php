<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Barang (Jajanan & Minuman)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                    <h3 class="text-lg font-bold text-gray-700">Manajemen Stok</h3>

                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('makanan.create', ['type' => 'Makanan']) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition shadow">
                            + Tambah Makanan Baru
                        </a>
                        <a href="{{ route('makanan.create', ['type' => 'Minuman']) }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition shadow">
                            + Tambah Minuman Baru
                        </a>
                    </div>
                </div>

                <!-- Search Filter Form - Simplified -->
                <div class="mb-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <div class="flex gap-2 items-center relative" id="searchForm">
                        <form method="GET" action="{{ route('makanan.index') }}" class="flex gap-2 items-center flex-1" id="filterForm">
                            <div class="flex-1 flex gap-2">
                                <input
                                    type="text"
                                    name="search"
                                    placeholder="Cari nama barang atau barcode..."
                                    value="{{ $search ?? '' }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >

                                <!-- Hidden inputs untuk kategori dan sort -->
                                <input type="hidden" name="kategori" id="kategoriValue" value="{{ $kategori ?? '' }}">
                                <input type="hidden" name="sort" id="sortValue" value="{{ $sort ?? 'terbaru' }}">
                            </div>

                            <!-- Icon Filter Kategori -->
                            <button type="button" id="btnFilterKategori" class="px-3 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 transition whitespace-nowrap" title="Filter Kategori">
                                🔽 Kategori
                            </button>

                            <!-- Dropdown Kategori (Hidden by default) -->
                            <div id="kategoriDropdown" class="absolute left-0 top-full mt-2 bg-white border border-gray-300 rounded-lg shadow-lg hidden z-10" style="width: 200px;">
                                <div class="p-2 max-h-64 overflow-y-auto">
                                    <button type="button" class="block w-full text-left px-3 py-2 hover:bg-blue-100 rounded kategori-option" data-value="">
                                        Semua Kategori
                                    </button>
                                    @foreach($categories as $cat)
                                        <button type="button" class="block w-full text-left px-3 py-2 hover:bg-blue-100 rounded kategori-option" data-value="{{ $cat }}">
                                            {{ $cat }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        <button
                            type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-gray-300 font-bold py-2 px-6 rounded-lg transition shadow whitespace-nowrap"
                        >
                            Cari
                        </button>
                        <!-- Reset Button (Outside form) -->
                        @if(!empty($search) || !empty($kategori))
                            <a
                                href="{{ route('makanan.index') }}"
                                class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition shadow whitespace-nowrap"
                            >
                                Reset
                            </a>
                        @endif
                    </div>
                </div>

                @if($search || $kategori)
                    <div class="mb-4 bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded flex justify-between items-center relative">
                        <div>
                            Hasil filter:
                            @if($search)
                                <strong>"{{ $search }}"</strong>
                            @endif
                            @if($kategori)
                                @if($search) & @endif
                                Kategori: <strong>{{ $kategori }}</strong>
                            @endif
                            ({{ $makanan->count() }} item ditemukan)
                        </div>

                        <!-- Icon Sort -->
                        <div class="relative">
                            <button type="button" id="btnSort" class="px-3 py-1 bg-blue-200 hover:bg-blue-300 rounded transition text-sm font-semibold" title="Urutkan">
                                ↕️ Sort
                            </button>

                            <!-- Dropdown Sort -->
                            <div id="sortDropdown" class="absolute right-0 mt-2 bg-white border border-gray-300 rounded-lg shadow-lg hidden z-20" style="width: 220px;">
                                <button type="button" class="block w-full text-left px-4 py-2 hover:bg-blue-100 rounded-t sort-option" data-value="terbaru">
                                    📅 Terbaru
                                </button>
                                <button type="button" class="block w-full text-left px-4 py-2 hover:bg-blue-100 sort-option" data-value="stok_asc">
                                    📈 Stok: Rendah ke Tinggi
                                </button>
                                <button type="button" class="block w-full text-left px-4 py-2 hover:bg-blue-100 rounded-b sort-option" data-value="stok_desc">
                                    📉 Stok: Tinggi ke Rendah
                                </button>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Show Sort even if no filter applied -->
                    <div class="mb-4 flex justify-end relative">
                        <button type="button" id="btnSort" class="px-3 py-1 bg-gray-300 hover:bg-gray-400 rounded transition text-sm font-semibold" title="Urutkan">
                            ↕️ Sort
                        </button>

                        <!-- Dropdown Sort -->
                        <div id="sortDropdown" class="absolute right-0 mt-10 bg-white border border-gray-300 rounded-lg shadow-lg hidden z-20" style="width: 220px;">
                            <button type="button" class="block w-full text-left px-4 py-2 hover:bg-blue-100 rounded-t sort-option" data-value="terbaru">
                                📅 Terbaru
                            </button>
                            <button type="button" class="block w-full text-left px-4 py-2 hover:bg-blue-100 sort-option" data-value="stok_asc">
                                📈 Stok: Rendah ke Tinggi
                            </button>
                            <button type="button" class="block w-full text-left px-4 py-2 hover:bg-blue-100 rounded-b sort-option" data-value="stok_desc">
                                📉 Stok: Tinggi ke Rendah
                            </button>
                        </div>
                    </div>
                @endif

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border rounded-lg">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-600">Barcode</th>
                                <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-600">Nama Barang</th>
                                <th class="py-3 px-4 border-b text-center text-sm font-semibold text-gray-600">Kategori</th>
                                <th class="py-3 px-4 border-b text-right text-sm font-semibold text-gray-600">Harga</th>
                                <th class="py-3 px-4 border-b text-center text-sm font-semibold text-gray-600">Stok</th>
                                <th class="py-3 px-4 border-b text-center text-sm font-semibold text-gray-600">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($makanan as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4 border-b text-sm">{{ $item->barcode ?? '-' }}</td>
                                <td class="py-3 px-4 border-b text-sm font-medium">{{ $item->nama_makanan }}</td>
                                <td class="py-3 px-4 border-b text-sm text-center">
                                    <span class="px-2 py-1 bg-indigo-100 text-indigo-800 text-xs rounded-full">{{ $item->jenis_makanan }}</span>
                                </td>
                                <td class="py-3 px-4 border-b text-sm text-right">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                <td class="py-3 px-4 border-b text-sm text-center font-bold {{ $item->stok < 5 ? 'text-red-500' : 'text-green-600' }}">
                                    {{ $item->stok }}
                                </td>
                                <td class="py-3 px-4 border-b text-center">
                                    <div class="flex gap-2 justify-center">
                                        <a href="{{ route('makanan.edit', $item->id_makanan) }}" class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded font-semibold transition">
                                            ✏️ Edit
                                        </a>
                                        <form action="{{ route('makanan.destroy', $item->id_makanan) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus barang ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-xs rounded font-semibold transition">
                                                🗑️ Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-gray-500 italic">Belum ada data barang yang terdaftar.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <script>
        // Toggle Filter Kategori Dropdown
        const btnFilterKategori = document.getElementById('btnFilterKategori');
        const kategoriDropdown = document.getElementById('kategoriDropdown');
        const kategoriOptions = document.querySelectorAll('.kategori-option');
        const kategoriValue = document.getElementById('kategoriValue');

        btnFilterKategori?.addEventListener('click', (e) => {
            e.preventDefault();
            kategoriDropdown.classList.toggle('hidden');
        });

        // Pilih kategori dari dropdown
        kategoriOptions.forEach(option => {
            option.addEventListener('click', (e) => {
                e.preventDefault();
                kategoriValue.value = option.dataset.value;
                kategoriDropdown.classList.add('hidden');
                document.getElementById('searchForm').submit();
            });
        });

        // Toggle Sort Dropdown
        const btnSort = document.getElementById('btnSort');
        const sortDropdown = document.getElementById('sortDropdown');
        const sortOptions = document.querySelectorAll('.sort-option');
        const sortValue = document.getElementById('sortValue');

        btnSort?.addEventListener('click', (e) => {
            e.preventDefault();
            sortDropdown.classList.toggle('hidden');
        });

        // Pilih sort dari dropdown
        sortOptions.forEach(option => {
            option.addEventListener('click', (e) => {
                e.preventDefault();
                sortValue.value = option.dataset.value;
                sortDropdown.classList.add('hidden');
                document.getElementById('searchForm').submit();
            });
        });

        // Close dropdowns jika klik di luar
        document.addEventListener('click', (e) => {
            if (!btnFilterKategori?.contains(e.target) && !kategoriDropdown?.contains(e.target)) {
                kategoriDropdown?.classList.add('hidden');
            }
            if (!btnSort?.contains(e.target) && !sortDropdown?.contains(e.target)) {
                sortDropdown?.classList.add('hidden');
            }
        });
    </script>
</x-app-layout>
