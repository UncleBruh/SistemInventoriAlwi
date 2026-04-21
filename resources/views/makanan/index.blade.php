<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Barang (Jajanan & Minuman)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                    <h3 class="text-lg font-bold text-gray-700">Manajemen Stok</h3>
                    
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('makanan.create', ['type' => 'Makanan']) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition shadow">
                            + Tambah Makanan Baru
                        </a>
                        <a href="{{ route('makanan.create', ['type' => 'Minuman']) }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition shadow">
                            + Tambah Minuman Baru
                        </a>
                    </div>
                </div>

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border rounded-lg">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-600">Barcode</th>
                                <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-600">Nama Barang</th>
                                <th class="py-3 px-4 border-b text-center text-sm font-semibold text-gray-600">Kategori</th>
                                <th class="py-3 px-4 border-b text-right text-sm font-semibold text-gray-600">Harga</th>
                                <th class="py-3 px-4 border-b text-center text-sm font-semibold text-gray-600">Stok</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($makanan as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4 border-b text-sm">{{ $item->barcode ?? '-' }}</td>
                                <td class="py-3 px-4 border-b text-sm font-medium">{{ $item->nama_makanan }}</td>
                                <td class="py-3 px-4 border-b text-sm text-center">
                                    <span class="px-2 py-1 bg-indigo-100 text-indigo-800 text-xs rounded-full">{{ $item->jenis_makanan }}</span>
                                </td>
                                <td class="py-3 px-4 border-b text-sm text-right">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                <td class="py-3 px-4 border-b text-sm text-center font-bold {{ $item->stok < 5 ? 'text-red-500' : 'text-green-600' }}">
                                    {{ $item->stok }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-gray-500 italic">Belum ada data barang yang terdaftar.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>