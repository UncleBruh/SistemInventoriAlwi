<x-app-layout>
    <x-slot name="header">
        {{ __('Daftar Jajanan Baru') }}
    </x-slot>

    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-200">

            <form action="{{ route('makanan.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <x-input-label for="id_kategori" value="Kategori Jajanan" />
                    <select id="id_kategori" name="id_kategori" class="searchable-select border-gray-300 rounded-md shadow-sm block mt-1 w-full" required autofocus>
                        <option value=""></option>
                        @foreach($kategori as $kat)
                            <option value="{{ $kat->id_kategori }}" {{ old('id_kategori') == $kat->id_kategori ? 'selected' : '' }}>
                                {{ $kat->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('id_kategori')" class="mt-2" />
                </div>

                <div class="mb-4">
                    <x-input-label for="barcode" value="Barcode (Opsional - Bisa di-scan)" />
                    <x-text-input id="barcode" class="block mt-1 w-full font-mono text-gray-600" type="text" name="barcode" value="{{ old('barcode') }}" placeholder="Scan atau ketik barcode..." />
                    <x-input-error :messages="$errors->get('barcode')" class="mt-2" />
                </div>

                <div class="mb-4">
                    <x-input-label for="nama_makanan" value="Nama Jajanan" />
                    <x-text-input id="nama_makanan" class="block mt-1 w-full font-medium" type="text" name="nama_makanan" value="{{ old('nama_makanan') }}" required placeholder="Contoh: Qtela, Chitato, dll" />
                    <x-input-error :messages="$errors->get('nama_makanan')" class="mt-2" />
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <x-input-label for="harga" value="Harga Jual (Rp)" />
                        <x-text-input id="harga" class="block mt-1 w-full" type="number" name="harga" value="{{ old('harga') }}" required min="0" placeholder="0" />
                        <x-input-error :messages="$errors->get('harga')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="stok" value="Stok Awal (Masuk ke Gudang)" />
                        <x-text-input id="stok" class="block mt-1 w-full font-bold text-indigo-700" type="number" name="stok" value="{{ old('stok', 0) }}" required min="0" />
                        <p class="text-xs text-gray-500 mt-1">Stok awal akan masuk ke gudang, bukan langsung ke etalase.</p>
                        <x-input-error :messages="$errors->get('stok')" class="mt-2" />
                    </div>
                </div>

                <div class="flex items-center justify-between border-t pt-4 mt-2">
                    <a href="{{ route('makanan.index') }}" class="text-gray-500 hover:text-gray-800 font-medium transition">⬅ Batal</a>
                    <x-primary-button class="bg-indigo-600 hover:bg-indigo-700 text-lg px-8 shadow-sm">Simpan Jajanan</x-primary-button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
