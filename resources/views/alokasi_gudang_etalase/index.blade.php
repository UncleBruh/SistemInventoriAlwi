<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg sm:text-xl text-gray-800 leading-tight">{{ __('Alokasi Gudang ke Etalase') }}</h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-3 sm:p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg text-sm flex items-start gap-2">
                    <span>✅</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-3 sm:p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg text-sm flex items-start gap-2">
                    <span>❌</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <div class="mb-6 bg-blue-50 p-3 sm:p-4 rounded-lg border border-blue-200 text-xs sm:text-sm">
                <p class="text-blue-700">
                    <strong>📋 Informasi:</strong> Menampilkan riwayat alokasi barang dari gudang ke etalase. Gunakan untuk tracking stok yang telah dialokasikan.
                </p>
            </div>

            <div class="mt-6 mb-6 sm:mt-8 flex justify-center">
                <a href="{{ route('alokasi-gudang-etalase.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg font-medium text-sm sm:text-base shadow-sm transition">
                    ➕ Alokasikan Barang
                </a>
            </div>

            @if($data->count() > 0)
                <div class="grid grid-cols-1 gap-3 sm:gap-4">
                    @foreach($data as $row)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition">
                        <!-- Header Card -->
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200">
                            <div class="flex justify-between items-start gap-3 sm:gap-4">
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-base sm:text-lg font-bold text-gray-800 truncate">{{ $row->makanan->nama_makanan ?? 'N/A' }}</h3>
                                    <p class="text-xs text-gray-500 mt-1">ID Alokasi: #{{ $row->id_alokasi }} • {{ $row->tgl_alokasi->format('d M Y H:i') }}</p>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <div class="text-2xl sm:text-3xl font-bold text-blue-600">{{ $row->jumlah_dialokasi }}</div>
                                    <p class="text-xs text-gray-600 mt-1">pcs dialokasi</p>
                                </div>
                            </div>
                        </div>

                        <!-- Content Card -->
                        <div class="px-4 sm:px-6 py-3 sm:py-4">
                            <div class="grid grid-cols-3 gap-2 sm:gap-4 md:gap-6">
                                <!-- Gudang Section -->
                                <div class="space-y-2">
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">📦 Gudang</p>
                                    <div class="flex items-center justify-between gap-1 sm:gap-2">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs text-gray-500">Sebelum</p>
                                            <p class="text-lg sm:text-2xl font-bold text-gray-700">{{ $row->stok_gudang_sebelum }}</p>
                                        </div>
                                        <div class="text-red-500 text-base sm:text-xl flex-shrink-0">➖</div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs text-gray-500">Sesudah</p>
                                            <div class="text-lg sm:text-2xl font-bold text-red-600 bg-red-50 px-2 sm:px-3 py-1 rounded">{{ $row->stok_gudang_sesudah }}</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Arrow -->
                                <div class="flex items-center justify-center">
                                    <div class="hidden md:block text-3xl text-blue-400 text-center">🔄</div>
                                    <div class="md:hidden text-2xl text-blue-400">➡️</div>
                                </div>

                                <!-- Etalase Section -->
                                <div class="space-y-2">
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">🏪 Etalase</p>
                                    <div class="flex items-center justify-between gap-1 sm:gap-2">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs text-gray-500">Sebelum</p>
                                            <p class="text-lg sm:text-2xl font-bold text-gray-700">{{ $row->stok_etalase_sebelum }}</p>
                                        </div>
                                        <div class="text-green-500 text-base sm:text-xl flex-shrink-0">➕</div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs text-gray-500">Sesudah</p>
                                            <div class="text-lg sm:text-2xl font-bold text-green-600 bg-green-50 px-2 sm:px-3 py-1 rounded">{{ $row->stok_etalase_sesudah }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Footer -->
                            <div class="mt-3 sm:mt-4 pt-3 sm:pt-4 border-t border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                                <div class="text-xs sm:text-sm text-gray-600">
                                    <span class="font-medium">Petugas:</span> {{ $row->pengguna->username }}
                                    @if($row->keterangan)
                                    <p class="text-xs text-gray-500 mt-1">Catatan: {{ $row->keterangan }}</p>
                                    @endif
                                </div>
                                <a href="{{ route('alokasi-gudang-etalase.show', $row->id_alokasi) }}" class="inline-flex items-center gap-1 sm:gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-3 sm:px-4 py-2 rounded-lg text-xs sm:text-sm font-medium transition whitespace-nowrap">
                                    👁️ Detail
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-lg shadow-sm p-8 sm:p-12 text-center">
                    <p class="text-3xl sm:text-4xl mb-4">📭</p>
                    <p class="text-base sm:text-lg font-medium text-gray-800 mb-2">Belum Ada Alokasi</p>
                    <p class="text-sm sm:text-base text-gray-600 mb-6">Mulai alokasikan barang dari gudang ke etalase untuk mempersiapkan penjualan.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
