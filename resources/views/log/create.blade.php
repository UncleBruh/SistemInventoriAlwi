<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $type == 'masuk' ? __('Pencatatan Barang Masuk') : __('Pencatatan Barang Keluar') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="flex border-b border-gray-200 mb-6">
                    <button type="button" class="tab-button py-2 px-4 font-semibold text-indigo-600 border-b-2 border-indigo-600" data-tab="manual">
                        📝 Input Manual
                    </button>
                    <button type="button" class="tab-button py-2 px-4 font-semibold text-gray-600 border-b-2 border-transparent hover:text-indigo-600" data-tab="barcode">
                        📱 Scan Barcode
                    </button>
                </div>

                <form action="{{ $type == 'masuk' ? route('log.store') : route('log.keluar.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <p class="text-sm font-medium text-gray-700">
                            Mode Aktivitas Saat Ini: 
                            <span class="text-lg ml-2 font-bold {{ $type == 'masuk' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $type == 'masuk' ? '➕ Tambah Stok (Barang Masuk)' : '➖ Kurangi Stok (Barang Keluar)' }}
                            </span>
                        </p>
                    </div>

                    <div id="manual-tab" class="tab-content">
                        <div class="mb-4">
                            <x-input-label for="id_makanan" value="Pilih Jajanan (Bisa cari dari Barcode/Nama)" />
                            <select id="id_makanan" name="id_makanan" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" required autofocus>
                                <option value="">-- Pilih Jajanan --</option>
                                @foreach($makanan as $item)
                                    <option value="{{ $item->id_makanan }}">
                                        {{ $item->barcode ? '['.$item->barcode.'] ' : '' }}{{ $item->nama_makanan }} (Sisa Stok: {{ $item->stok }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('id_makanan')" class="mt-2" />
                        </div>
                    </div>

                    <div id="barcode-tab" class="tab-content hidden">
                        <div class="mb-4 border-2 border-dashed border-gray-300 rounded-lg p-4">
                            <div class="text-center mb-4">
                                <p class="text-gray-600 mb-3">Arahkan kamera ke barcode untuk scanning:</p>
                                <div id="scanner-container" class="bg-gray-900 rounded-lg overflow-hidden" style="max-height: 400px;">
                                    <video id="video" style="width: 100%; height: 100%;"></video>
                                </div>
                            </div>

                            <div class="flex justify-center gap-2 mb-4">
                                <button type="button" id="btn-start-scan" class="bg-indigo-500 hover:bg-indigo-600 text-white px-6 py-2 rounded font-semibold transition">
                                    ▶ Mulai Scan
                                </button>
                                <button type="button" id="btn-stop-scan" class="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded font-semibold hidden transition">
                                    ⏹ Hentikan Scan
                                </button>
                            </div>

                            <div id="scan-result" class="hidden bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded">
                                <strong>Hasil Scan:</strong>
                                <p id="scan-barcode-value" class="font-mono text-lg mt-2"></p>
                                <button type="button" id="btn-use-scanned" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded font-semibold mt-3">
                                    ✓ Gunakan Produk Ini
                                </button>
                            </div>
                        </div>

                        <div class="mb-4 p-4 bg-gray-100 rounded">
                            <p class="text-sm text-gray-700">
                                <strong>Tips:</strong> Pastikan barcode terlihat jelas dan cukup terang untuk hasil scanning yang optimal.
                            </p>
                        </div>
                    </div>

                    <div id="product-info" class="hidden mb-6 p-4 bg-blue-50 border border-blue-200 rounded">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Produk</p>
                                <p id="info-nama" class="font-semibold text-lg"></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Kategori</p>
                                <p id="info-kategori" class="font-semibold"></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Harga</p>
                                <p id="info-harga" class="font-semibold">Rp. <span id="info-harga-val">0</span></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Stok Tersedia</p>
                                <p id="info-stok" class="font-semibold text-green-600"></p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <x-input-label for="jumlah_perubahan" value="Jumlah Pcs" />
                        <x-text-input id="jumlah_perubahan" class="block mt-1 w-full text-2xl font-bold text-center text-gray-700" type="number" min="1" name="jumlah_perubahan" value="1" required />
                        <x-input-error :messages="$errors->get('jumlah_perubahan')" class="mt-2" />
                    </div>

                    @if($type == 'keluar')
                    <div class="mb-6">
                        <x-input-label for="alasan" value="Alasan Pengeluaran Barang (Wajib Diisi)" />
                        <textarea id="alasan" name="alasan" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" rows="3" required placeholder="Contoh: Barang kadaluarsa, rusak, atau dipakai sendiri..."></textarea>
                        <x-input-error :messages="$errors->get('alasan')" class="mt-2" />
                    </div>
                    @endif

                    <div class="flex items-center justify-end mt-4">
                        <button type="submit" class="inline-flex items-center justify-center w-full px-4 py-3 border border-transparent rounded-md font-bold text-lg text-white tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150 {{ $type == 'masuk' ? 'bg-green-600 hover:bg-green-700 focus:ring-green-500' : 'bg-red-600 hover:bg-red-700 focus:ring-red-500' }}">
                            {{ $type == 'masuk' ? 'SIMPAN BARANG MASUK' : 'SIMPAN BARANG KELUAR' }}
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script src="https://serratus.github.io/quaggaJS/quagga.js"></script>
    <script>
        const makananSelect = document.getElementById('id_makanan');
        const barcodeTab = document.getElementById('barcode-tab');
        const manualTab = document.getElementById('manual-tab');
        const productInfo = document.getElementById('product-info');
        const scanResult = document.getElementById('scan-result');
        const tabButtons = document.querySelectorAll('.tab-button');
        let barcodeScanned = null;

        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const tabName = this.dataset.tab;
                document.querySelectorAll('.tab-content').forEach(tab => {
                    tab.classList.add('hidden');
                });
                tabButtons.forEach(btn => {
                    btn.classList.remove('text-indigo-600', 'border-indigo-600');
                    btn.classList.add('text-gray-600', 'border-transparent', 'hover:text-indigo-600');
                });
                document.getElementById(tabName + '-tab').classList.remove('hidden');
                this.classList.remove('text-gray-600', 'border-transparent', 'hover:text-indigo-600');
                this.classList.add('text-indigo-600', 'border-indigo-600');
                if (tabName === 'manual') {
                    stopScanning();
                }
            });
        });

        const startScanning = () => {
            Quagga.init({
                inputStream: { name: "Live", type: "LiveStream", target: document.querySelector('#scanner-container'), constraints: { width: 640, height: 480, facingMode: "environment" } },
                decoder: { readers: ["ean_reader", "ean_8_reader", "code_128_reader", "code_39_reader"] }
            }, function(err) {
                if (err) { alert('Gagal mengakses kamera.'); return; }
                Quagga.start();
                document.getElementById('btn-start-scan').classList.add('hidden');
                document.getElementById('btn-stop-scan').classList.remove('hidden');
            });
            Quagga.onDetected(onBarcodeDetected);
        };

        const stopScanning = () => {
            if (Quagga) { try { Quagga.stop(); } catch(e) {} }
            document.getElementById('btn-stop-scan').classList.add('hidden');
            document.getElementById('btn-start-scan').classList.remove('hidden');
        };

        const onBarcodeDetected = async (result) => {
            const barcode = result.codeResult.code;
            if (barcodeScanned === barcode) return;
            barcodeScanned = barcode;
            stopScanning();
            document.getElementById('scan-barcode-value').textContent = barcode;
            document.getElementById('scan-result').classList.remove('hidden');

            try {
                const response = await fetch(`/api/makanan/find-by-barcode/${barcode}`);
                const data = await response.json();
                if (data.success) {
                    showProductInfo(data);
                    makananSelect.value = data.id_makanan;
                } else {
                    alert('❌ Barcode tidak ditemukan!');
                    barcodeScanned = null;
                    startScanning();
                }
            } catch (error) {
                alert('⚠️ Terjadi kesalahan pencarian API');
                barcodeScanned = null;
                startScanning();
            }
        };

        const showProductInfo = (data) => {
            document.getElementById('info-nama').textContent = data.nama_makanan;
            document.getElementById('info-kategori').textContent = data.jenis_makanan || '-';
            const harga = data.harga ? data.harga : 0;
            document.getElementById('info-harga-val').textContent = harga.toLocaleString('id-ID');
            document.getElementById('info-stok').textContent = data.stok + ' pcs';
            document.getElementById('product-info').classList.remove('hidden');
        };

        document.getElementById('btn-start-scan').addEventListener('click', (e) => { e.preventDefault(); barcodeScanned = null; scanResult.classList.add('hidden'); startScanning(); });
        document.getElementById('btn-stop-scan').addEventListener('click', (e) => { e.preventDefault(); stopScanning(); });
        document.getElementById('btn-use-scanned').addEventListener('click', (e) => {
            e.preventDefault();
            if (makananSelect.value) {
                document.getElementById('jumlah_perubahan').focus();
            }
        });

        makananSelect.addEventListener('change', function() {
            if (this.value) {
                productInfo.classList.remove('hidden');
                const text = this.options[this.selectedIndex].textContent;
                const nameMatch = text.match(/\]? \s*(.+?) \(/) || text.match(/(.+?) \(/);
                const stockMatch = text.match(/Sisa Stok: (\d+)/);
                if (nameMatch) document.getElementById('info-nama').textContent = nameMatch[1].replace(/^\[.*?\]\s*/, '').trim();
                if (stockMatch) document.getElementById('info-stok').textContent = stockMatch[1] + ' pcs';
                document.getElementById('info-kategori').textContent = "-";
                document.getElementById('info-harga-val').textContent = "-";
            } else {
                productInfo.classList.add('hidden');
            }
        });
    </script>
</x-app-layout>