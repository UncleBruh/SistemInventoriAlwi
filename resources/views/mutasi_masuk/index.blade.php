<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Laporan Barang Masuk') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="p-3 border">Tanggal</th>
                            <th class="p-3 border">Nama Barang</th>
                            <th class="p-3 border text-center">Jumlah</th>
                            <th class="p-3 border text-center">Lokasi Tujuan</th>
                            <th class="p-3 border">Petugas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $row)
                        <tr>
                            <td class="p-3 border">{{ $row->tgl_mutasi }}</td>
                            <td class="p-3 border">{{ $row->makanan->nama_makanan }}</td>
                            <td class="p-3 border text-center text-green-600 font-bold">+{{ $row->jumlah_masuk }}</td>
                            <td class="p-3 border text-center">
                                @if($row->lokasi_tujuan === 'gudang')
                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">📦 Gudang</span>
                                @endif
                            </td>
                            <td class="p-3 border">{{ $row->pengguna->username }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
