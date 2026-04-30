<x-app-layout>
    <x-slot name="header">
        {{ __('Pencatatan Pengeluaran Gudang') }}
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

            <form action="{{ route('pengeluaran_gudang.store') }}" method="POST">
                @csrf
                <div class="mb-6 bg-orange-50 p-4 rounded-lg border border-orange-200">
                    <p class="text-sm font-medium text-orange-700">Mode: 🏭 Pengeluaran Stok Gudang</p>
                    <p class="text-xs text-orange-600 mt-2">💡 Pengeluaran gudang HANYA akan mengurangi stok GUDANG (untuk barang rusak, expired, digigit tikus, dll), bukan etalase.</p>
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
                            <option value="{{ $item->id_makanan }}" data-barcode="{{ $item->barcode }}">{{ $item->nama_makanan }} (Gudang: {{ $item->stok_gudang }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <x-input-label for="tgl_pengeluaran" value="Tanggal Pengeluaran Gudang" />
                    <x-text-input id="tgl_pengeluaran" class="block mt-1 w-full text-gray-700 font-medium" type="date" name="tgl_pengeluaran" value="{{ date('Y-m-d') }}" required />
                </div>

                <div class="mb-4">
                    <x-input-label for="jumlah_keluar" value="Jumlah Keluar (Pcs)" />
                    <x-text-input id="jumlah_keluar" class="block mt-1 w-full text-center text-2xl font-bold" type="number" min="1" name="jumlah_keluar" value="1" required />
                </div>

                <div class="mb-4">
                    <x-input-label for="alasan" value="Alasan Pengeluaran" />
                    <div class="grid grid-cols-2 gap-2 mt-2">
                        <label class="flex items-center p-2 border-2 border-gray-300 rounded-lg cursor-pointer hover:bg-red-50 transition" style="border-color: #ef4444;">
                            <input type="radio" name="alasan" value="expired" class="mr-2" required />
                            <span class="text-sm font-medium">📅 Expired</span>
                        </label>
                        <label class="flex items-center p-2 border-2 border-gray-300 rounded-lg cursor-pointer hover:bg-yellow-50 transition" style="border-color: #eab308;">
                            <input type="radio" name="alasan" value="keperluan_prive" class="mr-2" required />
                            <span class="text-sm font-medium">🛍️ Keperluan Prive</span>
                        </label>
                        <label class="flex items-center p-2 border-2 border-gray-300 rounded-lg cursor-pointer hover:bg-orange-50 transition" style="border-color: #f97316;">
                            <input type="radio" name="alasan" value="rusak" class="mr-2" required />
                            <span class="text-sm font-medium">🔨 Rusak</span>
                        </label>
                        <label class="flex items-center p-2 border-2 border-gray-300 rounded-lg cursor-pointer hover:bg-purple-50 transition" style="border-color: #a855f7;">
                            <input type="radio" name="alasan" value="lainnya" class="mr-2" required />
                            <span class="text-sm font-medium">📋 Lainnya</span>
                        </label>
                    </div>
                </div>

                <div class="mb-6">
                    <x-input-label for="keterangan" value="Keterangan Detail (Opsional)" />
                    <textarea id="keterangan" name="keterangan" rows="3" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" placeholder="Jelaskan detail kerusakan atau alasan pengeluaran... (contoh: Bungkus sobek, udara masuk, aroma berubah)"></textarea>
                </div>

                <x-primary-button class="w-full justify-center py-3 text-lg bg-orange-600 hover:bg-orange-700">SIMPAN PENGELUARAN GUDANG</x-primary-button>
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

        // Saat Enter di barcode, fokus ke jumlah
        barcodeInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                setTimeout(function() {
                    document.getElementById('jumlah_keluar').focus();
                    document.getElementById('jumlah_keluar').select();
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
