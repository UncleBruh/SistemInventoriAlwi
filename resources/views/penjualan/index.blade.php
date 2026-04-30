<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('💰 Laporan & Riwayat Penjualan') }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">

            <!-- KOTAK FILTER TANGGAL & CETAK (BARU) -->
            <div class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-6 mb-6 border-l-4 border-indigo-500">
                <form action="{{ route('penjualan.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4 items-end">
                    <div class="w-full sm:w-auto">
                        <label for="tgl_awal" class="block text-sm font-bold text-gray-700">Dari Tanggal</label>
                        <input type="date" name="tgl_awal" id="tgl_awal" value="{{ $tgl_awal ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div class="w-full sm:w-auto">
                        <label for="tgl_akhir" class="block text-sm font-bold text-gray-700">Sampai Tanggal</label>
                        <input type="date" name="tgl_akhir" id="tgl_akhir" value="{{ $tgl_akhir ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div class="flex flex-wrap gap-2 w-full sm:w-auto">
                        <button type="submit" class="flex-1 sm:flex-none bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow transition">
                            🔍 Filter
                        </button>
                        <a href="{{ route('penjualan.index') }}" class="flex-1 sm:flex-none text-center bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded shadow transition">
                            Reset
                        </a>
                        <a href="{{ route('penjualan.cetak.pdf', ['tgl_awal' => $tgl_awal ?? '', 'tgl_akhir' => $tgl_akhir ?? '']) }}" target="_blank" class="w-full sm:w-auto text-center bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded shadow transition flex items-center justify-center gap-1">
                            🖨️ Cetak PDF
                        </a>
                    </div>
                </form>
            </div>

            <!-- DAFTAR TRANSAKSI (GABUNGAN ASLI MILIKMU) -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-green-500">
                <div class="p-4 sm:p-6 text-gray-900">

                    <div class="mb-4 flex flex-col sm:flex-row sm:justify-between sm:items-start sm:items-center gap-4 border-b pb-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-700">Daftar Transaksi Kasir</h3>
                            <!-- Menampilkan Total Pendapatan Sesuai Filter -->
                            <p class="text-sm font-bold text-gray-500 mt-1">Total Pendapatan: <span class="text-xl text-green-600">Rp {{ number_format($total_pendapatan ?? 0, 0, ',', '.') }}</span></p>
                        </div>
                        <a href="{{ route('penjualan.create') }}" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded shadow text-center w-full sm:w-auto">
                            + Transaksi
                        </a>
                    </div>

                    <!-- Mobile Card View (Untuk HP) -->
                    <div class="sm:hidden space-y-3">
                        @forelse ($penjualan as $item)
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <p class="text-xs font-semibold text-indigo-700"><strong>Kode:</strong> {{ $item->no_nota ?? 'LAMA-' . $item->id_penjualan }}</p>
                                        <p class="text-xs text-gray-600">{{ \Carbon\Carbon::parse($item->tanggal_penjualan)->format('d M Y, H:i') }}</p>
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

                    <!-- Desktop Table View (Untuk Laptop) -->
                    <div class="hidden sm:block overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200 text-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="py-3 px-4 border-b text-left font-bold text-gray-700">Kode Transaksi</th>
                                    <th class="py-3 px-4 border-b text-left font-bold text-gray-700">Waktu</th>
                                    <th class="py-3 px-4 border-b text-left font-bold text-gray-700">Kasir</th>
                                    <th class="py-3 px-4 border-b text-left font-bold text-gray-700">Detail Belanjaan</th>
                                    <th class="py-3 px-4 border-b text-right font-bold text-gray-700">Total Harga</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($penjualan as $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-3 px-4 border-b font-bold text-indigo-700">{{ $item->no_nota ?? 'LAMA-' . $item->id_penjualan }}</td>
                                        <td class="py-3 px-4 border-b">{{ \Carbon\Carbon::parse($item->tanggal_penjualan)->format('d M Y, H:i') }}</td>
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
                                        <td colspan="5" class="py-6 px-4 text-center text-gray-500 italic">Belum ada data transaksi.</td>
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
