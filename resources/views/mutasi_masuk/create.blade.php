<x-app-layout>
    <x-slot name="header">
        {{ __('Pencatatan Barang Masuk') }}
    </x-slot>

    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg flex items-center">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg flex items-center">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('mutasi_masuk.store') }}" method="POST">
                @csrf
                <div class="mb-6 bg-green-50 p-4 rounded-lg border border-green-200">
                    <p class="text-sm font-medium text-green-700">Mode: ➕ Tambah Stok (Barang Masuk)</p>
                </div>

                <div class="mb-4">
                    <x-input-label for="id_makanan" value="Pilih Jajanan" />
                    <select id="id_makanan" name="id_makanan" class="border-gray-300 rounded-md shadow-sm block mt-1 w-full" required autofocus>
                        <option value="">-- Pilih Barang --</option>
                        @foreach($makanan as $item)
                            <option value="{{ $item->id_makanan }}">{{ $item->nama_makanan }} (Stok: {{ $item->stok }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <x-input-label for="tgl_mutasi" value="Tanggal Aktual Barang Datang" />
                    <x-text-input id="tgl_mutasi" class="block mt-1 w-full text-gray-700 font-medium" type="date" name="tgl_mutasi" value="{{ date('Y-m-d') }}" required />
                    <p class="text-xs text-gray-500 mt-1">Isi dengan tanggal kapan fisik barang benar-benar tiba di gudang.</p>
                </div>

                <div class="mb-6">
                    <x-input-label for="jumlah_perubahan" value="Jumlah Masuk (Pcs)" />
                    <x-text-input id="jumlah_perubahan" class="block mt-1 w-full text-center text-2xl font-bold" type="number" min="1" name="jumlah_perubahan" value="1" required />
                </div>

                <x-primary-button class="w-full justify-center py-3 text-lg bg-green-600 hover:bg-green-700">SIMPAN BARANG MASUK</x-primary-button>
            </form>
        </div>
    </div>
</x-app-layout>