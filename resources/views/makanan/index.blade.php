<x-app-layout>
    <x-slot name="header">
        {{ __('Data Jajanan') }}
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-200">
            
            <div class="flex flex-col md:flex-row justify-between gap-4 mb-6">
                <form action="{{ route('makanan.index') }}" method="GET" class="flex flex-col md:flex-row gap-2 w-full md:w-2/3">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau barcode..." class="border-gray-300 rounded-md shadow-sm w-full md:w-1/2">
                    
                    <select name="kategori" class="border-gray-300 rounded-md shadow-sm w-full md:w-1/4">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $kat)
                            <option value="{{ $kat->id_kategori }}" {{ request('kategori') == $kat->id_kategori ? 'selected' : '' }}>
                                {{ $kat->nama_kategori }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition font-medium">Filter</button>
                    @if($search || request('kategori'))
                        <a href="{{ route('makanan.index') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200 transition text-center font-medium border border-gray-300">Reset</a>
                    @endif
                </form>

                <a href="{{ route('makanan.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-bold text-sm text-white hover:bg-indigo-700 transition shadow-sm justify-center whitespace-nowrap">
                    <span class="mr-2 text-lg">➕</span> Daftar Jajanan Baru
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">
                            <th class="p-3 border">Barcode</th>
                            <th class="p-3 border">Kategori</th>
                            <th class="p-3 border">Nama Jajanan</th>
                            <th class="p-3 border text-right">Harga</th>
                            <th class="p-3 border text-center">Stok</th>
                            <th class="p-3 border text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($makanan as $item)
                        <tr class="hover:bg-gray-50 transition border-b border-gray-200">
                            <td class="p-3 text-sm text-gray-500 font-mono">{{ $item->barcode ?? '-' }}</td>
                            <td class="p-3 text-sm font-semibold text-indigo-600">
                                {{ $item->kategori->nama_kategori ?? 'Umum' }}
                            </td>
                            <td class="p-3 font-bold text-gray-800">{{ $item->nama_makanan }}</td>
                            <td class="p-3 text-right font-medium">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                            <td class="p-3 text-center font-bold text-lg {{ $item->stok < 10 ? 'text-red-600' : 'text-green-600' }}">{{ $item->stok }}</td>
                            <td class="p-3 text-center space-x-2">
                                <a href="{{ route('makanan.edit', $item->id_makanan) }}" class="text-blue-600 hover:text-blue-800 text-xs font-bold bg-blue-50 px-3 py-1.5 rounded">Edit</a>
                                
                                <form action="{{ route('makanan.destroy', $item->id_makanan) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus jajanan ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-bold bg-red-50 px-3 py-1.5 rounded">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="p-6 text-center text-gray-500 italic">Belum ada data jajanan yang sesuai.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $makanan->links() }}
            </div>
            
        </div>
    </div>
</x-app-layout>