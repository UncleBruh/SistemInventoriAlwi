<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kategori Barang') }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative text-sm" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative text-sm" role="alert">
                    <strong class="font-bold">Terjadi Kesalahan!</strong>
                    <ul class="list-disc list-inside mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form Tambah Kategori -->
            <div class="p-4 sm:p-6 bg-white shadow sm:rounded-lg">
                <header class="mb-6">
                    <h2 class="text-lg font-medium text-gray-900">
                        Tambah Kategori Baru
                    </h2>
                    <p class="mt-1 text-sm text-gray-600">
                        Tambahkan nama kategori baru untuk mengelompokkan data jajanan.
                    </p>
                </header>

                <form action="{{ route('kategori.store') }}" method="POST" class="mt-6 space-y-4">
                    @csrf
                    <div>
                        <x-input-label for="nama_kategori" value="Nama Kategori" />
                        <x-text-input id="nama_kategori" name="nama_kategori" type="text" class="mt-1 block w-full text-sm" placeholder="Contoh: Makanan Ringan" required />
                    </div>

                    <div class="flex items-center gap-4 pt-4">
                        <x-primary-button>{{ __('Simpan Kategori') }}</x-primary-button>
                    </div>
                </form>
            </div>

            <!-- Daftar Kategori -->
            <div class="p-4 sm:p-6 bg-white shadow sm:rounded-lg">
                <header class="mb-6">
                    <h2 class="text-lg font-medium text-gray-900">
                        Daftar Kategori
                    </h2>
                </header>

                <!-- Desktop Table View -->
                <div class="hidden sm:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 border">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12 text-center">No</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Kategori</th>
                                <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($kategori as $index => $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-4 text-sm text-gray-500 text-center font-medium">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="px-4 py-4 text-sm font-medium text-gray-900">
                                        {{ $item->nama_kategori }}
                                    </td>
                                    <td class="px-4 py-4 text-sm font-medium text-center">
                                        <form action="{{ route('kategori.destroy', $item->id_kategori) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini? Data yang terhubung mungkin akan terpengaruh.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center px-3 py-1 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white hover:bg-red-500 transition">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-6 text-sm text-gray-500 text-center italic">
                                        Belum ada data kategori di dalam database.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="sm:hidden space-y-3">
                    @forelse ($kategori as $index => $item)
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 flex justify-between items-center">
                            <div class="flex-1">
                                <p class="text-xs text-gray-500 mb-1">Kategori #{{ $index + 1 }}</p>
                                <p class="font-bold text-gray-900 text-base">{{ $item->nama_kategori }}</p>
                            </div>
                            <form action="{{ route('kategori.destroy', $item->id_kategori) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus?');" class="ml-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white hover:bg-red-500 transition whitespace-nowrap">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    @empty
                        <div class="p-6 text-center text-gray-500 italic text-sm">
                            Belum ada data kategori di dalam database.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
