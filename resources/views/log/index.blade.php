<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Semua Laporan Mutasi Barang') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="p-3 border">Tanggal & Waktu</th>
                            <th class="p-3 border">Nama Barang</th>
                            <th class="p-3 border">Jenis Aktivitas</th>
                            <th class="p-3 border text-center">Perubahan Stok</th>
                            <th class="p-3 border">Alasan</th>
                            <th class="p-3 border">Petugas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($semua_log as $log)
                        <tr class="hover:bg-gray-50">
                            <td class="p-3 border">{{ $log->tgl_mutasi }}</td>
                            <td class="p-3 border font-semibold">{{ $log->nama_makanan }}</td>
                            <td class="p-3 border">
                                @if($log->jenis == 'Barang Masuk')
                                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-sm font-bold">Masuk</span>
                                @else
                                    <span class="px-2 py-1 bg-red-100 text-red-700 rounded text-sm font-bold">Keluar</span>
                                @endif
                            </td>
                            <td class="p-3 border text-center font-bold {{ $log->jenis == 'Barang Masuk' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $log->jumlah }}
                            </td>
                            <td class="p-3 border text-sm italic">{{ $log->alasan }}</td>
                            <td class="p-3 border">{{ $log->petugas }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="p-3 border text-center text-gray-500">Belum ada data mutasi yang dicatat.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>