<x-app-layout>
    <x-slot name="header">
        {{ __('Form Pencatatan Retur Barang') }}
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">{{ session('error') }}</div>
            @endif
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">{{ session('success') }}</div>
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
                    <x-input-label for="id_penjualan" value="1. Pilih No Nota" />
                    <select id="id_penjualan" name="id_penjualan" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full mt-1" required>
                        <option value="">-- Pilih Transaksi --</option>
                        @foreach($penjualan as $trx)
                            <option value="{{ $trx->id_penjualan }}"
                                {{ (isset($selected_id) && $selected_id == $trx->id_penjualan) ? 'selected' : '' }}
                                data-items='@json($trx->detail)'>
                                {{ $trx->no_nota ?? 'N/A' }} | Tanggal: {{ \Carbon\Carbon::parse($trx->tanggal_penjualan)->format('d M Y') }} | Total: Rp {{ number_format($trx->total_harga, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4 bg-gray-50 p-4 rounded-lg border border-gray-200 shadow-inner" id="section-barang" style="display: none;">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end mb-3 gap-3">
                        <div>
                            <x-input-label value="2. Cari Barang & Input Jumlah" />
                            <span class="text-xs text-gray-500 font-medium">(Pilih dan isi jumlah pada salah satu barang)</span>
                        </div>
                        <div class="w-full sm:w-1/2 relative">
                            <input type="text" id="search-makanan" class="pl-9 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full text-sm" placeholder="Ketik nama barang di sini...">
                        </div>
                    </div>

                    <div class="border border-gray-200 rounded-md overflow-hidden bg-white shadow-sm">
                        <div class="bg-gray-100 px-4 py-2 grid grid-cols-12 gap-4 text-sm font-bold text-gray-700 border-b border-gray-200">
                            <div class="col-span-8">Daftar Belanjaan</div>
                            <div class="col-span-4 text-center">Jml Retur</div>
                        </div>
                        <div id="list-makanan-container" class="max-h-64 overflow-y-auto divide-y divide-gray-100">
                            </div>
                    </div>
                </div>

                <input type="hidden" id="id_makanan_hidden" name="id_makanan" required>
                <input type="hidden" id="jumlah_retur_hidden" name="jumlah_retur" required>
                <div class="grid grid-cols-1 mb-4">
                    <div>
                        <x-input-label for="tgl_retur" value="3. Tanggal Retur" />
                        <x-text-input id="tgl_retur" class="block w-full mt-1" type="date" name="tgl_retur" value="{{ date('Y-m-d') }}" required />
                    </div>
                </div>

                <div class="mb-6">
                    <x-input-label for="alasan" value="4. Alasan Retur" />
                    <textarea id="alasan" name="alasan" rows="3" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full mt-1" placeholder="Contoh: Salah beli, barang cacat dari kasir..." required></textarea>
                </div>

                <x-primary-button id="btn-submit-retur" class="w-full justify-center py-3 bg-orange-600 hover:bg-orange-700 text-lg">
                    PROSES RETUR & KEMBALIKAN KE ETALASE
                </x-primary-button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectTrx = document.getElementById('id_penjualan');
            const sectionBarang = document.getElementById('section-barang');
            const container = document.getElementById('list-makanan-container');
            const searchInput = document.getElementById('search-makanan');

            const hiddenIdMakanan = document.getElementById('id_makanan_hidden');
            const hiddenJumlah = document.getElementById('jumlah_retur_hidden');

            let currentItems = [];

            // Render list UI
            function renderList(filterText = '') {
                container.innerHTML = '';

                const filtered = currentItems.filter(item => {
                    const nama = item.makanan ? item.makanan.nama_makanan.toLowerCase() : 'produk terhapus';
                    return nama.includes(filterText.toLowerCase());
                });

                if (filtered.length === 0) {
                    container.innerHTML = '<div class="p-6 text-center text-gray-500 italic text-sm">Barang tidak ditemukan dalam transaksi ini.</div>';
                    return;
                }

                filtered.forEach(item => {
                    const nama = item.makanan ? item.makanan.nama_makanan : 'Produk Terhapus';
                    const hargaFmt = new Intl.NumberFormat('id-ID').format(item.harga_satuan);

                    const html = `
                        <div class="px-4 py-3 grid grid-cols-12 gap-4 items-center hover:bg-indigo-50 transition">
                            <div class="col-span-8">
                                <p class="text-sm font-bold text-gray-800">${nama}</p>
                                <p class="text-xs text-gray-500 mt-1">Harga: Rp ${hargaFmt} | Max Bisa Diretur: <span class="font-bold text-indigo-600">${item.jumlah} pcs</span></p>
                            </div>
                            <div class="col-span-4 flex justify-center">
                                <input type="number"
                                    class="input-qty border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-20 text-center text-sm"
                                    data-id="${item.id_makanan}"
                                    max="${item.jumlah}"
                                    min="0"
                                    placeholder="0">
                            </div>
                        </div>
                    `;
                    container.insertAdjacentHTML('beforeend', html);
                });

                // Logic input: Cegah error form ganda, sync ke Hidden Input
                const inputs = container.querySelectorAll('.input-qty');
                inputs.forEach(input => {
                    input.addEventListener('input', function() {
                        const val = parseInt(this.value);
                        const max = parseInt(this.getAttribute('max'));

                        // Validasi agar tidak bisa retur melebihi yang dibeli
                        if (val > max) this.value = max;
                        if (val < 0) this.value = 0;

                        if (this.value > 0) {
                            // Kosongkan form barang lain agar Controller tidak bingung
                            inputs.forEach(other => {
                                if (other !== this) other.value = '';
                            });
                            // Masukkan data ke input yang sesungguhnya
                            hiddenIdMakanan.value = this.getAttribute('data-id');
                            hiddenJumlah.value = this.value;
                        } else {
                            hiddenIdMakanan.value = '';
                            hiddenJumlah.value = '';
                        }
                    });
                });
            }

            // Memicu Update List Saat Kode Transaksi Terpilih
            function updateMakanan() {
                const selectedOption = selectTrx.options[selectTrx.selectedIndex];
                hiddenIdMakanan.value = '';
                hiddenJumlah.value = '';
                searchInput.value = '';

                if (!selectedOption || !selectedOption.value) {
                    sectionBarang.style.display = 'none';
                    currentItems = [];
                    return;
                }

                sectionBarang.style.display = 'block';
                currentItems = JSON.parse(selectedOption.getAttribute('data-items') || '[]');
                renderList();
            }

            // Jalankan fungsi saat halaman load (Misal jika user me-klik dari laporan)
            if(selectTrx.value) updateMakanan();

            // Interaksi manual user
            selectTrx.addEventListener('change', updateMakanan);
            searchInput.addEventListener('input', (e) => renderList(e.target.value));

            // Logic Submit & Cegah Spam Klik
            const form = document.getElementById('form-retur');
            let isSubmitting = false;

            if(form) {
                form.addEventListener('submit', function(e) {
                    if (!hiddenIdMakanan.value || !hiddenJumlah.value) {
                        e.preventDefault();
                        alert('Silakan isi angka pada kolom Jml Retur di salah satu barang!');
                        return false;
                    }

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
