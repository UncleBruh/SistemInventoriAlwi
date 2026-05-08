<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg sm:text-xl text-gray-800 leading-tight">{{ __('Detail Retur') }}</h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Back Button -->
            <a href="{{ route('retur.index') }}" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 text-sm font-medium mb-6">
                ← Kembali ke List Retur
            </a>

            <!-- Main Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-4 sm:px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-between items-start gap-4">
                        <div class="flex-1">
                            <h1 class="text-xl sm:text-2xl font-bold text-gray-800 mb-2">
                                {{ $retur->makanan->nama_makanan ?? 'Produk Terhapus' }}
                            </h1>
                            <p class="text-sm text-gray-600">
                                Retur #{{ $retur->id_retur }} • {{ $retur->tgl_retur->format('d M Y H:i') }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-600 mb-1">Total Retur</p>
                            <p class="text-3xl font-bold text-red-600">
                                Rp {{ number_format($retur->total_retur, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-4 sm:p-6 space-y-6">
                    <!-- Informasi Transaksi Penjualan -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">Informasi Transaksi</h3>
                            <div class="space-y-3 text-sm">
                                <div>
                                    <p class="text-gray-600">No Nota</p>
                                    <p class="font-mono font-bold text-gray-800">{{ $retur->penjualan->no_nota ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Tanggal Penjualan</p>
                                    <p class="font-bold text-gray-800">
                                        {{ $retur->penjualan->tanggal_penjualan ? \Carbon\Carbon::parse($retur->penjualan->tanggal_penjualan)->format('d M Y H:i') : '-' }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Total Penjualan (Awal)</p>
                                    <p class="font-bold text-gray-800">Rp {{ number_format($retur->penjualan->total_harga + $retur->total_retur, 0, ',', '.') }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Kasir</p>
                                    <p class="font-bold text-gray-800">{{ $retur->penjualan->pengguna->username ?? '-' }}</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">Informasi Retur</h3>
                            <div class="space-y-3 text-sm">
                                <div>
                                    <p class="text-gray-600">Jumlah Retur</p>
                                    <p class="font-bold text-2xl text-red-600">{{ $retur->jumlah_retur }} pcs</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Harga Satuan</p>
                                    <p class="font-bold text-gray-800">Rp {{ number_format($retur->harga_satuan, 0, ',', '.') }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Alasan Retur</p>
                                    <p class="font-bold text-gray-800 capitalize">{{ ucfirst(str_replace('_', ' ', $retur->alasan_retur)) }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Petugas Input</p>
                                    <p class="font-bold text-gray-800">{{ $retur->pengguna->username ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Keterangan -->
                    @if($retur->keterangan)
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-2">Catatan/Keterangan</h3>
                            <p class="text-gray-800">{{ $retur->keterangan }}</p>
                        </div>
                    @endif

                    <!-- Ringkasan Dampak -->
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                        <h3 class="text-sm font-semibold text-blue-900 uppercase tracking-wide mb-3">Dampak pada Pendapatan</h3>
                        <div class="space-y-2 text-sm">
                            <p class="text-blue-800">
                                <strong>Sebelum Retur:</strong> Rp {{ number_format($retur->penjualan->total_harga + $retur->total_retur, 0, ',', '.') }}
                            </p>
                            <p class="text-red-600 font-bold text-lg">
                                – Rp {{ number_format($retur->total_retur, 0, ',', '.') }} (Retur)
                            </p>
                            <p class="text-blue-800 pt-2 border-t border-blue-200">
                                <strong>Setelah Retur:</strong> Rp {{ number_format($retur->penjualan->total_harga, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>

                    <!-- Stok Update -->
                    <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                        <h3 class="text-sm font-semibold text-green-900 uppercase tracking-wide mb-2">Update Stok Etalase</h3>
                        <p class="text-green-800 text-sm">
                            Stok etalase dari produk <strong>{{ $retur->makanan->nama_makanan ?? 'Produk Terhapus' }}</strong>
                            bertambah <strong>{{ $retur->jumlah_retur }} pcs</strong> karena produk dikembalikan.
                        </p>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="bg-gray-50 px-4 sm:px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('retur.index') }}" class="flex-1 text-center bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium text-sm transition border border-gray-300">
                        ← Kembali
                    </a>

                    @if(Auth::user()->role === 'Pemilik')
                        <a href="{{ route('retur.edit', $retur->id_retur) }}" class="flex-1 text-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium text-sm transition">
                            ✏️ Edit
                        </a>

                        <form action="{{ route('retur.destroy', $retur->id_retur) }}" method="POST" class="flex-1" onsubmit="return confirm('Apakah Anda yakin ingin menghapus retur ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium text-sm transition">
                                🗑️ Hapus Retur
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
