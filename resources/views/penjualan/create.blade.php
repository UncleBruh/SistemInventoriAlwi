<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('🛒 Mesin Kasir') }}
        </h2>
    </x-slot>

    <!-- Tambahkan CDN jQuery, Select2, dan HTML5-QRCode -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <div class="py-3 sm:py-6">
        <div class="max-w-full px-3 sm:px-4 lg:px-8 mx-auto grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6 auto-rows-max">

            <!-- BAGIAN KIRI: Form Input/Scan Barang -->
            <div class="lg:col-span-1 bg-white shadow-sm sm:rounded-lg p-4 sm:p-6 border-t-4 border-blue-500">

                <!-- 1. FORM BARCODE & KAMERA -->
                <h3 class="text-base sm:text-lg font-bold text-gray-700 mb-3 sm:mb-4 border-b pb-2">1. Scan Barcode</h3>

                <!-- Area Kamera (Disembunyikan secara default) -->
                <div id="reader" class="mb-3 sm:mb-4 hidden rounded-lg overflow-hidden border-2 border-dashed border-gray-300"></div>

                <!-- Tombol Buka Kamera -->
                <button type="button" id="btn-scan-kamera" class="w-full mb-2 sm:mb-4 bg-gray-800 hover:bg-gray-900 text-white font-bold py-2 px-3 sm:px-4 text-sm sm:text-base rounded shadow flex justify-center items-center gap-2">
                    📷 Nyalakan Kamera
                </button>
                <button type="button" id="btn-tutup-kamera" class="w-full mb-3 sm:mb-4 bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-3 sm:px-4 text-sm sm:text-base rounded shadow justify-center items-center gap-2 hidden">
                    ❌ Matikan Kamera
                </button>

                <form action="{{ route('penjualan.keranjang.tambah') }}" method="POST" id="form-barcode" class="mb-4 sm:mb-6">
                    @csrf
                    <div class="mb-2 sm:mb-3">
                        <label for="barcode" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Barcode / Scan</label>
                        <input type="text" name="barcode" id="barcode" autofocus placeholder="Gunakan Alat Scan / Kamera..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-center font-bold text-base sm:text-lg bg-blue-50">
                    </div>

                    <div class="mb-3 sm:mb-4">
                        <label for="qty-barcode" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Jumlah Qty</label>
                        <input type="number" name="jumlah" id="qty-barcode" value="1" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-center font-bold text-base sm:text-lg">
                    </div>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 sm:py-3 px-3 sm:px-4 text-sm sm:text-base rounded-lg shadow-md transition-all">
                        + Tambah dari Barcode
                    </button>
                    <p class="text-xs text-gray-500 text-center mt-2">Scan → Isi Qty → Tambah</p>
                </form>

                <!-- 2. FORM MANUAL SEARCH -->
                <h3 class="text-base sm:text-lg font-bold text-gray-700 mb-3 sm:mb-4 border-b pb-2">2. Atau Cari Manual</h3>
                <form action="{{ route('penjualan.keranjang.tambah') }}" method="POST">
                    @csrf
                    <div class="mb-3 sm:mb-4">
                        <label for="id_makanan" class="block text-xs sm:text-sm font-medium text-gray-700">Ketik Nama Jajanan</label>
                        <select name="id_makanan" id="id_makanan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm sm:text-base select2-dropdown">
                            <option value="" disabled selected>-- Ketik/Pilih Jajanan --</option>
                            @foreach($makanan as $item)
                                <option value="{{ $item->id_makanan }}">
                                    {{ $item->nama_makanan }} (Stok: {{ $item->stok_etalase }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3 sm:mb-4">
                        <label for="jumlah" class="block text-xs sm:text-sm font-medium text-gray-700">Jumlah Qty</label>
                        <input type="number" name="jumlah" id="jumlah" value="1" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-center font-bold text-base sm:text-lg" required>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 sm:py-3 px-3 sm:px-4 text-sm sm:text-base rounded-lg shadow-md transition-all">
                        + Tambah ke Keranjang
                    </button>
                </form>
            </div>

            <!-- BAGIAN KANAN: Daftar Keranjang & Pembayaran -->
            <div class="lg:col-span-2 bg-white shadow-sm sm:rounded-lg p-4 sm:p-6 border-t-4 border-green-500 flex flex-col">
                <h3 class="text-lg font-bold text-gray-700 mb-4 border-b pb-2">Keranjang Belanja</h3>

                <div class="overflow-x-auto flex-1 mb-4 sm:mb-6">
                    <table class="min-w-full border border-gray-200 text-sm sm:text-base">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="py-2 sm:py-3 px-2 sm:px-4 border-b text-left font-bold text-gray-700">Jajanan</th>
                                <th class="py-2 sm:py-3 px-2 sm:px-4 border-b text-center font-bold text-gray-700">Harga</th>
                                <th class="py-2 sm:py-3 px-2 sm:px-4 border-b text-center font-bold text-gray-700">Qty</th>
                                <th class="py-2 sm:py-3 px-2 sm:px-4 border-b text-right font-bold text-gray-700">Subtotal</th>
                                <th class="py-2 sm:py-3 px-2 sm:px-4 border-b text-center font-bold text-gray-700">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($keranjang as $id => $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="py-2 sm:py-3 px-2 sm:px-4 border-b font-medium">{{ $item['nama_makanan'] }}</td>
                                    <td class="py-2 sm:py-3 px-2 sm:px-4 border-b text-center">Rp {{ number_format($item['harga_satuan'], 0, ',', '.') }}</td>
                                    <td class="py-2 sm:py-3 px-2 sm:px-4 border-b text-center font-bold">{{ $item['jumlah'] }}</td>
                                    <td class="py-2 sm:py-3 px-2 sm:px-4 border-b text-right font-bold text-indigo-600">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                                    <td class="py-2 sm:py-3 px-2 sm:px-4 border-b text-center">
                                        <form action="{{ route('penjualan.keranjang.hapus', $id) }}" method="POST" onsubmit="return confirm('Hapus barang ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-white bg-red-500 hover:bg-red-600 px-2 sm:px-3 py-1 rounded shadow text-xs font-bold transition">HAPUS</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 sm:py-8 px-2 sm:px-4 text-center text-gray-500 italic">
                                        Keranjang masih kosong. Silakan scan barcode atau pilih barang manual.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Bagian Total & Pembayaran -->
                <div class="bg-gray-50 p-3 sm:p-6 rounded-xl border border-gray-200 mt-auto">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-end mb-3 sm:mb-4 border-b pb-3 sm:pb-4 gap-2">
                        <span class="text-base sm:text-xl font-bold text-gray-700">TOTAL BAYAR:</span>
                        <span class="text-2xl sm:text-4xl font-black text-green-600 text-center sm:text-right">Rp {{ number_format($total_harga, 0, ',', '.') }}</span>
                    </div>

                    <form action="{{ route('penjualan.store') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-black py-3 sm:py-4 px-3 sm:px-4 rounded-lg shadow-lg text-lg sm:text-xl transition-all {{ count($keranjang) == 0 ? 'opacity-50 cursor-not-allowed' : '' }}" {{ count($keranjang) == 0 ? 'disabled' : '' }}>
                            ✓ SELESAI
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <!-- Script Inisialisasi Select2 & Kamera Scanner -->
    <script>
        $(document).ready(function() {
            // 1. Inisialisasi Fitur Pencarian Dropdown
            $('.select2-dropdown').select2({
                placeholder: "-- Ketik nama jajanan... --",
                allowClear: true,
                width: '100%'
            });

            $('#barcode').focus();

            // 2. Setup HTML5 QR/Barcode Scanner
            let html5QrcodeScanner = null;

            $('#btn-scan-kamera').click(function() {
                $('#reader').removeClass('hidden');
                $('#btn-scan-kamera').addClass('hidden');
                $('#btn-tutup-kamera').removeClass('hidden').addClass('flex');

                html5QrcodeScanner = new Html5QrcodeScanner(
                    "reader", { fps: 10, qrbox: {width: 250, height: 150} }, false);

                html5QrcodeScanner.render(onScanSuccess, onScanFailure);
            });

            $('#btn-tutup-kamera').click(function() {
                if(html5QrcodeScanner) {
                    html5QrcodeScanner.clear();
                }
                $('#reader').addClass('hidden');
                $('#btn-tutup-kamera').addClass('hidden').removeClass('flex');
                $('#btn-scan-kamera').removeClass('hidden');
            });

            // Jika kamera berhasil menangkap Barcode
            function onScanSuccess(decodedText, decodedResult) {
                // Bunyikan suara bip kecil (opsional)
                let audio = new Audio('https://www.soundjay.com/buttons/sounds/beep-07a.mp3');
                audio.play().catch(e => console.log('Audio error:', e));

                // Matikan kamera setelah scan berhasil
                html5QrcodeScanner.clear();
                $('#reader').addClass('hidden');
                $('#btn-tutup-kamera').addClass('hidden').removeClass('flex');
                $('#btn-scan-kamera').removeClass('hidden');

                // Masukkan angka barcode ke kolom input
                $('#barcode').val(decodedText);

                // Reset qty to 1 and focus on quantity input (jangan auto-submit)
                $('#qty-barcode').val(1);
                $('#qty-barcode').focus();
                $('#qty-barcode').select();
            }

            function onScanFailure(error) {
                // Gagal membaca (kamera terus mencari, abaikan saja)
            }
        });
    </script>
</x-app-layout>
