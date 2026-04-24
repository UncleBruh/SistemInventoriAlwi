<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Semua Laporan Mutasi Barang') }}
        </h2>
    </x-slot>

    <div class="py-12 flex-1 h-full">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse whitespace-nowrap">
                        <thead>
                            <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">
                                <th class="p-3 border">Waktu Input (Sistem)</th>
                                <th class="p-3 border bg-indigo-50 text-indigo-700">Tanggal Fisik</th>
                                <th class="p-3 border">Nama Barang</th>
                                <th class="p-3 border">Aktivitas</th>
                                <th class="p-3 border text-center">Perubahan</th>
                                <th class="p-3 border">Alasan</th>
                                <th class="p-3 border">Petugas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($semua_log as $log)
                            <tr class="hover:bg-gray-50 transition border-b border-gray-200">
                                <td class="p-3 text-sm text-gray-500">{{ $log->tgl_input }}</td>
                                
                                <td class="p-3 font-bold bg-indigo-50/30 text-indigo-900 border-x">{{ $log->tgl_aktual }}</td>
                                
                                <td class="p-3 font-semibold">{{ $log->nama_makanan }}</td>
                                <td class="p-3">
                                    @if($log->jenis == 'Barang Masuk')
                                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-bold">Masuk</span>
                                    @else
                                        <span class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs font-bold">Keluar</span>
                                    @endif
                                </td>
                                <td class="p-3 text-center font-bold text-lg {{ $log->jenis == 'Barang Masuk' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $log->jumlah }}
                                </td>
                                <td class="p-3 text-sm italic text-gray-500 max-w-xs truncate" title="{{ $log->alasan }}">
                                    {{ $log->alasan }}
                                </td>
                                <td class="p-3 text-sm font-medium">{{ $log->petugas }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="p-6 text-center text-gray-500 italic">Belum ada data mutasi yang dicatat.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>