<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg sm:text-xl text-gray-800 leading-tight">{{ __('Edit Retur') }}</h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Back Button -->
            <a href="{{ route('retur.show', $retur->id_retur) }}" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 text-sm font-medium mb-6">
                ← Kembali ke Detail
            </a>

            <!-- Form Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-4 sm:px-6 py-4 border-b border-gray-200">
                    <h1 class="text-lg sm:text-xl font-bold text-gray-800">
                        Edit Retur: {{ $retur->makanan->nama_makanan ?? 'Produk Terhapus' }}
                    </h1>
                </div>

                <!-- Form -->
                <form action="{{ route('retur.update', $retur->id_retur) }}" method="POST" class="p-4 sm:p-6">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <!-- Informasi Dasar (Read-Only) -->
                        <div>
                            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Informasi Retur</h3>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <div>
                                    <label class="text-sm font-medium text-gray-700">ID Retur</label>
                                    <p class="mt-1 text-gray-800 font-mono">{{ $retur->id_retur }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-700">Produk</label>
                                    <p class="mt-1 text-gray-800 font-bold">{{ $retur->makanan->nama_makanan ?? 'Produk Terhapus' }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-700">Jumlah Retur</label>
                                    <p class="mt-1 text-gray-800 font-bold">{{ $retur->jumlah_retur }} pcs</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-700">Total Retur</label>
                                    <p class="mt-1 text-gray-800 font-bold">Rp {{ number_format($retur->total_retur, 0, ',', '.') }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-700">Tanggal Retur</label>
                                    <p class="mt-1 text-gray-800">{{ $retur->tgl_retur->format('d M Y H:i') }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-700">Petugas Input</label>
                                    <p class="mt-1 text-gray-800">{{ $retur->pengguna->username ?? '-' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Edit Fields -->
                        <div>
                            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Edit Informasi</h3>

                            <div class="space-y-4">
                                <!-- Alasan Retur -->
                                <div>
                                    <label for="alasan_retur" class="block text-sm font-medium text-gray-700 mb-2">Alasan Retur *</label>
                                    <select name="alasan_retur" id="alasan_retur" class="w-full border-gray-300 rounded-md shadow-sm px-3 py-2 @error('alasan_retur') border-red-500 @enderror" required>
                                        <option value="">-- Pilih Alasan --</option>
                                        <option value="rusak" {{ $retur->alasan_retur === 'rusak' ? 'selected' : '' }}>Rusak</option>
                                        <option value="expired" {{ $retur->alasan_retur === 'expired' ? 'selected' : '' }}>Expired/Kadaluarsa</option>
                                        <option value="tidak_sesuai" {{ $retur->alasan_retur === 'tidak_sesuai' ? 'selected' : '' }}>Tidak Sesuai Pesanan</option>
                                        <option value="salah_kirim" {{ $retur->alasan_retur === 'salah_kirim' ? 'selected' : '' }}>Salah Kirim</option>
                                        <option value="lainnya" {{ $retur->alasan_retur === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                                    </select>
                                    @error('alasan_retur')
                                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Keterangan -->
                                <div>
                                    <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">Keterangan (Opsional)</label>
                                    <textarea name="keterangan" id="keterangan" rows="4" class="w-full border-gray-300 rounded-md shadow-sm px-3 py-2 @error('keterangan') border-red-500 @enderror" placeholder="Tambahkan catatan atau informasi tambahan tentang retur ini...">{{ old('keterangan', $retur->keterangan) }}</textarea>
                                    @error('keterangan')
                                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Penting -->
                        <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                            <p class="text-sm text-yellow-800">
                                <strong>⚠️ Perhatian:</strong> Hanya alasan retur dan keterangan yang bisa diubah.
                                Untuk mengubah jumlah atau harga retur, silakan hapus retur ini dan buat yang baru.
                            </p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-8 pt-6 border-t border-gray-200 flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('retur.show', $retur->id_retur) }}" class="flex-1 text-center bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium text-sm transition border border-gray-300">
                            ← Batal
                        </a>
                        <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium text-sm transition">
                            ✅ Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
