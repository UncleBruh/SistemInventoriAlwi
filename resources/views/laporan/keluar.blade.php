<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Barang Keluar') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <!-- Bagian Judul & Tombol Cetak -->
                    <div class="mb-4 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-700">Data Riwayat Barang Keluar</h3>
                        
                        <!-- Pastikan nama route() ini sesuai dengan yang ada di web.php milikmu -->
                        <a href="{{ route('laporan.keluar.pdf', request()->query()) }}" target="_blank" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded shadow">
                            🖨️ Cetak PDF
                        </a>
                    </div>

                    <!-- Tabel Data -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="py-3 px-4 border-b text-left text-sm font-bold text-gray-700">No</th>
                                    <th class="py-3 px-4 border-b text-left text-sm font-bold text-gray-700">Tanggal Keluar</th>
                                    <th class="py-3 px-4 border-b text-left text-sm font-bold text-gray-700">Nama Jajanan</th>
                                    <th class="py-3 px-4 border-b text-left text-sm font-bold text-gray-700">Alasan</th>
                                    <th class="py-3 px-4 border-b text-left text-sm font-bold text-gray-700">Penginput</th>
                                    <th class="py-3 px-4 border-b text-center text-sm font-bold text-gray-700">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($mutasiKeluar as $index => $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-2 px-4 border-b text-sm">{{ $index + 1 }}</td>
                                        <td class="py-2 px-4 border-b text-sm">{{ \Carbon\Carbon::parse($item->tgl_mutasi)->format('d M Y') }}</td>
                                        <td class="py-2 px-4 border-b text-sm font-medium">{{ $item->makanan->nama_makanan ?? 'Data Terhapus' }}</td>
                                        <td class="py-2 px-4 border-b text-sm">{{ $item->alasan ?? '-' }}</td>
                                        <td class="py-2 px-4 border-b text-sm">{{ $item->pengguna->name ?? $item->pengguna->username ?? 'Admin' }}</td>
                                        <td class="py-2 px-4 border-b text-center text-sm font-bold text-red-600">-{{ $item->jumlah_keluar ?? 0 }} Pcs</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-6 px-4 text-center text-gray-500 italic">Tidak ada data barang keluar untuk periode ini.</td>
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