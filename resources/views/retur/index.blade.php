<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Riwayat Retur Barang') }}
            </h2>
            <a href="{{ route('retur.create') }}" class="px-4 py-2 bg-indigo-600 text-white font-bold rounded-md hover:bg-indigo-700 transition">
                + Input Retur Baru
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-6">
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg font-bold">{{ session('success') }}</div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200">
                    <thead class="bg-orange-100 border-b-2 border-orange-300">
                        <tr>
                            <th class="py-3 px-4 text-left text-sm font-bold text-gray-700">Tanggal Retur</th>
                            <th class="py-3 px-4 text-left text-sm font-bold text-gray-700">Kode Transaksi (Asal)</th>
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
                                <td class="py-3 px-4 text-sm font-mono text-blue-600 font-bold">{{ $item->penjualan->kode_transaksi ?? 'Dihapus' }}</td>
                                <td class="py-3 px-4 text-sm font-bold">{{ $item->makanan->nama_makanan ?? 'Dihapus' }}</td>
                                <td class="py-3 px-4 text-center text-sm font-bold text-orange-600">+{{ $item->jumlah_retur }} Pcs</td>
                                <td class="py-3 px-4 text-sm text-gray-600 italic">{{ $item->alasan }}</td>
                                <td class="py-3 px-4 text-right text-sm font-bold text-red-600">-Rp {{ number_format($item->nominal_pengembalian, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 px-4 text-center text-gray-500 italic">Belum ada riwayat retur barang.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>