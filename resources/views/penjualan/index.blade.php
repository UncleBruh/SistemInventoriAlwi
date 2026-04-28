<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Laporan Penjualan Per Hari') }}</h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Filter Tanggal -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 sm:p-6 mb-6">
                <form method="GET" action="{{ route('penjualan.index') }}" class="flex flex-col sm:flex-row gap-3 sm:gap-4 items-stretch sm:items-end">
                    <div class="flex-1 min-w-[150px]">
                        <label for="tanggal_mulai" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                        <input type="date" id="tanggal_mulai" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}" class="w-full px-3 sm:px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div class="flex-1 min-w-[150px]">
                        <label for="tanggal_akhir" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                        <input type="date" id="tanggal_akhir" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}" class="w-full px-3 sm:px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 sm:flex-none bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 sm:px-6 rounded-lg text-sm">
                            🔍 Filter
                        </button>

                        @if(request('tanggal_mulai') || request('tanggal_akhir'))
                            <a href="{{ route('penjualan.index') }}" class="flex-1 sm:flex-none bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded-lg text-sm text-center">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Laporan Per Hari -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 sm:p-6">
                @if($jumlahTransaksi > 0)
                    @foreach($laporanPerHari as $laporan)
                        <div class="mb-8 last:mb-0">
                            <!-- Header Per Hari -->
                            <div class="bg-gradient-to-r from-green-50 to-blue-50 border border-gray-200 rounded-lg p-4 mb-4">
                                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                                    <h4 class="text-base sm:text-lg font-bold text-gray-800">
                                        @php
                                            $hariIndo = [
                                                'Monday' => 'Senin',
                                                'Tuesday' => 'Selasa',
                                                'Wednesday' => 'Rabu',
                                                'Thursday' => 'Kamis',
                                                'Friday' => 'Jumat',
                                                'Saturday' => 'Sabtu',
                                                'Sunday' => 'Minggu'
                                            ];
                                            $hari = \Carbon\Carbon::parse($laporan['tanggal'])->format('l');
                                            $hariIndonesia = $hariIndo[$hari];
                                        @endphp
                                        {{ \Carbon\Carbon::parse($laporan['tanggal'])->format('d F Y') }} ({{ $hariIndonesia }})
                                    </h4>
                                    <div class="text-right">
                                        <span class="text-xs sm:text-sm text-gray-600 block mb-1">{{ $laporan['jumlah_unit'] }} unit</span>
                                        <div class="text-lg sm:text-xl font-bold text-green-600">Rp {{ number_format($laporan['total'], 0, ',', '.') }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Desktop Table View -->
                            <div class="hidden md:block overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="bg-gray-50 border-b">
                                            <th class="p-3 text-left text-gray-700 font-semibold text-xs uppercase">Nama Jajanan</th>
                                            <th class="p-3 text-center text-gray-700 font-semibold text-xs uppercase">Qty</th>
                                            <th class="p-3 text-right text-gray-700 font-semibold text-xs uppercase">Harga/Unit</th>
                                            <th class="p-3 text-right text-gray-700 font-semibold text-xs uppercase">Total</th>
                                            <th class="p-3 text-left text-gray-700 font-semibold text-xs uppercase">Petugas</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($laporan['items'] as $item)
                                            @php
                                                $totalItem = $item->jumlah_keluar * $item->makanan->harga;
                                            @endphp
                                            <tr class="border-b hover:bg-gray-50">
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

                            <!-- Mobile Card View -->
                            <div class="md:hidden space-y-2">
                                @foreach($laporan['items'] as $item)
                                    @php
                                        $totalItem = $item->jumlah_keluar * $item->makanan->harga;
                                    @endphp
                                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                                        <div class="flex justify-between items-start mb-2">
                                            <div>
                                                <p class="font-bold text-gray-800 text-sm">{{ $item->makanan->nama_makanan }}</p>
                                <p class="text-xs text-gray-600 hidden md:block">{{ \Carbon\Carbon::parse($item->tgl_mutasi)->format('H:i') }}</p>
                                        <div class="grid grid-cols-3 gap-2 mb-2 pb-2 border-b border-gray-200">
                                            <div class="text-center">
                                                <p class="text-xs text-gray-600 mb-0.5">Qty</p>
                                                <p class="text-sm font-bold text-blue-600">{{ $item->jumlah_keluar }}</p>
                                            </div>
                                            <div class="text-center">
                                                <p class="text-xs text-gray-600 mb-0.5">Harga/Unit</p>
                                                <p class="text-xs font-bold text-gray-700">Rp {{ number_format($item->makanan->harga, 0, ',', '.') }}</p>
                                            </div>
                                            <div class="text-center">
                                                <p class="text-xs text-gray-600 mb-0.5">Total</p>
                                                <p class="text-xs font-bold text-green-600">Rp {{ number_format($totalItem, 0, ',', '.') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-gray-500 text-base sm:text-lg text-center py-8">📊 Belum ada data penjualan. Silakan input penjualan dari menu <a href="{{ route('mutasi_keluar.create') }}" class="text-green-600 font-bold hover:underline">Barang Keluar</a> dengan alasan "Penjualan"</p>
                @endif

                @if($laporanPerHari->count() > 0)
                    <div class="mt-8 pt-6 border-t border-gray-300">
                        {{ $laporanPerHari->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
