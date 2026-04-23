<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pencatatan Barang Masuk') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('log.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-6 bg-green-50 p-4 rounded-lg border border-green-200">
                        <p class="text-sm font-medium text-green-700">Mode: ➕ Tambah Stok (Barang Masuk)</p>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="id_makanan" value="Pilih Jajanan" />
                        <select id="id_makanan" name="id_makanan" class="border-gray-300 rounded-md shadow-sm block mt-1 w-full" required>
                            @foreach($makanan as $item)
                                <option value="{{ $item->id_makanan }}">{{ $item->nama_makanan }} (Stok: {{ $item->stok }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-6">
                        <x-input-label for="jumlah_perubahan" value="Jumlah Masuk (Pcs)" />
                        <x-text-input id="jumlah_perubahan" class="block mt-1 w-full text-center" type="number" min="1" name="jumlah_perubahan" required />
                    </div>

                    <x-primary-button class="w-full bg-green-600 hover:bg-green-700">SIMPAN BARANG MASUK</x-primary-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>