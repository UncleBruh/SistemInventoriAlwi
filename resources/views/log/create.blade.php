<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Keluar-Masuk Stok Jajanan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('log.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <x-input-label for="id_makanan" value="Pilih Jajanan (Bisa cari dari Barcode/Nama)" />
                        <select id="id_makanan" name="id_makanan" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" required autofocus>
                            <option value="">-- Pilih Jajanan --</option>
                            @foreach($makanan as $item)
                                <option value="{{ $item->id_makanan }}">
                                    {{ $item->barcode ? '['.$item->barcode.'] ' : '' }}{{ $item->nama_makanan }} (Sisa Stok: {{ $item->stok }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('id_makanan')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="jenis_aktivitas" value="Jenis Aktivitas" />
                        <select id="jenis_aktivitas" name="jenis_aktivitas" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full text-lg font-bold" required>
                            <option value="Barang Keluar" class="text-red-600">➖ Barang Keluar</option>
                            <option value="Barang Masuk" class="text-green-600">➕ Barang Masuk</option>
                        </select>
                        <x-input-error :messages="$errors->get('jenis_aktivitas')" class="mt-2" />
                    </div>

                    <div class="mb-6">
                        <x-input-label for="jumlah_perubahan" value="Jumlah Pcs" />
                        <x-text-input id="jumlah_perubahan" class="block mt-1 w-full text-xl font-bold" type="number" min="1" name="jumlah_perubahan" value="1" required />
                        <x-input-error :messages="$errors->get('jumlah_perubahan')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <x-primary-button class="w-full justify-center text-lg py-3">
                            Simpan Aktivitas Keluar-Masuk Barang
                        </x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>