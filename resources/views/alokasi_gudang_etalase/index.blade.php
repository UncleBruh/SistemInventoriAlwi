<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Alokasi Gudang ke Etalase') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg flex items-center">
                    ✅ {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg flex items-center">
                    ❌ {{ session('error') }}
                </div>
            @endif

            <div class="mb-6 bg-blue-50 p-4 rounded-lg border border-blue-200">
                <p class="text-sm text-blue-700">
                    <strong>📋 Informasi:</strong> Menampilkan riwayat alokasi barang dari gudang ke etalase.
                    Gunakan untuk tracking stok yang telah dialokasikan.
                </p>
            </div>

            @if($data->count() > 0)
                <div class="grid grid-cols-1 gap-4">
                    @foreach($data as $row)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition">
                        <!-- Header Card -->
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-200">
                            <div class="flex justify-between items-start gap-4">
                                <div class="flex-1">
                                    <h3 class="text-lg font-bold text-gray-800">{{ $row->makanan->nama_makanan ?? 'N/A' }}</h3>
                                    <p class="text-xs text-gray-500 mt-1">ID Alokasi: #{{ $row->id_alokasi }} • {{ $row->tgl_alokasi->format('d M Y H:i') }}</p>
                                </div>
                                <div class="text-right">
                                    <div class="text-3xl font-bold text-blue-600">{{ $row->jumlah_dialokasi }}</div>
                                    <p class="text-xs text-gray-600 mt-1">pcs dialokasi</p>
                                </div>
                            </div>
                        </div>

                        <!-- Content Card -->
                        <div class="px-6 py-4">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- Gudang Section -->
                                <div class="space-y-2">
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">📦 Stok Gudang</p>
                                    <div class="flex items-center justify-between gap-2">
                                        <div class="flex-1">
                                            <p class="text-xs text-gray-500">Sebelum</p>
                                            <p class="text-2xl font-bold text-gray-700">{{ $row->stok_gudang_sebelum }}</p>
                                        </div>
                                        <div class="text-red-500 text-xl">➖</div>
                                        <div class="flex-1">
                                            <p class="text-xs text-gray-500">Sesudah</p>
                                            <div class="text-2xl font-bold text-red-600 bg-red-50 px-3 py-1 rounded">{{ $row->stok_gudang_sesudah }}</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Arrow -->
                                <div class="flex items-center justify-center md:flex-col">
                                    <div class="hidden md:block text-3xl text-blue-400 text-center">🔄</div>
                                    <div class="md:hidden text-2xl text-blue-400">➡️</div>
                                </div>

                                <!-- Etalase Section -->
                                <div class="space-y-2">
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">🏪 Stok Etalase</p>
                                    <div class="flex items-center justify-between gap-2">
                                        <div class="flex-1">
                                            <p class="text-xs text-gray-500">Sebelum</p>
                                            <p class="text-2xl font-bold text-gray-700">{{ $row->stok_etalase_sebelum }}</p>
                                        </div>
                                        <div class="text-green-500 text-xl">➕</div>
                                        <div class="flex-1">
                                            <p class="text-xs text-gray-500">Sesudah</p>
                                            <div class="text-2xl font-bold text-green-600 bg-green-50 px-3 py-1 rounded">{{ $row->stok_etalase_sesudah }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Footer -->
                            <div class="mt-4 pt-4 border-t border-gray-200 flex justify-between items-center">
                                <div class="text-sm text-gray-600">
                                    <span class="font-medium">Petugas:</span> {{ $row->pengguna->username }}
                                    @if($row->keterangan)
                                    <p class="text-xs text-gray-500 mt-1">Catatan: {{ $row->keterangan }}</p>
                                    @endif
                                </div>
                                <a href="{{ route('alokasi-gudang-etalase.show', $row->id_alokasi) }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                                    👁️ Detail
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                    <p class="text-4xl mb-4">📭</p>
                    <p class="text-lg font-medium text-gray-800 mb-2">Belum Ada Alokasi</p>
                    <p class="text-gray-600 mb-6">Mulai alokasikan barang dari gudang ke etalase untuk mempersiapkan penjualan.</p>
                </div>
            @endif

            <div class="mt-8 flex justify-center">
                <a href="{{ route('alokasi-gudang-etalase.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium shadow-sm transition">
                    ➕ Alokasikan Barang
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
