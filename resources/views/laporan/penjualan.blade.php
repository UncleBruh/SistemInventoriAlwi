<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('💰 Laporan Penjualan') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- KOTAK FILTER & TOMBOL CETAK PDF -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <form method="GET" action="{{ route('laporan.penjualan') }}" class="flex flex-wrap gap-4 items-end bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <div>
                        <label for="nama_produk" class="block text-sm font-bold text-gray-700">Cari Nama Produk</label>
                        <input type="text" id="nama_produk" name="nama_produk" value="{{ request('nama_produk') }}" placeholder="Contoh: Donat, Kopi..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="start_date" class="block text-sm font-bold text-gray-700">Dari Tanggal</label>
                        <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-bold text-gray-700">Sampai Tanggal</label>
                        <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="sort" class="block text-sm font-bold text-gray-700">Sortir</label>
                        <select id="sort" name="sort" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1">
                            <option value="">Pilih...</option>
                            <option value="terbaru" @selected(request('sort') === 'terbaru')>Terbaru</option>
                            <option value="terlama" @selected(request('sort') === 'terlama')>Terlama</option>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded shadow transition">
                            🔍 Filter
                        </button>
                        <a href="{{ route('laporan.penjualan') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded shadow transition">
                            ↺ Reset
                        </a>
                        <!-- Tombol Cetak PDF -->
                        <a href="{{ route('laporan.penjualan.pdf', ['nama_produk' => request('nama_produk'), 'start_date' => request('start_date'), 'end_date' => request('end_date'), 'sort' => request('sort')]) }}" target="_blank" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded shadow transition flex items-center justify-center gap-1">
                            🖨️ Cetak PDF
                        </a>
                    </div>
                </form>
            </div>

            <!-- KOTAK TABEL DATA -->
            <div class="bg-white shadow-sm sm:rounded-lg p-6 border-t-4 border-green-500 flex flex-col">
                <div class="flex justify-between items-center mb-4 pb-4 border-b">
                    <h3 class="text-lg font-bold text-gray-700">Riwayat Transaksi</h3>
                    <div class="text-right">
                        <span class="text-sm font-bold text-gray-500 block">Total Pendapatan:</span>
                        <span class="text-2xl font-black text-green-600">Rp {{ number_format($total_pendapatan, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="py-3 px-4 border-b text-center text-sm font-bold text-gray-700">No</th>
                                <th class="py-3 px-4 border-b text-left text-sm font-bold text-gray-700">No. Nota</th>
                                <th class="py-3 px-4 border-b text-left text-sm font-bold text-gray-700">Tanggal Transaksi</th>
                                <th class="py-3 px-4 border-b text-left text-sm font-bold text-gray-700">Kasir</th>
                                <th class="py-3 px-4 border-b text-right text-sm font-bold text-gray-700">Total Belanja</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($penjualan as $index => $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="py-3 px-4 border-b text-center text-sm font-medium">{{ $index + 1 }}</td>
                                    <td class="py-3 px-4 border-b text-sm font-bold text-indigo-600">{{ $item->no_nota ?? '-' }}</td>
                                    <td class="py-3 px-4 border-b text-sm">{{ \Carbon\Carbon::parse($item->tanggal_penjualan)->format('d M Y, H:i') }} WIB</td>
                                    <td class="py-3 px-4 border-b text-sm font-medium">{{ $item->pengguna->name ?? $item->pengguna->username ?? 'Unknown' }}</td>
                                    <td class="py-3 px-4 border-b text-right text-sm font-bold text-green-600">Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-gray-500 italic">
                                        Belum ada transaksi pada rentang tanggal ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
