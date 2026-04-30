<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Penjualan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="mb-4 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-700">Daftar Transaksi Kasir</h3>
                        <a href="{{ route('penjualan.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded shadow">
                            + Tambah Transaksi Baru
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="py-3 px-4 border-b text-left text-sm font-bold text-gray-700">No. Nota</th>
                                    <th class="py-3 px-4 border-b text-left text-sm font-bold text-gray-700">Tanggal</th>
                                    <th class="py-3 px-4 border-b text-left text-sm font-bold text-gray-700">Kasir</th>
                                    <th class="py-3 px-4 border-b text-left text-sm font-bold text-gray-700">Detail Belanjaan</th>
                                    <th class="py-3 px-4 border-b text-right text-sm font-bold text-gray-700">Total Harga</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data as $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-3 px-4 border-b text-sm font-bold text-indigo-600">{{ $item->no_nota ?? 'LAMA-' . $item->id_penjualan }}</td>
                                        <td class="py-3 px-4 border-b text-sm">{{ \Carbon\Carbon::parse($item->tgl_penjualan)->format('d M Y') }}</td>
                                        <td class="py-3 px-4 border-b text-sm">{{ $item->pengguna->name ?? $item->pengguna->username ?? 'Admin' }}</td>
                                        
                                        <!-- Menampilkan Daftar Barang dari Relasi Detail -->
                                        <td class="py-3 px-4 border-b text-sm">
                                            @if($item->detail && $item->detail->count() > 0)
                                                <ul class="list-disc list-inside">
                                                    @foreach($item->detail as $det)
                                                        <li>{{ $det->makanan->nama_makanan ?? 'Terhapus' }} <span class="font-semibold">({{ $det->jumlah }}x)</span></li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <span class="text-gray-400 italic">Data format lama / Kosong</span>
                                            @endif
                                        </td>
                                        
                                        <td class="py-3 px-4 border-b text-sm text-right font-bold text-green-600">
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