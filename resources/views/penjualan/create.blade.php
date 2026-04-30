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

    <div class="py-6">
        <div class="max-w-full px-4 sm:px-6 lg:px-8 mx-auto flex flex-col lg:flex-row gap-6">
            
            <!-- BAGIAN KIRI: Form Input/Scan Barang -->
            <div class="w-full lg:w-1/3 bg-white shadow-sm sm:rounded-lg p-6 h-fit border-t-4 border-blue-500">
                
                <!-- 1. FORM BARCODE & KAMERA -->
                <h3 class="text-lg font-bold text-gray-700 mb-4 border-b pb-2">1. Scan Barcode</h3>
                
                <!-- Area Kamera (Disembunyikan secara default) -->
                <div id="reader" class="mb-4 hidden rounded-lg overflow-hidden border-2 border-dashed border-gray-300"></div>
                
                <!-- Tombol Buka Kamera -->
                <button type="button" id="btn-scan-kamera" class="w-full mb-4 bg-gray-800 hover:bg-gray-900 text-white font-bold py-2 px-4 rounded shadow flex justify-center items-center gap-2">
                    📷 Nyalakan Kamera HP
                </button>
                <button type="button" id="btn-tutup-kamera" class="w-full mb-4 bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded shadow justify-center items-center gap-2 hidden">
                    ❌ Matikan Kamera
                </button>

                <form action="{{ route('penjualan.keranjang.tambah') }}" method="POST" id="form-barcode" class="mb-6">
                    @csrf
                    <div class="mb-2">
                        <input type="text" name="barcode" id="barcode" autofocus placeholder="Gunakan Alat Scan / Kamera..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-center font-bold text-lg bg-blue-50">
                    </div>
                    <input type="hidden" name="jumlah" value="1">
                    <button type="submit" class="hidden">Submit</button>
                    <p class="text-xs text-gray-500 text-center mt-1">Tekan Enter jika mengetik manual</p>
                </form>

                <!-- 2. FORM MANUAL SEARCH -->
                <h3 class="text-lg font-bold text-gray-700 mb-4 border-b pb-2">2. Atau Cari Manual</h3>
                <form action="{{ route('penjualan.keranjang.tambah') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="id_makanan" class="block text-sm font-medium text-gray-700">Ketik Nama Jajanan</label>
                        <select name="id_makanan" id="id_makanan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm select2-dropdown">
                            <option value="" disabled selected>-- Ketik/Pilih Jajanan --</option>
                            @foreach($makanan as $item)
                                <option value="{{ $item->id_makanan }}">
                                    {{ $item->nama_makanan }} (Stok: {{ $item->stok_etalase }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="jumlah" class="block text-sm font-medium text-gray-700">Jumlah Qty</label>
                        <input type="number" name="jumlah" id="jumlah" value="1" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg shadow-md transition-all">
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

                // Otomatis tekan Enter / Submit form
                $('#form-barcode').submit();
            }

            function onScanFailure(error) {
                // Gagal membaca (kamera terus mencari, abaikan saja)
            }
        });
    </script>
</x-app-layout>