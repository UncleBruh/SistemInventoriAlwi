<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Penjualan') }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6 text-gray-900">

                    <div class="mb-4 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
                        <h3 class="text-lg font-bold text-gray-700">Daftar Transaksi Kasir</h3>
                        <a href="{{ route('penjualan.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded shadow text-center">
                            + Tambah Transaksi
                        </a>
                    </div>

                    <!-- Mobile Card View -->
                    <div class="sm:hidden space-y-3">
                        @forelse ($data as $item)
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <p class="text-xs font-semibold text-indigo-700">{{ $item->kode_transaksi ?? 'N/A' }}</p>
                                        <p class="text-xs text-gray-600">{{ \Carbon\Carbon::parse($item->tgl_penjualan)->format('d M Y') }}</p>
                                    </div>
                                    <p class="text-sm font-bold text-green-600 text-right">
                                        Rp {{ number_format($item->total_harga, 0, ',', '.') }}
                                    </p>
                                </div>
                                <p class="text-xs text-gray-700 mb-2"><strong>Kasir:</strong> {{ $item->pengguna->name ?? $item->pengguna->username ?? 'Admin' }}</p>
                                <div class="text-xs text-gray-700">
                                    <strong>Barang:</strong>
                                    @if($item->detail && $item->detail->count() > 0)
                                        <ul class="list-disc list-inside mt-1">
                                            @foreach($item->detail as $det)
                                                <li>{{ $det->makanan->nama_makanan ?? 'Terhapus' }} ({{ $det->jumlah }}x)</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <span class="text-gray-400 italic">Data format lama</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-gray-500 italic py-6">Belum ada data transaksi.</div>
                        @endforelse
                    </div>

                    <!-- Desktop Table View -->
                    <div class="hidden sm:block overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200 text-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="py-3 px-4 border-b text-left font-bold text-gray-700">Kode Transaksi</th>
                                    <th class="py-3 px-4 border-b text-left font-bold text-gray-700">No. Nota</th>
                                    <th class="py-3 px-4 border-b text-left font-bold text-gray-700">Tanggal</th>
                                    <th class="py-3 px-4 border-b text-left font-bold text-gray-700">Kasir</th>
                                    <th class="py-3 px-4 border-b text-left font-bold text-gray-700">Detail Belanjaan</th>
                                    <th class="py-3 px-4 border-b text-right font-bold text-gray-700">Total Harga</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data as $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-3 px-4 border-b font-bold text-indigo-700">{{ $item->kode_transaksi ?? 'N/A' }}</td>
                                        <td class="py-3 px-4 border-b font-semibold text-indigo-600">{{ $item->no_nota ?? 'LAMA-' . $item->id_penjualan }}</td>
                                        <td class="py-3 px-4 border-b">{{ \Carbon\Carbon::parse($item->tgl_penjualan)->format('d M Y') }}</td>
                                        <td class="py-3 px-4 border-b">{{ $item->pengguna->name ?? $item->pengguna->username ?? 'Admin' }}</td>

                                        <!-- Menampilkan Daftar Barang dari Relasi Detail -->
                                        <td class="py-3 px-4 border-b">
                                            @if($item->detail && $item->detail->count() > 0)
                                                <ul class="list-disc list-inside">
                                                    @foreach($item->detail as $det)
                                                        <li>{{ $det->makanan->nama_makanan ?? 'Terhapus' }} <span class="font-semibold">({{ $det->jumlah }}x @ Rp {{ number_format($det->harga_satuan, 0, ',', '.') }})</span></li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <span class="text-gray-400 italic">Data format lama / Kosong</span>
                                            @endif
                                        </td>

                                        <td class="py-3 px-4 border-b text-right font-bold text-green-600">
                                            Rp {{ number_format($item->total_harga, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-6 px-4 text-center text-gray-500 italic">Belum ada data transaksi.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
