<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Data Barang') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form action="{{ route('makanan.update', $makanan->id_makanan) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="mb-4">
                        <x-input-label for="barcode" value="Scan Barcode (Opsional)" />
                        <x-text-input id="barcode" class="block mt-1 w-full bg-blue-50 focus:bg-white" type="text" name="barcode" :value="old('barcode', $makanan->barcode)" autofocus placeholder="Arahkan scanner ke sini..." />
                        <x-input-error :messages="$errors->get('barcode')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="nama_makanan" value="Nama Barang" />
                        <x-text-input id="nama_makanan" class="block mt-1 w-full" type="text" name="nama_makanan" :value="old('nama_makanan', $makanan->nama_makanan)" required />
                        <x-input-error :messages="$errors->get('nama_makanan')" class="mt-2" />
                    </div>

                    <div class="mb-4" x-data="{ isNew: false }">
                        <x-input-label for="jenis_makanan" value="Kategori Barang" />

                        <div class="flex flex-col gap-2 mt-1">
                            <select id="jenis_makanan" name="jenis_makanan"
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block w-full"
                                    x-bind:disabled="isNew">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" {{ (old('jenis_makanan', $makanan->jenis_makanan) == $cat) ? 'selected' : '' }}>
                                        {{ $cat }}
                                    </option>
                                @endforeach
                            </select>

                            <div class="flex items-center gap-2 mt-1">
                                <input type="checkbox" id="toggle_new" x-model="isNew" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <label for="toggle_new" class="text-sm text-gray-600 italic cursor-pointer">Atau ketik kategori baru?</label>
                            </div>

                            <div x-show="isNew" style="display: none;" x-transition class="mt-2">
                                <x-text-input id="jenis_makanan_baru" class="block w-full bg-yellow-50" type="text"
                                              name="jenis_makanan_baru" :value="old('jenis_makanan_baru')"
                                              placeholder="Misal: Snack Import, Minuman Dingin, dsb..." />
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('jenis_makanan')" class="mt-2" />
                        <x-input-error :messages="$errors->get('jenis_makanan_baru')" class="mt-2" />
                    </div>

                    <div class="mb-6">
                        <x-input-label for="harga" value="Harga Jual (Rp)" />
                        <x-text-input id="harga" class="block mt-1 w-full" type="number" min="0" name="harga" :value="old('harga', $makanan->harga)" required />
                        <x-input-error :messages="$errors->get('harga')" class="mt-2" />
                    </div>

                    <!-- INFO: Stok tidak bisa diedit -->
                    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded">
                        <p class="text-sm text-blue-700">
                            <strong>ℹ️ Catatan:</strong> Stok saat ini adalah <strong>{{ $makanan->stok }} pcs</strong>.
                            Untuk mengubah stok, gunakan fitur <strong>"Mutasi Stok"</strong> di menu utama.
                        </p>
                    </div>

                    <div class="flex items-center justify-end mt-4 gap-4">
                        <a href="{{ route('makanan.index') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">Batal</a>
                        <x-primary-button>
                            Simpan Perubahan
                        </x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
