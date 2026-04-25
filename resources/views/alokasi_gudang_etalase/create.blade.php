<x-app-layout>
    <x-slot name="header">
        {{ __('Alokasikan Barang dari Gudang ke Etalase') }}
    </x-slot>

    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg flex items-center">
                    ✅ {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg flex items-center">
                    ❌ {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('alokasi-gudang-etalase.store') }}" method="POST">
                @csrf

                <div class="mb-6 bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <p class="text-sm font-medium text-blue-700">
                        <strong>📦 Fungsi:</strong> Alokasikan barang dari gudang ke etalase agar bisa dijual
                    </p>
                    <p class="text-xs text-blue-600 mt-2">
                        💡 Stok gudang akan berkurang, stok etalase akan bertambah. Stok total barang tetap sama.
                    </p>
                </div>

                <div class="mb-4">
                    <x-input-label for="id_makanan" value="Pilih Jajanan dari Gudang" />
                    <select id="id_makanan" name="id_makanan" class="searchable-select border-gray-300 rounded-md shadow-sm block mt-1 w-full" required autofocus>
                        <option value="">-- Pilih Barang --</option>
                        @foreach($makanan as $item)
                            <option value="{{ $item->id_makanan }}">
                                {{ $item->nama_makanan }}
                                (Gudang: {{ $item->stok_gudang }} pcs | Etalase: {{ $item->stok_etalase }} pcs)
                            </option>
                        @endforeach
                    </select>
                    @error('id_makanan')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <x-input-label for="jumlah_dialokasi" value="Jumlah yang Dialokasikan (Pcs)" />
                    <x-text-input
                        id="jumlah_dialokasi"
                        class="block mt-1 w-full text-center text-2xl font-bold"
                        type="number"
                        min="1"
                        name="jumlah_dialokasi"
                        value="1"
                        required
                    />
                    @error('jumlah_dialokasi')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Masukkan jumlah barang yang ingin dialokasikan ke etalase</p>
                </div>

                <div class="mb-6">
                    <x-input-label for="keterangan" value="Keterangan (Opsional)" />
                    <textarea
                        id="keterangan"
                        name="keterangan"
                        rows="3"
                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full"
                        placeholder="Contoh: Persiapan penjualan weekend, restock etalase, dsb..."
                    ></textarea>
                    @error('keterangan')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-4">
                    <x-primary-button class="flex-1 justify-center py-3 text-lg bg-blue-600 hover:bg-blue-700">
                        ✅ ALOKASIKAN BARANG
                    </x-primary-button>
                    <a href="{{ route('alokasi-gudang-etalase.index') }}" class="flex-1 flex items-center justify-center py-3 text-lg bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold rounded-lg">
                        ❌ BATAL
                    </a>
                </div>
            </form>

            <!-- Info Box -->
            <div class="mt-8 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <p class="text-sm text-yellow-800">
                    <strong>⚠️ Catatan Penting:</strong>
                </p>
                <ul class="list-disc ml-5 text-sm text-yellow-700 mt-2">
                    <li>Barang hanya bisa dialokasikan jika ada stok di gudang</li>
                    <li>Stok gudang tidak boleh melebihi jumlah yang dialokasikan</li>
                    <li>Proses alokasi dicatat untuk audit trail</li>
                    <li>Hanya barang dari etalase yang bisa dijual/keluar</li>
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
