<x-app-layout>
    <x-slot name="header">
        {{ __('Pencatatan Barang Masuk') }}
    </x-slot>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 sm:p-6">

            @if(session('success'))
                <div class="mb-4 p-3 sm:p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg text-xs sm:text-sm flex items-start gap-2">
                    <span>✅</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-3 sm:p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg text-xs sm:text-sm flex items-start gap-2">
                    <span>❌</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <form action="{{ route('mutasi_masuk.store') }}" method="POST">
                @csrf
                <div class="mb-6 bg-green-50 p-3 sm:p-4 rounded-lg border border-green-200">
                    <p class="text-xs sm:text-sm font-medium text-green-700">Mode: ➕ Tambah Stok (Barang Masuk)</p>
                </div>

                <div class="mb-4">
                    <x-input-label for="barcode" value="Barcode (Opsional - Bisa di-scan)" />
                    <div class="flex flex-col sm:flex-row gap-2 items-stretch sm:items-start mt-1">
                        <div class="flex-grow">
                            <x-text-input id="barcode" class="block w-full font-mono text-gray-600 text-sm" type="text" name="barcode_scan" value="" placeholder="Scan barcode untuk mencari jajanan..." />
                        </div>
                        <button type="button" id="btn-scan" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md shadow-sm transition text-sm whitespace-nowrap">
                            📷 Scan
                        </button>
                    </div>

                    <div id="reader" style="width: 100%; display: none;" class="mt-3 border-2 border-dashed border-gray-300 rounded-md overflow-hidden max-h-72"></div>
                </div>

                <div class="mb-4">
                    <x-input-label for="id_makanan" value="Pilih Jajanan" />
                    <select id="id_makanan" name="id_makanan" class="searchable-select border-gray-300 rounded-md shadow-sm block mt-1 w-full text-sm py-2 px-3" required autofocus>
                        <option value=""></option>
                        @foreach($makanan as $item)
                            <option value="{{ $item->id_makanan }}" data-barcode="{{ $item->barcode }}">{{ $item->nama_makanan }} (Stok: {{ $item->stok }} | Gudang: {{ $item->stok_gudang }} | Etalase: {{ $item->stok_etalase }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <x-input-label for="tgl_mutasi" value="Tanggal Aktual Barang Datang" />
                    <x-text-input id="tgl_mutasi" class="block mt-1 w-full text-gray-700 font-medium text-sm" type="date" name="tgl_mutasi" value="{{ date('Y-m-d') }}" required />
                    <p class="text-xs text-gray-500 mt-1">Isi dengan tanggal kapan fisik barang benar-benar tiba di gudang.</p>
                </div>

                <div class="mb-4">
                    <x-input-label for="lokasi_tujuan" value="Lokasi Tujuan Barang" />
                    <div class="grid grid-cols-1 gap-3 mt-2">
                        <label class="flex items-center w-full p-3 sm:p-4 border-2 border-blue-500 rounded-lg cursor-pointer hover:bg-blue-50 transition text-sm">
                            <input type="radio" name="lokasi_tujuan" value="gudang" class="mr-3" checked required />
                            <span class="font-medium">📦 Masuk ke Gudang</span>
                        </label>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">
                        <strong>Gudang:</strong> Stok keseluruhan barang (aset utama)
                    </p>
                </div>

                <div class="mb-6">
                    <x-input-label for="jumlah_perubahan" value="Jumlah Masuk (Pcs)" />
                    <x-text-input id="jumlah_perubahan" class="block mt-1 w-full text-center text-xl sm:text-2xl font-bold" type="number" min="1" name="jumlah_perubahan" value="1" required />
                </div>

                <x-primary-button class="w-full justify-center py-2 sm:py-3 text-base bg-green-600 hover:bg-green-700">SIMPAN BARANG MASUK</x-primary-button>
            </form>
        </div>
    </div>
</x-app-layout>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnScan = document.getElementById('btn-scan');
        const readerDiv = document.getElementById('reader');
        const barcodeInput = document.getElementById('barcode');
        const selectMakanan = document.getElementById('id_makanan');

        let html5QrCode;
        let isScanning = false;

        // Event listener untuk manual barcode input
        barcodeInput.addEventListener('change', function() {
            findMakananByBarcode(this.value);
        });

        btnScan.addEventListener('click', function() {
            if (isScanning) {
                html5QrCode.stop().then(() => {
                    readerDiv.style.display = 'none';
                    isScanning = false;
                    btnScan.innerHTML = '📷 Scan';
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
                    barcodeInput.value = decodedText;
                    findMakananByBarcode(decodedText);

                    html5QrCode.stop().then(() => {
                        readerDiv.style.display = 'none';
                        isScanning = false;
                        btnScan.innerHTML = '📷 Scan';
                        btnScan.classList.replace('bg-red-600', 'bg-blue-600');
                        btnScan.classList.replace('hover:bg-red-700', 'hover:bg-blue-700');

                        barcodeInput.classList.add('bg-green-100');
                        setTimeout(() => barcodeInput.classList.remove('bg-green-100'), 1500);
                    }).catch(err => console.error(err));
                },
                (errorMessage) => {}
            ).catch((err) => {
                console.error("Error memulai kamera: ", err);
                alert("Kamera tidak dapat diakses. Pastikan Anda mengizinkan akses kamera dan menggunakan koneksi HTTPS atau localhost.");

                readerDiv.style.display = 'none';
                isScanning = false;
                btnScan.innerHTML = '📷 Scan';
                btnScan.classList.replace('bg-red-600', 'bg-blue-600');
                btnScan.classList.replace('hover:bg-red-700', 'hover:bg-blue-700');
            });
        });

        function findMakananByBarcode(barcode) {
            if (!barcode) return;

            for (let option of selectMakanan.options) {
                if (option.getAttribute('data-barcode') === barcode) {
                    selectMakanan.value = option.value;
                    // Trigger change event untuk update Select2 jika ada
                    selectMakanan.dispatchEvent(new Event('change'));
                    break;
                }
            }
        }
    });
</script>
