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
                    <p class="text-sm font-medium text-red-700">Mode: ➖ Kurangi Stok (Barang Keluar)</p>
                </div>

                <div class="mb-4">
                    <x-input-label for="id_makanan" value="Pilih Jajanan" />
                    <select id="id_makanan" name="id_makanan" class="searchable-select border-gray-300 rounded-md shadow-sm block mt-1 w-full" required autofocus>
                        <option value=""></option>
                        @foreach($makanan as $item)
                            <option value="{{ $item->id_makanan }}">{{ $item->nama_makanan }} (Stok: {{ $item->stok }})</option>
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

                <div class="mb-6">
                    <x-input-label for="alasan" value="Alasan Pengeluaran" />

                    @if(Auth::user()->role === 'Admin')
                        <x-text-input id="alasan" name="alasan" type="text" class="block mt-1 w-full bg-gray-100 cursor-not-allowed text-gray-600" value="Penjualan" readonly />
                        <p class="text-xs text-red-500 mt-1 font-medium italic">*Sebagai Admin, alasan mutasi otomatis diatur sebagai Penjualan.</p>
                    @else
                        <select id="alasan_type" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                            <option value="penjualan" selected>Penjualan</option>
                            <option value="lainnya">Lainnya</option>
                        </select>

                        <!-- Input untuk Penjualan (hidden) -->
                        <input type="hidden" id="alasan" name="alasan" value="Penjualan" />

                        <!-- Textarea untuk Lainnya (hidden by default) -->
                        <textarea id="alasan_custom" name="alasan_custom" rows="3" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full hidden" placeholder="Tulis alasan pengeluaran barang (Contoh: Kedaluwarsa, Expired, Barang Rusak, Pemberian ke Pegawai, dll...)"></textarea>

                        <script>
                            document.getElementById('alasan_type').addEventListener('change', function(e) {
                                const alasinField = document.getElementById('alasan');
                                const customField = document.getElementById('alasan_custom');

                                if (e.target.value === 'lainnya') {
                                    customField.classList.remove('hidden');
                                    customField.required = true;
                                    alasinField.value = '';
                                } else {
                                    customField.classList.add('hidden');
                                    customField.required = false;
                                    customField.value = '';
                                    alasinField.value = 'Penjualan';
                                }
                            });

                            // Update alasan field on form submit
                            document.querySelector('form').addEventListener('submit', function(e) {
                                const alasinType = document.getElementById('alasan_type').value;
                                const alasinField = document.getElementById('alasan');
                                const customField = document.getElementById('alasan_custom');

                                if (alasinType === 'lainnya') {
                                    if (!customField.value.trim()) {
                                        e.preventDefault();
                                        alert('Silakan isi alasan pengeluaran');
                                        customField.focus();
                                        return;
                                    }
                                    alasinField.value = customField.value;
                                }
                            });
                        </script>
                    @endif
                </div>

                <x-primary-button class="w-full justify-center py-3 text-lg bg-red-600 hover:bg-red-700">SIMPAN BARANG KELUAR</x-primary-button>
            </form>
        </div>
    </div>
</x-app-layout>
