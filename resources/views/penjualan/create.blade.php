<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kasir / Point of Sale') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- BAGIAN KIRI: Form Input/Scan Barang -->
            <div class="md:col-span-1 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 h-fit">
                <h3 class="text-lg font-bold text-gray-700 mb-4 border-b pb-2">Scan / Pilih Barang</h3>
                
                <form action="{{ route('penjualan.keranjang.tambah') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="id_makanan" class="block text-sm font-medium text-gray-700">Nama Jajanan</label>
                        <!-- Jika pakai barcode scanner, alatnya otomatis mengetik di kolom ini jika diubah ke input text, tapi kita pakai select untuk amannya -->
                        <select name="id_makanan" id="id_makanan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required autofocus>
                            <option value="" disabled selected>-- Pilih Jajanan --</option>
                            @foreach($makanan as $item)
                                <option value="{{ $item->id_makanan }}">{{ $item->nama_makanan }} (Stok Etalase: {{ $item->stok_etalase }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="jumlah" class="block text-sm font-medium text-gray-700">Jumlah</label>
                        <input type="number" name="jumlah" id="jumlah" value="1" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow">
                        + Tambah ke Keranjang (Enter)
                    </button>
                </form>
            </div>

            <!-- BAGIAN KANAN: Daftar Keranjang & Pembayaran -->
            <div class="md:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold text-gray-700 mb-4 border-b pb-2">Keranjang Belanja</h3>
                
                <!-- Tabel Daftar Barang -->
                <div class="overflow-x-auto mb-6">
                    <table class="min-w-full border border-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="py-2 px-3 border-b text-left text-xs font-bold text-gray-700">Jajanan</th>
                                <th class="py-2 px-3 border-b text-center text-xs font-bold text-gray-700">Harga</th>
                                <th class="py-2 px-3 border-b text-center text-xs font-bold text-gray-700">Qty</th>
                                <th class="py-2 px-3 border-b text-right text-xs font-bold text-gray-700">Subtotal</th>
                                <th class="py-2 px-3 border-b text-center text-xs font-bold text-gray-700">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($keranjang as $id => $item)
                                <tr>
                                    <td class="py-2 px-3 border-b text-sm">{{ $item['nama_makanan'] }}</td>
                                    <td class="py-2 px-3 border-b text-center text-sm">Rp {{ number_format($item['harga_satuan'], 0, ',', '.') }}</td>
                                    <td class="py-2 px-3 border-b text-center text-sm">{{ $item['jumlah'] }}</td>
                                    <td class="py-2 px-3 border-b text-right text-sm font-semibold">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                                    <td class="py-2 px-3 border-b text-center">
                                        <form action="{{ route('penjualan.keranjang.hapus', $id) }}" method="POST" onsubmit="return confirm('Hapus barang ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 font-bold">X</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-4 text-center text-gray-500 italic">Keranjang masih kosong. Silakan scan/pilih barang.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Bagian Total & Pembayaran -->
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-xl font-bold text-gray-700">TOTAL:</span>
                        <span class="text-3xl font-black text-green-600">Rp {{ number_format($total_harga, 0, ',', '.') }}</span>
                    </div>

                    <!-- Form Proses Checkout -->
                    <form action="{{ route('penjualan.store') }}" method="POST" class="mt-4 border-t pt-4">
                        @csrf
                        <div class="flex flex-col sm:flex-row gap-4 items-end">
                            <div class="w-full sm:w-2/3">
                                <label for="bayar" class="block text-sm font-bold text-gray-700">Uang Diterima (Rp)</label>
                                <input type="number" name="bayar" id="bayar" min="{{ $total_harga }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-lg font-bold" required placeholder="Contoh: 50000" {{ count($keranjang) == 0 ? 'disabled' : '' }}>
                            </div>
                            <div class="w-full sm:w-1/3">
                                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded shadow text-lg" {{ count($keranjang) == 0 ? 'disabled' : '' }}>
                                    💵 BAYAR
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>