<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Log Aktivitas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-6">
                <form action="{{ route('log.aktivitas') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
                    
                    <div class="flex-1">
                        <x-input-label for="search" value="Cari Makanan/Barcode" />
                        <x-text-input id="search" name="search" type="text" class="mt-1 block w-full" value="{{ request('search') }}" placeholder="Ketik nama jajanan..." />
                    </div>

                    <div>
                        <x-input-label for="jenis_aktivitas" value="Jenis Aktivitas" />
                        <select id="jenis_aktivitas" name="jenis_aktivitas" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                            <option value="">Semua Aktivitas</option>
                            <option value="Barang Masuk" {{ request('jenis_aktivitas') == 'Barang Masuk' ? 'selected' : '' }}>Barang Masuk</option>
                            <option value="Barang Keluar" {{ request('jenis_aktivitas') == 'Barang Keluar' ? 'selected' : '' }}>Barang Keluar</option>
                        </select>
                    </div>

                    <div>
                        <x-input-label for="tanggal_awal" value="Tanggal Awal" />
                        <x-text-input id="tanggal_awal" name="tanggal_awal" type="date" class="mt-1 block w-full" value="{{ request('tanggal_awal') }}" />
                    </div>

                    <div>
                        <x-input-label for="tanggal_akhir" value="Tanggal Akhir" />
                        <x-text-input id="tanggal_akhir" name="tanggal_akhir" type="date" class="mt-1 block w-full" value="{{ request('tanggal_akhir') }}" />
                    </div>

                    <div>
                        <x-primary-button type="submit" class="py-3">
                            Filter
                        </x-primary-button>
                        <a href="{{ route('log.aktivitas') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 ml-2">Reset</a>
                    </div>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border rounded-lg">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-600">Waktu</th>
                                <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-600">Jajanan</th>
                                <th class="py-3 px-4 border-b text-center text-sm font-semibold text-gray-600">Aktivitas</th>
                                <th class="py-3 px-4 border-b text-center text-sm font-semibold text-gray-600">Perubahan</th>
                                <th class="py-3 px-4 border-b text-center text-sm font-semibold text-gray-600">Stok Akhir</th>
                                <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-600">Kasir/Admin</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4 border-b text-sm">{{ \Carbon\Carbon::parse($log->tgl_aktivitas)->format('d M Y, H:i') }}</td>
                                <td class="py-3 px-4 border-b text-sm font-medium">{{ $log->makanan->nama_makanan ?? 'Terhapus' }}</td>
                                <td class="py-3 px-4 border-b text-sm text-center">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $log->jenis_aktivitas == 'Barang Masuk' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $log->jenis_aktivitas }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 border-b text-sm text-center font-bold {{ $log->jenis_aktivitas == 'Barang Masuk' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $log->jenis_aktivitas == 'Barang Masuk' ? '+' : '-' }}{{ $log->jumlah_perubahan }}
                                </td>
                                <td class="py-3 px-4 border-b text-sm text-center">{{ $log->stok_sesudah }}</td>
                                <td class="py-3 px-4 border-b text-sm text-gray-500">{{ $log->pengguna->username ?? 'Sistem' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-gray-500 italic">Belum ada riwayat Keluar-Masuk Barang.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $logs->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>