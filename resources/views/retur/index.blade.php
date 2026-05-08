<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Riwayat Retur Barang') }}
            </h2>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-6">
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg font-bold">{{ session('success') }}</div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <!-- Form Filter -->
            <form method="GET" action="{{ route('retur.index') }}" class="mb-6 flex flex-wrap gap-4 items-end bg-gray-50 p-4 rounded-lg border border-gray-200">
                <div>
                    <x-input-label for="nama_produk" value="Cari Nama Produk" />
                    <x-text-input id="nama_produk" type="text" name="nama_produk" value="{{ request('nama_produk') }}" placeholder="Contoh: Donat, Kopi..." class="block mt-1" />
                </div>
                <div>
                    <x-input-label for="start_date" value="Dari Tanggal" />
                    <x-text-input id="start_date" type="date" name="start_date" value="{{ request('start_date') }}" class="block mt-1" />
                </div>
                <div>
                    <x-input-label for="end_date" value="Sampai Tanggal" />
                    <x-text-input id="end_date" type="date" name="end_date" value="{{ request('end_date') }}" class="block mt-1" />
                </div>
                <div>
                    <x-input-label for="sort" value="Sortir" />
                    <select id="sort" name="sort" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1">
                        <option value="">Pilih...</option>
                        <option value="terbaru" @selected(request('sort') === 'terbaru')>Terbaru</option>
                        <option value="terlama" @selected(request('sort') === 'terlama')>Terlama</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <x-primary-button type="submit" class="bg-indigo-600 hover:bg-indigo-700">🔍 Filter</x-primary-button>
                </div>
            </form>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200">
                    <thead class="bg-orange-100 border-b-2 border-orange-300">
                        <tr>
                            <th class="py-3 px-4 text-left text-sm font-bold text-gray-700">Tanggal Retur</th>
                            <th class="py-3 px-4 text-left text-sm font-bold text-gray-700">Jajanan Diretur</th>
                            <th class="py-3 px-4 text-center text-sm font-bold text-gray-700">Jumlah</th>
                            <th class="py-3 px-4 text-left text-sm font-bold text-gray-700">Alasan</th>
                            <th class="py-3 px-4 text-right text-sm font-bold text-gray-700">Potongan Laporan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($retur as $item)
                            <tr class="hover:bg-gray-50 border-b border-gray-200">
                                <td class="py-3 px-4 text-sm">{{ \Carbon\Carbon::parse($item->tgl_retur)->format('d M Y') }}</td>
                                <td class="py-3 px-4 text-sm font-bold">{{ $item->makanan->nama_makanan ?? 'Dihapus' }}</td>
                                <td class="py-3 px-4 text-center text-sm font-bold text-orange-600">+{{ $item->jumlah_retur }} Pcs</td>
                                <td class="py-3 px-4 text-sm text-gray-600 italic">{{ $item->alasan }}</td>
                                <td class="py-3 px-4 text-right text-sm font-bold text-red-600">-Rp {{ number_format($item->nominal_pengembalian, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 px-4 text-center text-gray-500 italic">Belum ada riwayat retur barang.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($retur->count() > 0)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="flex justify-end">
                        <div class="text-right">
                            <p class="text-lg font-bold text-gray-800 mb-2">Total Potongan Laporan:</p>
                            <p class="text-3xl font-black text-red-600">-Rp {{ number_format($total_pengembalian, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
