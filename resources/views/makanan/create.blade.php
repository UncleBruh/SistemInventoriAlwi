<x-app-layout>
    <x-slot name="header">
        {{ __('Daftar Jajanan Baru') }}
    </x-slot>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 sm:p-6 border border-gray-200">

            <form action="{{ route('makanan.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <x-input-label for="id_kategori" value="Kategori Jajanan" />
                    <select id="id_kategori" name="id_kategori" class="searchable-select border-gray-300 rounded-md shadow-sm block mt-1 w-full text-sm py-2 px-3" required autofocus>
                        <option value=""></option>
                        @foreach($kategori as $kat)
                            <option value="{{ $kat->id_kategori }}" {{ old('id_kategori') == $kat->id_kategori ? 'selected' : '' }}>
                                {{ $kat->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('id_kategori')" class="mt-2" />
                </div>

                <div class="mb-4">
                    <x-input-label for="barcode" value="Barcode (Opsional - Bisa di-scan)" />
                    <div class="flex flex-col sm:flex-row gap-2 items-stretch sm:items-start mt-1">
                        <div class="flex-grow">
                            <x-text-input id="barcode" class="block w-full font-mono text-gray-600 text-sm" type="text" name="barcode" value="{{ old('barcode') }}" placeholder="Scan atau ketik barcode..." />
                        </div>
                        <button type="button" id="btn-scan" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md shadow-sm transition text-sm whitespace-nowrap">
                            📷 Scan
                        </button>
                    </div>

                    <div id="reader" style="width: 100%; display: none;" class="mt-3 border-2 border-dashed border-gray-300 rounded-md overflow-hidden max-h-72"></div>

                    <x-input-error :messages="$errors->get('barcode')" class="mt-2" />
                </div>

                <div class="mb-4">
                    <x-input-label for="nama_makanan" value="Nama Jajanan" />
                    <x-text-input id="nama_makanan" class="block mt-1 w-full font-medium text-sm" type="text" name="nama_makanan" value="{{ old('nama_makanan') }}" required placeholder="Contoh: Qtela, Chitato, dll" />
                    <x-input-error :messages="$errors->get('nama_makanan')" class="mt-2" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    <div>
                        <x-input-label for="harga" value="Harga Jual (Rp)" />
                        <x-text-input id="harga" class="block mt-1 w-full text-sm" type="number" name="harga" value="{{ old('harga') }}" required min="0" placeholder="0" />
                        <x-input-error :messages="$errors->get('harga')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="stok" value="Stok Awal (Masuk ke Gudang)" />
                        <x-text-input id="stok" class="block mt-1 w-full font-bold text-indigo-700 text-sm" type="number" name="stok" value="{{ old('stok', 0) }}" required min="0" />
                        <p class="text-xs text-gray-500 mt-1">Stok awal akan masuk ke gudang, bukan langsung ke etalase.</p>
                        <x-input-error :messages="$errors->get('stok')" class="mt-2" />
                    </div>
                </div>

                <div class="border-t pt-4 mt-2">
                    <div class="flex flex-col items-center gap-3">
                        <x-primary-button class="bg-indigo-600 hover:bg-indigo-700 text-base px-8 shadow-sm w-full sm:w-auto justify-center">Simpan Jajanan</x-primary-button>
                        <a href="{{ route('makanan.index') }}" class="text-gray-500 hover:text-gray-800 font-medium transition text-sm">⬅ Batal</a>
                    </div>
                </div>
            </form>

        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnScan = document.getElementById('btn-scan');
            const readerDiv = document.getElementById('reader');
            const barcodeInput = document.getElementById('barcode');

            let html5QrCode;
            let isScanning = false;

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
                        // Aksi saat barcode berhasil terbaca
                        barcodeInput.value = decodedText;

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
                    (errorMessage) => {
                    }
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
        });
    </script>
</x-app-layout>
