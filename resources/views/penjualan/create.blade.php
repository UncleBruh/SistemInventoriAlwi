<x-app-layout>
    <x-slot name="header">
        {{ __('Tambah Penjualan') }}
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

            <form action="{{ route('penjualan.store') }}" method="POST">
                @csrf
                <div class="mb-6 bg-green-50 p-4 rounded-lg border border-green-200">
                    <p class="text-sm font-medium text-green-700">💰 Pencatatan Penjualan - Stok akan berkurang dan pendapatan tercatat</p>
                </div>

                <div class="mb-4">
                    <x-input-label for="id_makanan" value="Pilih Jajanan yang Terjual" />
                    <select id="id_makanan" name="id_makanan" class="searchable-select border-gray-300 rounded-md shadow-sm block mt-1 w-full" required autofocus onchange="updateHarga()">
                        <option value="">-- Pilih Jajanan --</option>
                        @foreach($makanan as $item)
                            <option value="{{ $item->id_makanan }}" data-harga="{{ $item->harga }}" data-stok="{{ $item->stok }}">
                                {{ $item->nama_makanan }} - Rp {{ number_format($item->harga, 0, ',', '.') }} (Stok: {{ $item->stok }})
                            </option>
                        @endforeach
                    </select>
                    @error('id_makanan')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4">
                    <x-input-label for="tanggal_penjualan" value="Tanggal Penjualan" />
                    <x-text-input id="tanggal_penjualan" class="block mt-1 w-full" type="datetime-local" name="tanggal_penjualan" value="{{ date('Y-m-d\TH:i') }}" required />
                    @error('tanggal_penjualan')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4">
                    <x-input-label for="jumlah_terjual" value="Jumlah Terjual (Pcs)" />
                    <x-text-input id="jumlah_terjual" class="block mt-1 w-full text-center text-2xl font-bold" type="number" min="1" name="jumlah_terjual" value="1" required onchange="hitungTotal()" oninput="hitungTotal()" />
                    @error('jumlah_terjual')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4">
                    <x-input-label for="harga_per_unit" value="Harga per Unit (Rp)" />
                    <x-text-input id="harga_per_unit" class="block mt-1 w-full text-right text-lg font-semibold" type="number" step="0.01" min="0" name="harga_per_unit" value="{{ old('harga_per_unit') }}" required onchange="hitungTotal()" oninput="hitungTotal()" />
                    @error('harga_per_unit')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-6 bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <div class="flex justify-between items-center">
                        <span class="text-blue-900 font-medium">Total Pendapatan:</span>
                        <span class="text-blue-900 text-2xl font-bold" id="totalDisplay">Rp 0</span>
                    </div>
                    <input type="hidden" id="totalInput" name="total_harga" value="0">
                </div>

                <div class="mb-6">
                    <x-input-label for="catatan" value="Catatan (Opsional)" />
                    <textarea id="catatan" name="catatan" rows="3" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" placeholder="Contoh: Penjualan di tempat, penjualan online, dll..."></textarea>
                </div>

                <x-primary-button class="w-full justify-center py-3 text-lg bg-green-600 hover:bg-green-700">💾 SIMPAN PENJUALAN</x-primary-button>
            </form>
        </div>
    </div>

    <script>
        function updateHarga() {
            const selectElement = document.getElementById('id_makanan');
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const harga = selectedOption.getAttribute('data-harga') || 0;
            const stok = selectedOption.getAttribute('data-stok') || 0;

            document.getElementById('harga_per_unit').value = harga;
            document.getElementById('jumlah_terjual').max = stok;
            hitungTotal();
        }

        function hitungTotal() {
            const jumlah = parseFloat(document.getElementById('jumlah_terjual').value) || 0;
            const harga = parseFloat(document.getElementById('harga_per_unit').value) || 0;
            const total = jumlah * harga;

            document.getElementById('totalInput').value = total;
            document.getElementById('totalDisplay').textContent = 'Rp ' + formatCurrency(total);
        }

        function formatCurrency(value) {
            return Math.floor(value).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        document.addEventListener('DOMContentLoaded', function() {
            hitungTotal();
        });
    </script>
</x-app-layout>
