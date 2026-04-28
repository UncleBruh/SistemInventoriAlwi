<x-app-layout>
    <x-slot name="header">
        {{ __('Detail Alokasi Barang') }}
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 sm:p-6">

                <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <h3 class="text-lg sm:text-xl font-semibold text-gray-800">
                        Alokasi ID: #{{ $alokasi->id_alokasi }}
                    </h3>
                    <a href="{{ route('alokasi-gudang-etalase.index') }}" class="text-blue-600 hover:text-blue-900 text-sm sm:text-base">
                        ← Kembali ke Daftar
                    </a>
                </div>

                <!-- Status Info -->
                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="sm:text-center">
                            <p class="text-gray-600 text-xs sm:text-sm">Tanggal Alokasi</p>
                            <p class="text-base sm:text-lg font-bold text-blue-600 mt-1">{{ $alokasi->tgl_alokasi->format('d-m-Y H:i') }}</p>
                        </div>
                        <div class="sm:text-center">
                            <p class="text-gray-600 text-xs sm:text-sm">Petugas</p>
                            <p class="text-base sm:text-lg font-bold text-blue-600 mt-1">
                                @if($alokasi->pengguna)
                                    {{ $alokasi->pengguna->username }}
                                @else
                                    <span class="text-gray-400 italic">-</span>
                                @endif
                            </p>
                        </div>
                        <div class="sm:text-center">
                            <p class="text-gray-600 text-xs sm:text-sm">Status</p>
                            <p class="text-base sm:text-lg font-bold text-green-600 mt-1">✅ Selesai</p>
                        </div>
                    </div>
                </div>

                <!-- Barang Info -->
                <div class="mb-6">
                    <h4 class="text-base sm:text-md font-semibold text-gray-800 mb-4">📦 Informasi Barang</h4>
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <p class="text-gray-600 text-xs sm:text-sm">Nama Barang</p>
                                <p class="text-base sm:text-lg font-bold mt-1">{{ $alokasi->makanan->nama_makanan ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600 text-xs sm:text-sm">Jumlah Dialokasi</p>
                                <p class="text-base sm:text-lg font-bold text-blue-600 mt-1">{{ $alokasi->jumlah_dialokasi }} pcs</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stok Gudang -->
                <div class="mb-6">
                    <h4 class="text-base sm:text-md font-semibold text-gray-800 mb-4">🏭 Perubahan Stok Gudang</h4>
                    <div class="space-y-3 md:space-y-0 md:grid md:grid-cols-3 md:gap-4">
                        <!-- Mobile: Full width -->
                        <div class="md:hidden bg-gray-100 p-4 rounded-lg border-l-4 border-l-gray-600">
                            <p class="text-gray-600 text-xs">Stok Sebelum</p>
                            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $alokasi->stok_gudang_sebelum }}</p>
                            <p class="text-xs text-gray-500 mt-1">pcs</p>
                        </div>
                        <div class="md:hidden flex items-center justify-center py-2">
                            <p class="text-xl font-bold text-red-600">➖ {{ $alokasi->jumlah_dialokasi }}</p>
                        </div>
                        <div class="md:hidden bg-red-100 p-4 rounded-lg border-l-4 border-l-red-600">
                            <p class="text-gray-600 text-xs">Stok Sesudah</p>
                            <p class="text-2xl font-bold text-red-800 mt-1">{{ $alokasi->stok_gudang_sesudah }}</p>
                            <p class="text-xs text-gray-500 mt-1">pcs</p>
                        </div>

                        <!-- Desktop: 3 columns -->
                        <div class="hidden md:block bg-gray-100 p-4 rounded-lg border-l-4 border-l-gray-600">
                            <p class="text-gray-600 text-sm">Stok Sebelum</p>
                            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $alokasi->stok_gudang_sebelum }}</p>
                            <p class="text-xs text-gray-500 mt-1">pcs</p>
                        </div>
                        <div class="hidden md:flex md:items-center md:justify-center">
                            <p class="text-2xl font-bold text-red-600">➖ {{ $alokasi->jumlah_dialokasi }}</p>
                        </div>
                        <div class="hidden md:block bg-red-100 p-4 rounded-lg border-l-4 border-l-red-600">
                            <p class="text-gray-600 text-sm">Stok Sesudah</p>
                            <p class="text-2xl font-bold text-red-800 mt-1">{{ $alokasi->stok_gudang_sesudah }}</p>
                            <p class="text-xs text-gray-500 mt-1">pcs</p>
                        </div>
                    </div>
                </div>

                <!-- Stok Etalase -->
                <div class="mb-6">
                    <h4 class="text-base sm:text-md font-semibold text-gray-800 mb-4">🏪 Perubahan Stok Etalase</h4>
                    <div class="space-y-3 md:space-y-0 md:grid md:grid-cols-3 md:gap-4">
                        <!-- Mobile: Full width -->
                        <div class="md:hidden bg-gray-100 p-4 rounded-lg border-l-4 border-l-gray-600">
                            <p class="text-gray-600 text-xs">Stok Sebelum</p>
                            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $alokasi->stok_etalase_sebelum }}</p>
                            <p class="text-xs text-gray-500 mt-1">pcs</p>
                        </div>
                        <div class="md:hidden flex items-center justify-center py-2">
                            <p class="text-xl font-bold text-green-600">➕ {{ $alokasi->jumlah_dialokasi }}</p>
                        </div>
                        <div class="md:hidden bg-green-100 p-4 rounded-lg border-l-4 border-l-green-600">
                            <p class="text-gray-600 text-xs">Stok Sesudah</p>
                            <p class="text-2xl font-bold text-green-800 mt-1">{{ $alokasi->stok_etalase_sesudah }}</p>
                            <p class="text-xs text-gray-500 mt-1">pcs</p>
                        </div>

                        <!-- Desktop: 3 columns -->
                        <div class="hidden md:block bg-gray-100 p-4 rounded-lg border-l-4 border-l-gray-600">
                            <p class="text-gray-600 text-sm">Stok Sebelum</p>
                            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $alokasi->stok_etalase_sebelum }}</p>
                            <p class="text-xs text-gray-500 mt-1">pcs</p>
                        </div>
                        <div class="hidden md:flex md:items-center md:justify-center">
                            <p class="text-2xl font-bold text-green-600">➕ {{ $alokasi->jumlah_dialokasi }}</p>
                        </div>
                        <div class="hidden md:block bg-green-100 p-4 rounded-lg border-l-4 border-l-green-600">
                            <p class="text-gray-600 text-sm">Stok Sesudah</p>
                            <p class="text-2xl font-bold text-green-800 mt-1">{{ $alokasi->stok_etalase_sesudah }}</p>
                            <p class="text-xs text-gray-500 mt-1">pcs</p>
                        </div>
                    </div>
                </div>

                <!-- Keterangan -->
                @if($alokasi->keterangan)
                <div class="mb-6">
                    <h4 class="text-base sm:text-md font-semibold text-gray-800 mb-2">📝 Keterangan</h4>
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                        <p class="text-gray-700 text-sm sm:text-base">{{ $alokasi->keterangan }}</p>
                    </div>
                </div>
                @endif

                <!-- Timeline -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <h4 class="text-base sm:text-md font-semibold text-gray-800 mb-4">📅 Timeline</h4>
                    <div class="space-y-2 text-xs sm:text-sm text-gray-600">
                        <p>📍 Dibuat: {{ $alokasi->created_at->format('d-m-Y H:i:s') }}</p>
                        <p>📍 Diupdate: {{ $alokasi->updated_at->format('d-m-Y H:i:s') }}</p>
                    </div>
                </div>

                <div class="flex gap-4">
                    <a href="{{ route('alokasi-gudang-etalase.index') }}" class="flex-1 flex items-center justify-center py-2 sm:py-3 text-base sm:text-lg bg-gray-600 hover:bg-gray-700 text-white font-bold rounded-lg transition">
                        ← Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
