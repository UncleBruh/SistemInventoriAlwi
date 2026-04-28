<x-app-layout>
    <x-slot name="header">
        {{ __('Alokasi Stok: Gudang ➔ Etalase') }}
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

            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                    <strong class="font-bold">Terjadi Kesalahan!</strong>
                    <ul class="list-disc list-inside mt-1 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('alokasi-gudang-etalase.store') }}" method="POST">
                @csrf
                <div class="mb-6 bg-indigo-50 p-4 rounded-lg border border-indigo-200">
                    <p class="text-sm font-medium text-indigo-700">Mode: 🏭 Pemindahan Stok (Internal)</p>
                </div>

                <div class="mb-4">
                    <x-input-label for="barcode" value="Scan / Ketik Barcode" />
                    <div class="flex flex-col sm:flex-row gap-2 items-stretch sm:items-start mt-1">
                        <div class="flex-grow">
                            <x-text-input id="barcode" class="block w-full font-mono text-gray-600 text-sm" type="text" name="barcode" placeholder="Scan atau ketik barcode di sini..." autofocus />
                        </div>
                        <button type="button" id="btn-scan" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md shadow-sm transition text-sm whitespace-nowrap">
                            📷 Scan
                        </button>
                    </div>

                    <div id="reader" style="width: 100%; display: none;" class="mt-3 border-2 border-dashed border-gray-300 rounded-md overflow-hidden max-h-72"></div>
                    <p class="text-xs text-gray-500 mt-1">Pilihan jajanan akan otomatis terisi saat barcode terbaca.</p>
                </div>

                <div class="mb-4">
                    <x-input-label for="id_makanan" value="Pilih Jajanan" />
                    <select id="id_makanan" name="id_makanan" class="searchable-select border-gray-300 rounded-md shadow-sm block mt-1 w-full" required>
                        <option value=""></option>
                        @foreach($makanan as $item)
                            <option value="{{ $item->id_makanan }}" data-barcode="{{ $item->barcode }}">
                                {{ $item->nama_makanan }} (Gudang: {{ $item->stok_gudang }} | Etalase: {{ $item->stok_etalase }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <x-input-label for="tgl_alokasi" value="Tanggal Pemindahan" />
                    <x-text-input id="tgl_alokasi" class="block mt-1 w-full text-gray-700 font-medium" type="date" name="tgl_alokasi" value="{{ date('Y-m-d') }}" required />
                </div>

                <div class="mb-4">
                    <x-input-label for="jumlah" value="Jumlah yang Dipindahkan (Pcs)" />
                    <x-text-input id="jumlah" class="block mt-1 w-full text-center text-2xl font-bold" type="number" min="1" name="jumlah_dialokasi" value="1" required />
                    <p class="text-xs text-gray-500 mt-1">Stok akan dipotong dari Gudang dan ditambah ke Etalase.</p>
                </div>

                <x-primary-button class="w-full justify-center py-3 text-lg bg-indigo-600 hover:bg-indigo-700">KONFIRMASI PEMINDAHAN STOK</x-primary-button>
            </form>
        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnScan = document.getElementById('btn-scan');
            const readerDiv = document.getElementById('reader');
            const barcodeInput = document.getElementById('barcode');
            const selectMakanan = document.getElementById('id_makanan');
            const jumlahInput = document.getElementById('jumlah'); // Tangkap elemen jumlah

            let html5QrCode;
            let isScanning = false;

            selectMakanan.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const barcode = selectedOption ? selectedOption.getAttribute('data-barcode') : null;
                
                if (barcode) {
                    barcodeInput.value = barcode;
                } else {
                    barcodeInput.value = '';
                }
            });

            barcodeInput.addEventListener('input', function() {
                findMakananByBarcode(this.value.trim());
            });

            barcodeInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    
                    // Tambahkan jeda 200ms dan blok otomatis (select)
                    setTimeout(function() {
                        jumlahInput.focus();
                        jumlahInput.select();
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
                            
                            jumlahInput.focus();
                        }).catch(err => console.error(err));
                    },
                    (errorMessage) => {}
                ).catch((err) => {
                    console.error("Error memulai kamera: ", err);
                    alert("Kamera tidak dapat diakses. Pastikan Anda mengizinkan akses kamera.");

                    readerDiv.style.display = 'none';
                    isScanning = false;
                    btnScan.innerHTML = '📷 Scan';
                    btnScan.classList.replace('bg-red-600', 'bg-blue-600');
                    btnScan.classList.replace('hover:bg-red-700', 'hover:bg-blue-700');
                });
            });

            function findMakananByBarcode(barcode) {
                if (!barcode) {
                    selectMakanan.value = "";
                    if (typeof jQuery !== 'undefined') jQuery(selectMakanan).trigger('change');
                    return;
                }

                let matchFound = false;
                for (let option of selectMakanan.options) {
                    if (option.getAttribute('data-barcode') === barcode) {
                        selectMakanan.value = option.value;
                        
                        selectMakanan.dispatchEvent(new Event('change'));
                        
                        if (typeof jQuery !== 'undefined') {
                            jQuery(selectMakanan).trigger('change');
                        }
                        
                        matchFound = true;
                        break;
                    }
                }

                if (!matchFound) {
                    selectMakanan.value = "";
                    if (typeof jQuery !== 'undefined') jQuery(selectMakanan).trigger('change');
                }
            }
        });
    </script>
</x-app-layout>