<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('💰 Laporan Penjualan') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- KOTAK FILTER TANGGAL -->
            <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-6 border-l-4 border-indigo-500">
                <form action="{{ route('penjualan.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4 items-end">
                    <div>
                        <label for="tgl_awal" class="block text-sm font-bold text-gray-700">Dari Tanggal</label>
                        <input type="date" name="tgl_awal" id="tgl_awal" value="{{ $tgl_awal }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    </div>
                    <div>
                        <label for="tgl_akhir" class="block text-sm font-bold text-gray-700">Sampai Tanggal</label>
                        <input type="date" name="tgl_akhir" id="tgl_akhir" value="{{ $tgl_akhir }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    </div>
                    <div class="flex gap-2 w-full sm:w-auto">
                        <button type="submit" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow transition">
                            🔍 Filter
                        </button>
                        <a href="{{ route('penjualan.index') }}" class="w-full sm:w-auto text-center bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded shadow transition">
                            Reset
                        </a>
                        <!-- Tombol Cetak PDF mengirimkan data tanggal yang sedang difilter -->
                        <a href="{{ route('penjualan.cetak_pdf', ['tgl_awal' => $tgl_awal, 'tgl_akhir' => $tgl_akhir]) }}" target="_blank" class="w-full sm:w-auto text-center bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded shadow transition flex items-center justify-center gap-1">
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