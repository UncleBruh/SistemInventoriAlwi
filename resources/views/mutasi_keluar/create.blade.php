<x-app-layout>
    <x-slot name="header">
        {{ __('Pencatatan Barang Keluar') }}
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

            <form action="{{ route('mutasi_keluar.store') }}" method="POST">
                @csrf
                <div class="mb-6 bg-red-50 p-4 rounded-lg border border-red-200">
                    <p class="text-sm font-medium text-red-700">Mode: ➖ Kurangi Stok Etalase (Barang Keluar)</p>
                    <p class="text-xs text-red-600 mt-2">💡 Barang keluar hanya akan mengurangi stok ETALASE, bukan gudang.</p>
                </div>

                <div class="mb-4">
                    <x-input-label for="id_makanan" value="Pilih Jajanan" />
                    <select id="id_makanan" name="id_makanan" class="searchable-select border-gray-300 rounded-md shadow-sm block mt-1 w-full" required autofocus>
                        <option value=""></option>
                        @foreach($makanan as $item)
                            <option value="{{ $item->id_makanan }}">{{ $item->nama_makanan }} (Etalase: {{ $item->stok_etalase }} | Gudang: {{ $item->stok_gudang }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <x-input-label for="tgl_mutasi" value="Tanggal Aktual Barang Keluar" />
                    <x-text-input id="tgl_mutasi" class="block mt-1 w-full text-gray-700 font-medium" type="date" name="tgl_mutasi" value="{{ date('Y-m-d') }}" required />
                </div>

                <div class="mb-4">
                    <x-input-label for="jumlah_perubahan" value="Jumlah Keluar (Pcs)" />
                    <x-text-input id="jumlah_perubahan" class="block mt-1 w-full text-center text-2xl font-bold" type="number" min="1" name="jumlah_perubahan" value="1" required />
                </div>

                <div class="mb-4">
                    <x-input-label for="tipe_keluar" value="Tipe Pengeluaran Barang" />
                    <div class="grid grid-cols-2 gap-2 mt-2">
                        <label class="flex items-center p-2 border-2 border-gray-300 rounded-lg cursor-pointer hover:bg-green-50 transition" style="border-color: #22c55e;">
                            <input type="radio" name="tipe_keluar" value="penjualan" class="mr-2" @if(Auth::user()->role === 'Admin') checked @endif @if(Auth::user()->role === 'Admin') disabled @endif required />
                            <span class="text-sm font-medium">💰 Penjualan</span>
                        </label>
                        @if(Auth::user()->role !== 'Admin')
                        <label class="flex items-center p-2 border-2 border-gray-300 rounded-lg cursor-pointer hover:bg-orange-50 transition" style="border-color: #f97316;">
                            <input type="radio" name="tipe_keluar" value="rusak" class="mr-2" required />
                            <span class="text-sm font-medium">🔨 Rusak</span>
                        </label>
                        <label class="flex items-center p-2 border-2 border-gray-300 rounded-lg cursor-pointer hover:bg-yellow-50 transition" style="border-color: #eab308;">
                            <input type="radio" name="tipe_keluar" value="hilang" class="mr-2" required />
                            <span class="text-sm font-medium">❓ Hilang</span>
                        </label>
                        <label class="flex items-center p-2 border-2 border-gray-300 rounded-lg cursor-pointer hover:bg-purple-50 transition" style="border-color: #a855f7;">
                            <input type="radio" name="tipe_keluar" value="lainnya" class="mr-2" required />
                            <span class="text-sm font-medium">📋 Lainnya</span>
                        </label>
                        @endif
                    </div>
                </div>

                <div class="mb-6">
                    <x-input-label for="alasan" value="Keterangan Tambahan (Opsional)" />
                    <textarea id="alasan" name="alasan" rows="3" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" placeholder="Contoh: Barang expired, Kualitas buruk, dsb..."></textarea>
                </div>

                <x-primary-button class="w-full justify-center py-3 text-lg bg-red-600 hover:bg-red-700">SIMPAN BARANG KELUAR</x-primary-button>
            </form>
        </div>
    </div>
</x-app-layout>
