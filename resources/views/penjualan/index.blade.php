<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Laporan Penjualan') }}
            </h2>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-6">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

            <form method="GET" action="{{ route('laporan.penjualan') }}" class="mb-6 flex flex-wrap gap-4 items-end bg-gray-50 p-4 rounded-lg border border-gray-200">
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

                    <a href="{{ route('laporan.penjualan.pdf', ['nama_produk' => request('nama_produk'), 'start_date' => request('start_date'), 'end_date' => request('end_date'), 'sort' => request('sort')]) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 transition">
                        🖨️ Cetak PDF
                    </a>
                </div>
            </form>

            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="py-3 px-4 border-b text-left text-sm font-bold text-gray-700">No</th>
                            <th class="py-3 px-4 border-b text-left text-sm font-bold text-gray-700">Kode Transaksi</th>
                            <th class="py-3 px-4 border-b text-left text-sm font-bold text-gray-700">Tanggal</th>
                            <th class="py-3 px-4 border-b text-left text-sm font-bold text-gray-700">Petugas</th>
                            <th class="py-3 px-4 border-b text-left text-sm font-bold text-gray-700">Detail Belanjaan</th>
                            <th class="py-3 px-4 border-b text-right text-sm font-bold text-gray-700">Total Bayar</th>
                            <th class="py-3 px-4 border-b text-center text-sm font-bold text-gray-700">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($penjualan as $index => $item)
                            <tr class="hover:bg-gray-50">
                                <td class="py-2 px-4 border-b text-sm">{{ $index + 1 }}</td>
                                <td class="py-2 px-4 border-b text-sm font-bold text-indigo-600">{{ $item->kode_transaksi ?? 'N/A' }}</td>
                                <td class="py-2 px-4 border-b text-sm">{{ \Carbon\Carbon::parse($item->tgl_penjualan)->format('d M Y') }}</td>
                                <td class="py-2 px-4 border-b text-sm">{{ $item->pengguna->name ?? $item->pengguna->username ?? 'Admin' }}</td>
                                <td class="py-2 px-4 border-b text-sm">
                                    <ul class="list-disc list-inside">
                                        @foreach($item->detail as $detail)
                                            <li>{{ $detail->makanan->nama_makanan ?? 'Produk Terhapus' }} - {{ $detail->jumlah }} pcs x Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td class="py-2 px-4 border-b text-right text-sm font-bold text-green-600">Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                                
                                <td class="py-2 px-4 border-b text-center text-sm">
                                    <a href="{{ route('retur.create', ['id_penjualan' => $item->id_penjualan]) }}" class="inline-flex items-center px-3 py-1 bg-orange-100 text-orange-700 hover:bg-orange-200 border border-orange-300 rounded-md text-xs font-bold transition">
                                        🔄 Retur
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-6 px-4 text-center text-gray-500 italic">Tidak ada data penjualan untuk periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="md:hidden space-y-3">
                @forelse ($penjualan as $item)
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <p class="font-bold text-indigo-600 text-base">{{ $item->kode_transaksi ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-600">{{ \Carbon\Carbon::parse($item->tgl_penjualan)->format('d M Y') }}</p>
                            </div>
                            <span class="text-green-600 font-bold text-lg">Rp {{ number_format($item->total_harga, 0, ',', '.') }}</span>
                        </div>

                        <div class="mb-3 pb-3 border-b border-gray-200">
                            <p class="text-xs text-gray-600 font-semibold mb-2">Petugas: {{ $item->pengguna->name ?? $item->pengguna->username ?? 'Admin' }}</p>
                            <p class="text-xs text-gray-600 font-semibold">Detail Belanjaan:</p>
                            <ul class="list-disc list-inside text-xs text-gray-600 mt-1">
                                @foreach($item->detail as $detail)
                                    <li>{{ $detail->makanan->nama_makanan ?? 'Produk Terhapus' }} - {{ $detail->jumlah }} pcs x Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</li>
                                @endforeach
                            </ul>
                        </div>
                        
                        <div class="flex justify-end">
                            <a href="{{ route('retur.create', ['id_penjualan' => $item->id_penjualan]) }}" class="inline-flex items-center px-4 py-2 bg-orange-100 text-orange-700 hover:bg-orange-200 border border-orange-300 rounded-md text-sm font-bold transition shadow-sm">
                                🔄 Proses Retur Barang
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <p class="text-gray-500">Tidak ada data penjualan untuk periode ini.</p>
                    </div>
                @endforelse
            </div>

            @if($penjualan->count() > 0)
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">📊 Total Penghasilan Per Tanggal</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead class="bg-yellow-100">
                                <tr>
                                    <th class="py-3 px-4 border-b text-left text-sm font-bold text-gray-700">Tanggal</th>
                                    <th class="py-3 px-4 border-b text-right text-sm font-bold text-gray-700">Total Penghasilan (Setelah Potong Retur)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($totalPerTanggal ?? [] as $tanggal => $total)
                                    <tr class="hover:bg-yellow-50">
                                        <td class="py-2 px-4 border-b text-sm font-medium">{{ \Carbon\Carbon::parse($tanggal)->format('d M Y (l)') }}</td>
                                        <td class="py-2 px-4 border-b text-right text-sm font-bold text-green-600">Rp {{ number_format($total, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                                <tr class="bg-gray-100 font-bold">
                                    <td class="py-3 px-4 border-b">TOTAL KESELURUHAN</td>
                                    <td class="py-3 px-4 border-b text-right text-green-600">Rp {{ number_format($penjualan->sum('total_harga'), 0, ',', '.') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>