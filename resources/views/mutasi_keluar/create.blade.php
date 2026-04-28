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
                    <select id="id_makanan" name="id_makanan" class="searchable-select border-gray-300 rounded-md shadow-sm block mt-1 w-full" required autofocus>
                        <option value=""></option>
                        @foreach($makanan as $item)
                            <option value="{{ $item->id_makanan }}" data-barcode="{{ $item->barcode }}">{{ $item->nama_makanan }} (Etalase: {{ $item->stok_etalase }} | Gudang: {{ $item->stok_gudang }})</option>
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

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnScan = document.getElementById('btn-scan');
        const readerDiv = document.getElementById('reader');
        const barcodeInput = document.getElementById('barcode');
        const selectMakanan = document.getElementById('id_makanan');

        let html5QrCode;
        let isScanning = false;

        barcodeInput.addEventListener('input', function() {
            findMakananByBarcode(this.value.trim());
        });

        // Saat Enter di barcode, fokus ke jumlah (jangan submit form)
        barcodeInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                setTimeout(function() {
                    document.getElementById('jumlah_perubahan').focus();
                    document.getElementById('jumlah_perubahan').select();
                }, 200);
            }
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
                    selectMakanan.dispatchEvent(new Event('change'));
                    break;
                }
            }
        }
    });
</script>
