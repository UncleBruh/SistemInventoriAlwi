<x-app-layout>
    <x-slot name="header">
        {{ __('Laporan Barang Masuk') }}
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-6">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            
            <!-- Form Filter & Tombol PDF -->
            <form method="GET" action="{{ route('laporan.masuk') }}" class="mb-6 flex flex-wrap gap-4 items-end bg-gray-50 p-4 rounded-lg border border-gray-200">
                <div>
                    <x-input-label for="start_date" value="Dari Tanggal" />
                    <x-text-input id="start_date" type="date" name="start_date" value="{{ request('start_date') }}" class="block mt-1" />
                </div>
                <div>
                    <x-input-label for="end_date" value="Sampai Tanggal" />
                    <x-text-input id="end_date" type="date" name="end_date" value="{{ request('end_date') }}" class="block mt-1" />
                </div>
                <div class="flex gap-2">
                    <x-primary-button type="submit" class="bg-indigo-600 hover:bg-indigo-700">🔍 Filter</x-primary-button>
                    
                    <!-- Tombol Cetak PDF -->
                    <a href="{{ route('laporan.masuk.pdf', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 transition">
                        🖨️ Cetak PDF
                    </a>
                </div>
            </form>

<div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="py-3 px-4 border-b text-left text-sm font-bold text-gray-700">No</th>
                            <th class="py-3 px-4 border-b text-left text-sm font-bold text-gray-700">Tanggal Masuk</th>
                            <th class="py-3 px-4 border-b text-left text-sm font-bold text-gray-700">Nama Jajanan</th>
                            <th class="py-3 px-4 border-b text-left text-sm font-bold text-gray-700">Penginput</th>
                            <th class="py-3 px-4 border-b text-center text-sm font-bold text-gray-700">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($mutasiMasuk as $index => $item)
                            <tr class="hover:bg-gray-50">
                                <td class="py-2 px-4 border-b text-sm">{{ $index + 1 }}</td>
                                <td class="py-2 px-4 border-b text-sm">{{ \Carbon\Carbon::parse($item->tgl_mutasi)->format('d M Y') }}</td>
                                <td class="py-2 px-4 border-b text-sm font-medium">{{ $item->makanan->nama_makanan ?? 'Data Terhapus' }}</td>
                                <!-- Menampilkan nama User/Pengguna -->
                                <td class="py-2 px-4 border-b text-sm">{{ $item->user->name ?? $item->pengguna->name ?? 'Admin' }}</td>
                                <!-- Memperbaiki panggilan kolom jumlah -->
                                <td class="py-2 px-4 border-b text-center text-sm font-bold text-green-600">+{{ $item->jumlah ?? $item->jumlah_masuk ?? $item->jumlah_perubahan ?? 0 }} Pcs</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 px-4 text-center text-gray-500 italic">Tidak ada data barang masuk untuk periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
</x-app-layout>