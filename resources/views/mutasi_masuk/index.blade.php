<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Laporan Barang Masuk') }}</h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 sm:p-6">

                <!-- Desktop Table View -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="p-3 border text-xs uppercase font-semibold">Tanggal</th>
                                <th class="p-3 border text-xs uppercase font-semibold">Nama Barang</th>
                                <th class="p-3 border text-center text-xs uppercase font-semibold">Jumlah</th>
                                <th class="p-3 border text-center text-xs uppercase font-semibold">Lokasi Tujuan</th>
                                <th class="p-3 border text-xs uppercase font-semibold">Petugas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $row)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-3 text-sm">{{ $row->tgl_mutasi }}</td>
                                <td class="p-3 font-medium text-sm">{{ $row->makanan->nama_makanan }}</td>
                                <td class="p-3 text-center text-green-600 font-bold">+{{ $row->jumlah_masuk }}</td>
                                <td class="p-3 text-center">
                                    @if($row->lokasi_tujuan === 'gudang')
                                        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">📦 Gudang</span>
                                    @endif
                                </td>
                                <td class="p-3 text-sm">
                                    @if($row->pengguna)
                                        {{ $row->pengguna->username }}
                                    @else
                                        <span class="text-gray-400 italic">-</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="md:hidden space-y-3">
                    @foreach($data as $row)
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <p class="font-bold text-gray-800 text-base mb-1">{{ $row->makanan->nama_makanan }}</p>
                                    <p class="text-xs text-gray-600">{{ $row->tgl_mutasi }}</p>
                                </div>
                                <span class="text-green-600 font-bold text-lg">+{{ $row->jumlah_masuk }}</span>
                            </div>

                            <div class="mb-3 pb-3 border-b border-gray-200">
                                @if($row->lokasi_tujuan === 'gudang')
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium inline-block">📦 Gudang</span>
                                @endif
                            </div>

                            <p class="text-xs text-gray-600"><strong>Petugas:</strong>
                                @if($row->pengguna)
                                    {{ $row->pengguna->username }}
                                @else
                                    <span class="text-gray-400 italic">-</span>
                                @endif
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
