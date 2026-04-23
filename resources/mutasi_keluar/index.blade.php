<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Laporan Barang Keluar') }}</h2>
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
                            <th class="p-3 border">Alasan</th>
                            <th class="p-3 border">Petugas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $row)
                        <tr>
                            <td class="p-3 border">{{ $row->tgl_mutasi }}</td>
                            <td class="p-3 border">{{ $row->makanan->nama_makanan }}</td>
                            <td class="p-3 border text-center text-red-600 font-bold">-{{ $row->jumlah_keluar }}</td>
                            <td class="p-3 border italic">{{ $row->alasan }}</td>
                            <td class="p-3 border">{{ $row->pengguna->username }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>