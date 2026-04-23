<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pencatatan Barang Keluar') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg shadow-sm flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg shadow-sm flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('log.keluar.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-6 bg-red-50 p-4 rounded-lg border border-red-200">
                        <p class="text-sm font-medium text-red-700">Mode: ➖ Kurangi Stok (Barang Keluar)</p>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="id_makanan" value="Pilih Jajanan" />
                        <select id="id_makanan" name="id_makanan" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" required autofocus>
                            <option value="">-- Pilih Barang --</option>
                            @foreach($makanan as $item)
                                <option value="{{ $item->id_makanan }}">{{ $item->nama_makanan }} (Stok: {{ $item->stok }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="jumlah_perubahan" value="Jumlah Keluar (Pcs)" />
                        <x-text-input id="jumlah_perubahan" class="block mt-1 w-full text-center text-2xl font-bold" type="number" min="1" name="jumlah_perubahan" value="1" required />
                    </div>

                    <div class="mb-6">
                        <x-input-label for="alasan" value="Alasan Pengeluaran" />
                        <textarea id="alasan" name="alasan" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" rows="3" required placeholder="Contoh: Rusak atau Expired"></textarea>
                    </div>

                    <x-primary-button class="w-full justify-center py-3 text-lg bg-red-600 hover:bg-red-700">SIMPAN BARANG KELUAR</x-primary-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>