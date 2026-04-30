<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('🛒 Mesin Kasir') }}
        </h2>
    </x-slot>

    <!-- Pustaka Tambahan: jQuery, Select2, dan HTML5-QRCode -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <div class="py-6">
        
        <!-- KOTAK PESAN ERROR & SUKSES (BARU DITAMBAHKAN) -->
        <div class="max-w-full px-4 sm:px-6 lg:px-8 mx-auto mb-4">
            @if(session('error'))
                <div class="p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg flex items-center shadow-sm font-bold text-lg mb-4">
                    🚨 ERROR: {{ session('error') }}
                </div>
            @endif
            @if(session('success'))
                <div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg flex items-center shadow-sm font-bold text-lg mb-4">
                    ✅ {{ session('success') }}
                </div>
            @endif
        </div>
        <!-- ============================================ -->

        <div class="max-w-full px-4 sm:px-6 lg:px-8 mx-auto flex flex-col lg:flex-row gap-6">
            
            <!-- BAGIAN KIRI: Form Input/Scan Barang -->
            <div class="w-full lg:w-1/3 bg-white shadow-sm sm:rounded-lg p-6 h-fit border-t-4 border-blue-500">
                <form action="{{ route('penjualan.keranjang.tambah') }}" method="POST" id="form-tambah-keranjang">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="barcode" class="block text-sm font-bold text-gray-700 mb-2">1. Barcode (Opsional - Bisa di-scan)</label>
                        <div class="flex flex-col sm:flex-row gap-2 items-stretch sm:items-start">
                            <div class="flex-grow">
                                <input id="barcode" class="block w-full font-mono text-gray-600 text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-blue-50 text-center font-bold" type="text" name="barcode" value="" placeholder="Scan alat / kamera..." autofocus />
                            </div>
                            <button type="button" id="btn-scan" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md shadow-sm transition text-sm whitespace-nowrap">
                                📷 Scan Kamera
                            </button>
                        </div>
                        <div id="reader" style="width: 100%; display: none;" class="mt-3 border-2 border-dashed border-gray-300 rounded-md overflow-hidden max-h-72"></div>
                    </div>

                    <div class="mb-4">
                        <label for="id_makanan" class="block text-sm font-bold text-gray-700 mb-2">2. Atau Cari Manual</label>
                        <select id="id_makanan" name="id_makanan" class="select2-dropdown border-gray-300 rounded-md shadow-sm block mt-1 w-full">
                            <option value="" disabled selected>-- Ketik/Pilih Jajanan --</option>
                            @foreach($makanan as $item)
                                <!-- Atribut data-barcode ditambahkan agar fungsi scan milikmu bisa mencocokkan data -->
                                <option value="{{ $item->id_makanan }}" data-barcode="{{ $item->barcode ?? '' }}">
                                    {{ $item->nama_makanan }} (Stok: {{ $item->stok_etalase }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-6">
                        <label for="jumlah" class="block text-sm font-bold text-gray-700">3. Jumlah Qty</label>
                        <input type="number" name="jumlah" id="jumlah" value="1" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 font-bold text-center text-lg" required>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg shadow-md transition-all text-lg">
                        + Tambah ke Keranjang
                    </button>
                </form>
            </div>

            <!-- BAGIAN KANAN: Daftar Keranjang & Pembayaran -->
            <div class="w-full lg:w-2/3 bg-white shadow-sm sm:rounded-lg p-6 border-t-4 border-green-500 flex flex-col">
                <h3 class="text-lg font-bold text-gray-700 mb-4 border-b pb-2">Keranjang Belanja</h3>
                
                <div class="overflow-x-auto flex-1 mb-6">
                    <table class="min-w-full border border-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="py-3 px-4 border-b text-left text-sm font-bold text-gray-700">Jajanan</th>
                                <th class="py-3 px-4 border-b text-center text-sm font-bold text-gray-700">Harga</th>
                                <th class="py-3 px-4 border-b text-center text-sm font-bold text-gray-700">Qty</th>
                                <th class="py-3 px-4 border-b text-right text-sm font-bold text-gray-700">Subtotal</th>
                                <th class="py-3 px-4 border-b text-center text-sm font-bold text-gray-700">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($keranjang as $id => $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="py-3 px-4 border-b text-sm font-medium">{{ $item['nama_makanan'] }}</td>
                                    <td class="py-3 px-4 border-b text-center text-sm">Rp {{ number_format($item['harga_satuan'], 0, ',', '.') }}</td>
                                    <td class="py-3 px-4 border-b text-center text-sm font-bold">{{ $item['jumlah'] }}</td>
                                    <td class="py-3 px-4 border-b text-right text-sm font-bold text-indigo-600">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                                    <td class="py-3 px-4 border-b text-center">
                                        <form action="{{ route('penjualan.keranjang.hapus', $id) }}" method="POST" onsubmit="return confirm('Hapus barang ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-white bg-red-500 hover:bg-red-600 px-3 py-1 rounded shadow text-xs font-bold transition">HAPUS</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-gray-500 italic">
                                        Keranjang masih kosong. Silakan scan barcode atau pilih barang manual.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Bagian Total & Pembayaran -->
                <div class="bg-gray-50 p-6 rounded-xl border border-gray-200 mt-auto">
                    <div class="flex justify-between items-end mb-4 border-b pb-4">
                        <span class="text-xl font-bold text-gray-700">TOTAL TAGIHAN:</span>
                        <span class="text-4xl font-black text-green-600">Rp {{ number_format($total_harga, 0, ',', '.') }}</span>
                    </div>

                    <form action="{{ route('penjualan.store') }}" method="POST">
                        @csrf
                        <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-end">
                            <div class="w-full sm:w-2/3">
                                <label for="bayar" class="block text-sm font-bold text-gray-700 mb-1">Uang Diterima dari Pelanggan (Rp)</label>
                                <input type="number" name="bayar" id="bayar" min="{{ $total_harga }}" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-2xl font-bold p-3" required placeholder="Cth: 50000" {{ count($keranjang) == 0 ? 'disabled' : '' }}>
                            </div>
                            <div class="w-full sm:w-1/3">
                                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-black py-4 px-4 rounded-lg shadow-lg text-xl transition-all {{ count($keranjang) == 0 ? 'opacity-50 cursor-not-allowed' : '' }}" {{ count($keranjang) == 0 ? 'disabled' : '' }}>
                                    💵 BAYAR
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            // 1. Inisialisasi Select2
            $('.select2-dropdown').select2({
                placeholder: "-- Ketik nama jajanan... --",
                allowClear: true,
                width: '100%'
            });

            // 2. Logika Scanner Bawaan
            const btnScan = document.getElementById('btn-scan');
            const readerDiv = document.getElementById('reader');
            const barcodeInput = document.getElementById('barcode');
            const selectMakanan = document.getElementById('id_makanan');
            const formTambah = document.getElementById('form-tambah-keranjang');

            let html5QrCode;
            let isScanning = false;

            // Jika mengetik/scan pakai alat scanner fisik
            barcodeInput.addEventListener('input', function() {
                findMakananByBarcode(this.value.trim());
            });

            // Otomatis Submit jika alat scanner fisik menekan Enter
            barcodeInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    if(barcodeInput.value.trim() !== '') {
                        formTambah.submit();
                    }
                }
            });

            btnScan.addEventListener('click', function() {
                if (isScanning) {
                    html5QrCode.stop().then(() => {
                        readerDiv.style.display = 'none';
                        isScanning = false;
                        btnScan.innerHTML = '📷 Scan Kamera';
                        btnScan.classList.replace('bg-red-600', 'bg-blue-600');
                        btnScan.classList.replace('hover:bg-red-700', 'hover:bg-blue-700');
                    }).catch(err => console.error("Gagal mematikan scanner", err));
                    return;
                }

                readerDiv.style.display = 'block';
                btnScan.innerHTML = 'Batal Scan';
                btnScan.classList.replace('bg-blue-600', 'bg-red-600');
                btnScan.classList.replace('hover:bg-blue-700', 'hover:bg-red-700');
                
                isScanning = true;

                html5QrCode = new Html5Qrcode("reader");
                const config = { fps: 10, qrbox: { width: Math.min(250, window.innerWidth * 0.8), height: 100 } };

                html5QrCode.start(
                    { facingMode: "environment" },
                    config,
                    (decodedText, decodedResult) => {
                        // Memasukkan hasil scan
                        barcodeInput.value = decodedText;
                        findMakananByBarcode(decodedText);

                        html5QrCode.stop().then(() => {
                            readerDiv.style.display = 'none';
                            isScanning = false;
                            btnScan.innerHTML = '📷 Scan Kamera';
                            btnScan.classList.replace('bg-red-600', 'bg-blue-600');
                            btnScan.classList.replace('hover:bg-red-700', 'hover:bg-blue-700');

                            barcodeInput.classList.add('bg-green-100');
                            
                            // Otomatis Submit ke keranjang setelah berhasil memindai!
                            setTimeout(() => {
                                barcodeInput.classList.remove('bg-green-100');
                                formTambah.submit(); 
                            }, 500);

                        }).catch(err => console.error(err));
                    },
                    (errorMessage) => {}
                ).catch((err) => {
                    console.error("Error memulai kamera: ", err);
                    alert("Kamera tidak dapat diakses. Pastikan Anda mengizinkan akses kamera.");

                    readerDiv.style.display = 'none';
                    isScanning = false;
                    btnScan.innerHTML = '📷 Scan Kamera';
                    btnScan.classList.replace('bg-red-600', 'bg-blue-600');
                    btnScan.classList.replace('hover:bg-red-700', 'hover:bg-blue-700');
                });
            });

            // Fungsi pencari option Select2 berdasarkan barcode
            function findMakananByBarcode(barcode) {
                if (!barcode) return;
                for (let option of selectMakanan.options) {
                    if (option.getAttribute('data-barcode') === barcode) {
                        // Update value dan paksa Select2 untuk refresh UI
                        $(selectMakanan).val(option.value).trigger('change');
                        break;
                    }
                }
            }
        });
    </script>
</x-app-layout>