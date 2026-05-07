<x-app-layout>
    <x-slot name="header">
        {{ __('Form Pencatatan Retur Barang') }}
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            
            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">{{ session('error') }}</div>
            @endif

            <form action="{{ route('retur.store') }}" method="POST" id="form-retur">
                @csrf
                <div class="mb-6 bg-orange-50 p-4 rounded-lg border border-orange-200">
                    <p class="text-sm font-bold text-orange-700">⚠️ Perhatian Mode Retur!</p>
                    <ul class="text-xs text-orange-600 mt-2 list-disc list-inside">
                        <li>Stok barang yang diretur akan dikembalikan otomatis ke <strong>ETALASE</strong>.</li>
                        <li>Total pendapatan pada Transaksi Penjualan terkait akan <strong>dipotong otomatis</strong>.</li>
                    </ul>
                </div>

                <div class="mb-4">
                    <x-input-label for="id_penjualan" value="1. Pilih Kode Transaksi" />
                    <select id="id_penjualan" name="id_penjualan" class="border-gray-300 rounded-md shadow-sm w-full mt-1" required>
                        <option value="">-- Pilih Transaksi --</option>
                        @foreach($penjualan as $trx)
                            <option value="{{ $trx->id_penjualan }}" 
                                {{ (isset($selected_id) && $selected_id == $trx->id_penjualan) ? 'selected' : '' }}
                                data-items='@json($trx->detail)'>
                                {{ $trx->kode_transaksi }} | Tanggal: {{ \Carbon\Carbon::parse($trx->tgl_penjualan)->format('d M Y') }} | Total: Rp {{ number_format($trx->total_harga, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <x-input-label for="id_makanan" value="2. Jajanan yang Diretur" />
                    <select id="id_makanan" name="id_makanan" class="border-gray-300 rounded-md shadow-sm w-full mt-1" required>
                        <option value="">-- Pilih Transaksi di atas terlebih dahulu --</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <x-input-label for="jumlah_retur" value="3. Jumlah Pcs Diretur" />
                        <x-text-input id="jumlah_retur" class="block w-full mt-1 text-center font-bold" type="number" min="1" name="jumlah_retur" required />
                    </div>
                    <div>
                        <x-input-label for="tgl_retur" value="4. Tanggal Retur" />
                        <x-text-input id="tgl_retur" class="block w-full mt-1" type="date" name="tgl_retur" value="{{ date('Y-m-d') }}" required />
                    </div>
                </div>

                <div class="mb-6">
                    <x-input-label for="alasan" value="5. Alasan Retur" />
                    <textarea id="alasan" name="alasan" rows="3" class="border-gray-300 rounded-md shadow-sm w-full mt-1" placeholder="Contoh: Salah beli, barang cacat dari kasir..." required></textarea>
                </div>

                <x-primary-button id="btn-submit-retur" class="w-full justify-center py-3 bg-orange-600 hover:bg-orange-700 text-lg">
                    PROSES RETUR & KEMBALIKAN KE ETALASE
                </x-primary-button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- LOGIKA FILTER BARANG BERDASARKAN TRANSAKSI ---
            const selectTrx = document.getElementById('id_penjualan');
            const selectMakanan = document.getElementById('id_makanan');

            function updateMakanan() {
                const selectedOption = selectTrx.options[selectTrx.selectedIndex];
                
                // Jika tidak ada transaksi yang dipilih, kosongkan dropdown barang
                if (!selectedOption || !selectedOption.value) {
                    selectMakanan.innerHTML = '<option value="">-- Pilih Transaksi di atas terlebih dahulu --</option>';
                    return;
                }
                
                // Ambil daftar barang dari atribut data-items
                const items = JSON.parse(selectedOption.getAttribute('data-items') || '[]');
                
                // Reset dropdown barang
                selectMakanan.innerHTML = '<option value="">-- Pilih Jajanan --</option>';
                
                // Isi dropdown dengan barang yang hanya dibeli pada transaksi tersebut
                items.forEach(item => {
                    const opt = document.createElement('option');
                    opt.value = item.id_makanan;
                    const namaMakanan = item.makanan ? item.makanan.nama_makanan : 'Produk Terhapus';
                    opt.textContent = `${namaMakanan} (Dibeli: ${item.jumlah} pcs)`;
                    selectMakanan.appendChild(opt);
                });
            }

            // Panggil saat halaman pertama dimuat (berguna jika datang dari tombol Laporan)
            if(selectTrx.value) updateMakanan();

            // Panggil setiap kali user mengganti pilihan transaksi secara manual
            selectTrx.addEventListener('change', updateMakanan);

            // --- LOGIKA ANTI SPAM KLIK ---
            const form = document.getElementById('form-retur');
            let isSubmitting = false;

            if(form) {
                form.addEventListener('submit', function(e) {
                    if (isSubmitting) {
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        return false;
                    }
                    isSubmitting = true;
                    const btn = document.getElementById('btn-submit-retur');
                    btn.innerHTML = 'MEMPROSES... ⏳';
                    btn.classList.add('opacity-50', 'cursor-not-allowed');
                    setTimeout(() => { btn.disabled = true; }, 10);
                });
            }
        });
    </script>
</x-app-layout>