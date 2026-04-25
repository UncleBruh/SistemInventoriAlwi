<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Laporan Penjualan Per Hari') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Filter Tanggal -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <form method="GET" action="{{ route('penjualan.index') }}" class="flex gap-4 items-end flex-wrap">
                    <div class="flex-1 min-w-[200px]">
                        <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                        <input type="date" id="tanggal_mulai" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div class="flex-1 min-w-[200px]">
                        <label for="tanggal_akhir" class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                        <input type="date" id="tanggal_akhir" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg">
                        🔍 Filter
                    </button>

                    @if(request('tanggal_mulai') || request('tanggal_akhir'))
                        <a href="{{ route('penjualan.index') }}" class="bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-6 rounded-lg">
                            ✖ Reset
                        </a>
                    @endif
                </form>
            </div>

            <!-- Tabel Laporan Per Hari -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if($jumlahTransaksi > 0)
                    @foreach($laporanPerHari as $laporan)
                        <div class="mb-8">
                            <div class="flex justify-between items-center mb-4 pb-3 border-b-2 border-gray-200">
                                <h4 class="text-lg font-bold text-gray-800">
                                    {{ \Carbon\Carbon::parse($laporan['tanggal'])->format('d F Y (l)') }}
                                </h4>
                                <div class="text-right">
                                    <span class="text-sm text-gray-600">{{ $laporan['jumlah_unit'] }} unit</span>
                                    <div class="text-xl font-bold text-green-600">Rp {{ number_format($laporan['total'], 0, ',', '.') }}</div>
                                </div>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="bg-gray-50 border-b">
                                            <th class="p-3 text-left text-gray-700 font-semibold">Jam</th>
                                            <th class="p-3 text-left text-gray-700 font-semibold">Nama Jajanan</th>
                                            <th class="p-3 text-center text-gray-700 font-semibold">Qty</th>
                                            <th class="p-3 text-right text-gray-700 font-semibold">Harga/Unit</th>
                                            <th class="p-3 text-right text-gray-700 font-semibold">Total</th>
                                            <th class="p-3 text-left text-gray-700 font-semibold">Petugas</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($laporan['items'] as $item)
                                            @php
                                                $totalItem = $item->jumlah_keluar * $item->makanan->harga;
                                            @endphp
                                            <tr class="border-b hover:bg-gray-50">
                                                <td class="p-3 text-gray-600">{{ \Carbon\Carbon::parse($item->tgl_mutasi)->format('H:i') }}</td>
                                                <td class="p-3 font-medium">{{ $item->makanan->nama_makanan }}</td>
                                                <td class="p-3 text-center text-blue-600 font-semibold">{{ $item->jumlah_keluar }}</td>
                                                <td class="p-3 text-right">Rp {{ number_format($item->makanan->harga, 0, ',', '.') }}</td>
                                                <td class="p-3 text-right font-semibold text-green-600">Rp {{ number_format($totalItem, 0, ',', '.') }}</td>
                                                <td class="p-3 text-gray-600 text-xs">{{ $item->pengguna->username ?? 'N/A' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="bg-green-50 font-bold">
                                            <td colspan="4" class="p-3 text-right text-green-900">Subtotal:</td>
                                            <td class="p-3 text-right text-green-700">Rp {{ number_format($laporan['total'], 0, ',', '.') }}</td>
                                            <td class="p-3"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-gray-500 text-lg text-center py-8">📊 Belum ada data penjualan. Silakan input penjualan dari menu <a href="{{ route('mutasi_keluar.create') }}" class="text-green-600 font-bold hover:underline">Barang Keluar</a> dengan alasan "Penjualan"</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
