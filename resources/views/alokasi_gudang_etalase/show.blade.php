<x-app-layout>
    <x-slot name="header">
        {{ __('Detail Alokasi Barang') }}
    </x-slot>

    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

            <div class="mb-6 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">
                    Alokasi ID: #{{ $alokasi->id_alokasi }}
                </h3>
                <a href="{{ route('alokasi-gudang-etalase.index') }}" class="text-blue-600 hover:text-blue-900">
                    ← Kembali ke Daftar
                </a>
            </div>

            <!-- Status Info -->
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="grid grid-cols-3 gap-4">
                    <div class="text-center">
                        <p class="text-gray-600 text-sm">Tanggal Alokasi</p>
                        <p class="text-lg font-bold text-blue-600">{{ $alokasi->tgl_alokasi->format('d-m-Y H:i') }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-gray-600 text-sm">Petugas</p>
                        <p class="text-lg font-bold text-blue-600">{{ $alokasi->pengguna->username }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-gray-600 text-sm">Status</p>
                        <p class="text-lg font-bold text-green-600">✅ Selesai</p>
                    </div>
                </div>
            </div>

            <!-- Barang Info -->
            <div class="mb-6">
                <h4 class="text-md font-semibold text-gray-800 mb-4">📦 Informasi Barang</h4>
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-600 text-sm">Nama Barang</p>
                            <p class="text-lg font-bold">{{ $alokasi->makanan->nama_makanan ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Jumlah Dialokasi</p>
                            <p class="text-lg font-bold text-blue-600">{{ $alokasi->jumlah_dialokasi }} pcs</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stok Gudang -->
            <div class="mb-6">
                <h4 class="text-md font-semibold text-gray-800 mb-4">🏭 Perubahan Stok Gudang</h4>
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-gray-100 p-4 rounded-lg border-l-4 border-l-gray-600">
                        <p class="text-gray-600 text-sm">Stok Sebelum</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $alokasi->stok_gudang_sebelum }}</p>
                        <p class="text-xs text-gray-500 mt-1">pcs</p>
                    </div>
                    <div class="flex items-center justify-center">
                        <p class="text-2xl font-bold text-red-600">➖ {{ $alokasi->jumlah_dialokasi }}</p>
                    </div>
                    <div class="bg-red-100 p-4 rounded-lg border-l-4 border-l-red-600">
                        <p class="text-gray-600 text-sm">Stok Sesudah</p>
                        <p class="text-2xl font-bold text-red-800">{{ $alokasi->stok_gudang_sesudah }}</p>
                        <p class="text-xs text-gray-500 mt-1">pcs</p>
                    </div>
                </div>
            </div>

            <!-- Stok Etalase -->
            <div class="mb-6">
                <h4 class="text-md font-semibold text-gray-800 mb-4">🏪 Perubahan Stok Etalase</h4>
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-gray-100 p-4 rounded-lg border-l-4 border-l-gray-600">
                        <p class="text-gray-600 text-sm">Stok Sebelum</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $alokasi->stok_etalase_sebelum }}</p>
                        <p class="text-xs text-gray-500 mt-1">pcs</p>
                    </div>
                    <div class="flex items-center justify-center">
                        <p class="text-2xl font-bold text-green-600">➕ {{ $alokasi->jumlah_dialokasi }}</p>
                    </div>
                    <div class="bg-green-100 p-4 rounded-lg border-l-4 border-l-green-600">
                        <p class="text-gray-600 text-sm">Stok Sesudah</p>
                        <p class="text-2xl font-bold text-green-800">{{ $alokasi->stok_etalase_sesudah }}</p>
                        <p class="text-xs text-gray-500 mt-1">pcs</p>
                    </div>
                </div>
            </div>

            <!-- Keterangan -->
            @if($alokasi->keterangan)
            <div class="mb-6">
                <h4 class="text-md font-semibold text-gray-800 mb-2">📝 Keterangan</h4>
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <p class="text-gray-700">{{ $alokasi->keterangan }}</p>
                </div>
            </div>
            @endif

            <!-- Timeline -->
            <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                <h4 class="text-md font-semibold text-gray-800 mb-4">📅 Timeline</h4>
                <div class="space-y-2 text-sm text-gray-600">
                    <p>📍 Dibuat: {{ $alokasi->created_at->format('d-m-Y H:i:s') }}</p>
                    <p>📍 Diupdate: {{ $alokasi->updated_at->format('d-m-Y H:i:s') }}</p>
                </div>
            </div>

            <div class="flex gap-4">
                <a href="{{ route('alokasi-gudang-etalase.index') }}" class="flex-1 flex items-center justify-center py-3 text-lg bg-gray-600 hover:bg-gray-700 text-white font-bold rounded-lg">
                    ← Kembali
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
