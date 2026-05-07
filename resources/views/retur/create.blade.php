<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg sm:text-xl text-gray-800 leading-tight">{{ __('Input Retur Baru') }}</h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('error'))
                <div class="mb-4 p-3 sm:p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Langkah 1: Pilih Transaksi Penjualan -->
            <div class="mb-6 bg-white rounded-lg border border-gray-200 p-4 sm:p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-700 rounded-full font-bold text-sm">1</span>
                    Pilih Transaksi Penjualan
                </h3>

                <div class="space-y-3 max-h-96 overflow-y-auto border border-gray-200 rounded-lg">
                    @forelse($penjualanList as $penjualan)
                        <label class="flex items-start p-3 hover:bg-blue-50 cursor-pointer transition border-b border-gray-100 last:border-0">
                            <input type="radio" name="id_penjualan" value="{{ $penjualan->id_penjualan }}" class="mt-1 mr-3 transaction-radio" />
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-start gap-2 mb-1">
                                    <p class="font-semibold text-gray-800 truncate">{{ $penjualan->kode_transaksi ?? 'N/A' }}</p>
                                    <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded whitespace-nowrap font-bold">
                                        Rp {{ number_format($penjualan->total_harga, 0, ',', '.') }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-600">
                                    {{ $penjualan->tanggal_penjualan ? \Carbon\Carbon::parse($penjualan->tanggal_penjualan)->format('d M Y H:i') : 'N/A' }} • Kasir: {{ $penjualan->pengguna->username ?? 'Unknown' }}
                                </p>
                                @if($penjualan->detail && $penjualan->detail->count() > 0)
                                    <p class="text-xs text-gray-600 mt-1">
                                        {{ $penjualan->detail->count() }} item:
                                        {{ $penjualan->detail->map(fn($d) => $d->makanan->nama_makanan ?? 'Terhapus')->join(', ') }}
                                    </p>
                                @endif
                            </div>
                        </label>
                    @empty
                        <div class="p-6 text-center text-gray-500">
                            <p>Tidak ada transaksi penjualan.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Langkah 2: Pilih Item yang Diretur -->
            <div class="mb-6 bg-white rounded-lg border border-gray-200 p-4 sm:p-6 shadow-sm" id="step2-container" style="display: none;">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-8 h-8 bg-green-100 text-green-700 rounded-full font-bold text-sm">2</span>
                    Pilih Item yang Diretur
                </h3>

                <form action="{{ route('retur.store') }}" method="POST" id="retur-form">
                    @csrf

                    <input type="hidden" name="id_penjualan" id="form-id_penjualan" />

                    <div id="items-container" class="space-y-4">
                        <!-- Items akan diisi oleh JavaScript -->
                    </div>

                    <!-- Button Aksi -->
                    <div class="mt-6 flex flex-col sm:flex-row gap-3 pt-4 border-t border-gray-200">
                        <a href="{{ route('retur.index') }}" class="flex-1 text-center bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium text-sm transition border border-gray-300">
                            ← Batal
                        </a>
                        <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium text-sm transition">
                            ✅ Simpan Retur
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.transaction-radio').forEach(radio => {
            radio.addEventListener('change', function() {
                const id_penjualan = this.value;
                loadTransactionDetails(id_penjualan);
            });
        });

        function loadTransactionDetails(id_penjualan) {
            // Set form value
            document.getElementById('form-id_penjualan').value = id_penjualan;

            // Fetch data via AJAX
            fetch(`/retur/get-detail/${id_penjualan}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        renderReturnItems(data.detail);
                        document.getElementById('step2-container').style.display = 'block';
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function renderReturnItems(details) {
            const container = document.getElementById('items-container');
            container.innerHTML = '';

            details.forEach((detail, index) => {
                const makananNama = detail.makanan?.nama_makanan || 'Terhapus';
                const hargaSatuan = detail.harga_satuan || 0;

                const itemHtml = `
                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-800">${makananNama}</h4>
                                <p class="text-sm text-gray-600">
                                    Harga: Rp ${new Intl.NumberFormat('id-ID').format(hargaSatuan)} |
                                    Terjual: ${detail.jumlah} pcs
                                </p>
                            </div>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" class="item-checkbox" value="${index}" />
                                <span class="text-sm text-gray-600">Diretur</span>
                            </label>
                        </div>

                        <div class="item-inputs space-y-3" style="display: none;" id="inputs-${index}">
                            <input type="hidden" name="retur_items[${index}][id_makanan]" value="${detail.id_makanan}" />

                            <div>
                                <label class="text-sm font-medium text-gray-700">Jumlah Retur (Max: ${detail.jumlah})</label>
                                <input type="number" name="retur_items[${index}][jumlah_retur]" min="1" max="${detail.jumlah}" value="1" class="mt-1 w-full border-gray-300 rounded-md shadow-sm px-3 py-2" required />
                            </div>

                            <div>
                                <label class="text-sm font-medium text-gray-700">Harga Satuan (Rp)</label>
                                <input type="number" name="retur_items[${index}][harga_satuan]" value="${hargaSatuan}" min="0" class="mt-1 w-full border-gray-300 rounded-md shadow-sm px-3 py-2" required />
                            </div>

                            <div>
                                <label class="text-sm font-medium text-gray-700">Alasan Retur *</label>
                                <select name="retur_items[${index}][alasan_retur]" class="mt-1 w-full border-gray-300 rounded-md shadow-sm px-3 py-2" required>
                                    <option value="">-- Pilih Alasan --</option>
                                    <option value="rusak">Rusak</option>
                                    <option value="expired">Expired/Kadaluarsa</option>
                                    <option value="tidak_sesuai">Tidak Sesuai Pesanan</option>
                                    <option value="salah_kirim">Salah Kirim</option>
                                    <option value="lainnya">Lainnya</option>
                                </select>
                            </div>

                            <div>
                                <label class="text-sm font-medium text-gray-700">Keterangan (Opsional)</label>
                                <textarea name="retur_items[${index}][keterangan]" class="mt-1 w-full border-gray-300 rounded-md shadow-sm px-3 py-2 text-sm" rows="2" placeholder="Tambahkan catatan jika ada..."></textarea>
                            </div>
                        </div>
                    </div>
                `;

                container.innerHTML += itemHtml;
            });

            // Add event listeners untuk checkbox
            document.querySelectorAll('.item-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const index = this.value;
                    const inputsDiv = document.getElementById(`inputs-${index}`);
                    inputsDiv.style.display = this.checked ? 'block' : 'none';
                });
            });
        }
    </script>
</x-app-layout>
