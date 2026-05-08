@php
    $navClass = "flex items-center px-3 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm font-bold rounded-lg transition-all mb-1";
    $activeClass = "bg-indigo-50 text-indigo-700 border-l-4 border-indigo-600 shadow-sm";
    $inactiveClass = "text-gray-600 hover:bg-gray-50 hover:text-gray-900 border-l-4 border-transparent";
@endphp

<div>
    <div x-show="sidebarOpen" class="relative z-50 lg:hidden" x-cloak>
        <div class="fixed inset-0 bg-gray-900/80 transition-opacity" x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false"></div>
        <div class="fixed inset-0 flex">
            <div x-show="sidebarOpen" x-transition:enter="transition ease-in-out duration-300 transform" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in-out duration-300 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="relative flex w-full max-w-xs flex-1 flex-col bg-white">

                <div @click="sidebarOpen = false" class="flex h-14 sm:h-16 shrink-0 items-center px-4 sm:px-6 bg-indigo-600 text-white font-bold text-lg sm:text-xl cursor-pointer hover:bg-indigo-700 transition gap-3">
                    <img src="{{ asset('foto/logobimbel.png') }}" alt="Logo" class="w-8 sm:w-10 h-8 sm:h-10 object-contain">
                    Warung Biebie
                </div>

                <nav class="flex-1 overflow-y-auto p-3 sm:p-4">
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? $activeClass : $inactiveClass }} {{ $navClass }}">🏠 Dashboard</a>

                    <div class="pt-3 sm:pt-4 pb-2 px-3 sm:px-4 text-[9px] sm:text-[10px] font-bold text-gray-400 uppercase">Database</div>

                    @if(Auth::user()->role === 'Pemilik')
                        <a href="{{ route('kategori.index') }}" class="{{ request()->routeIs('kategori.*') ? $activeClass : $inactiveClass }} {{ $navClass }}">🏷️ Kategori Barang</a>
                    @endif
                    <a href="{{ route('makanan.index') }}" class="{{ request()->routeIs('makanan.*') ? $activeClass : $inactiveClass }} {{ $navClass }}">📦 Data Jajanan</a>

                    <div class="pt-3 sm:pt-4 pb-2 px-3 sm:px-4 text-[9px] sm:text-[10px] font-bold text-gray-400 uppercase">Mutasi</div>

                    <a href="{{ route('mutasi_masuk.create') }}" class="{{ request()->routeIs('mutasi_masuk.*') ? $activeClass : $inactiveClass }} {{ $navClass }}">➕ Barang Masuk</a>

                    @if(Auth::user()->role === 'Pemilik' || Auth::user()->role === 'Admin')
                        <a href="{{ route('alokasi-gudang-etalase.index') }}" class="{{ request()->routeIs('alokasi-gudang-etalase.*') ? $activeClass : $inactiveClass }} {{ $navClass }}">🏭 Alokasi Gudang→Etalase</a>
                    @endif

                    @if(Auth::user()->role === 'Pemilik')
                        <a href="{{ route('mutasi_keluar.create') }}" class="{{ request()->routeIs('mutasi_keluar.*') ? $activeClass : $inactiveClass }} {{ $navClass }}">📦 Pengelolaan Stok Etalase</a>
                        <a href="{{ route('pengeluaran_gudang.create') }}" class="{{ request()->routeIs('pengeluaran_gudang.*') ? $activeClass : $inactiveClass }} {{ $navClass }}">🏭 Pengeluaran Gudang</a>
                        <a href="{{ route('log.aktivitas') }}" class="{{ request()->routeIs('log.aktivitas') ? $activeClass : $inactiveClass }} {{ $navClass }}">📋 Lihat Aktivitas Mutasi</a>
                    @endif

                    <div class="pt-3 sm:pt-4 pb-2 px-3 sm:px-4 text-[9px] sm:text-[10px] font-bold text-gray-400 uppercase">Transaksi</div>
                    <a href="{{ route('penjualan.create') }}" class="{{ request()->routeIs('penjualan.create') ? $activeClass : $inactiveClass }} {{ $navClass }}">🛒 Mesin Kasir</a>

                    @if(Auth::user()->role === 'Pemilik' || Auth::user()->role === 'Admin')
                        <div class="pt-3 sm:pt-4 pb-2 px-3 sm:px-4 text-[9px] sm:text-[10px] font-bold text-gray-400 uppercase">Laporan</div>

                        <a href="{{ route('penjualan.index') }}" class="{{ request()->routeIs('penjualan.*') && !request()->routeIs('penjualan.create') ? $activeClass : $inactiveClass }} {{ $navClass }}">💰 Laporan Penjualan</a>
                        <a href="{{ route('retur.index') }}" class="{{ request()->routeIs('retur.*') ? $activeClass : $inactiveClass }} {{ $navClass }}">🔄 Riwayat Retur</a>

                    @if(Auth::user()->role === 'Pemilik')
                        <a href="{{ route('laporan.masuk') }}" class="{{ request()->routeIs('laporan.masuk') ? $activeClass : $inactiveClass }} {{ $navClass }}">📄 Laporan Barang Masuk</a>
                        <a href="{{ route('laporan.keluar') }}" class="{{ request()->routeIs('laporan.keluar') ? $activeClass : $inactiveClass }} {{ $navClass }}">📄 Laporan Barang Keluar dari Etalase</a>
                        <a href="{{ route('laporan.pengeluaran_gudang') }}" class="{{ request()->routeIs('laporan.pengeluaran_gudang') ? $activeClass : $inactiveClass }} {{ $navClass }}">📄 Laporan Pengeluaran Gudang</a>

                        <div class="pt-3 sm:pt-4 pb-2 px-3 sm:px-4 text-[9px] sm:text-[10px] font-bold text-gray-400 uppercase">Admin</div>

                        <a href="{{ route('register') }}" class="{{ request()->routeIs('register') ? $activeClass : $inactiveClass }} {{ $navClass }}">👤 Tambah Pengguna</a>
                    @endif
                </nav>
            </div>
        </div>
    </div>


    <nav x-show="sidebarOpen" x-transition:enter="transition ease-in-out duration-300 transform" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in-out duration-300 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-64 lg:flex-col border-r border-gray-200 bg-white shadow-sm">

        <div @click="sidebarOpen = false" class="flex h-16 shrink-0 items-center border-b border-indigo-700 bg-indigo-600 px-4 sm:px-6 text-white font-bold text-lg sm:text-xl shadow-md cursor-pointer hover:bg-indigo-700 transition gap-3">
            <img src="{{ asset('foto/logobimbel.png') }}" alt="Logo" class="w-8 sm:w-10 h-8 sm:h-10 object-contain">
            Warung Biebie
        </div>

        <div class="flex flex-1 flex-col overflow-y-auto px-3 sm:px-4 py-4 sm:py-6">
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? $activeClass : $inactiveClass }} {{ $navClass }}">🏠 Dashboard</a>

            <div class="pt-3 sm:pt-4 pb-2 px-3 sm:px-4 text-[10px] font-bold text-gray-400 uppercase">Database</div>

            @if(Auth::user()->role === 'Pemilik')
                <a href="{{ route('kategori.index') }}" class="{{ request()->routeIs('kategori.*') ? $activeClass : $inactiveClass }} {{ $navClass }}">🏷️ Kategori Barang</a>
            @endif
            <a href="{{ route('makanan.index') }}" class="{{ request()->routeIs('makanan.*') ? $activeClass : $inactiveClass }} {{ $navClass }}">📦 Data Jajanan</a>

            <div class="pt-3 sm:pt-4 pb-2 px-3 sm:px-4 text-[10px] font-bold text-gray-400 uppercase">Mutasi</div>

            <a href="{{ route('mutasi_masuk.create') }}" class="{{ request()->routeIs('mutasi_masuk.*') ? $activeClass : $inactiveClass }} {{ $navClass }}">➕ Barang Masuk</a>

            @if(Auth::user()->role === 'Pemilik' || Auth::user()->role === 'Admin')
                <a href="{{ route('alokasi-gudang-etalase.index') }}" class="{{ request()->routeIs('alokasi-gudang-etalase.*') ? $activeClass : $inactiveClass }} {{ $navClass }}">🏭 Alokasi Gudang→Etalase</a>
            @endif

            @if(Auth::user()->role === 'Pemilik')
                <a href="{{ route('mutasi_keluar.create') }}" class="{{ request()->routeIs('mutasi_keluar.*') ? $activeClass : $inactiveClass }} {{ $navClass }}">📦 Pengelolaan Stok Etalase</a>
                <a href="{{ route('pengeluaran_gudang.create') }}" class="{{ request()->routeIs('pengeluaran_gudang.*') ? $activeClass : $inactiveClass }} {{ $navClass }}">🏭 Pengeluaran Gudang</a>
                <a href="{{ route('log.aktivitas') }}" class="{{ request()->routeIs('log.aktivitas') ? $activeClass : $inactiveClass }} {{ $navClass }}">📋 Lihat Aktivitas Mutasi</a>
            @endif

            <div class="pt-3 sm:pt-4 pb-2 px-3 sm:px-4 text-[10px] font-bold text-gray-400 uppercase">Transaksi</div>
            <a href="{{ route('penjualan.create') }}" class="{{ request()->routeIs('penjualan.create') ? $activeClass : $inactiveClass }} {{ $navClass }}">🛒 Mesin Kasir</a>

            @if(Auth::user()->role === 'Pemilik' || Auth::user()->role === 'Admin')
                <div class="pt-3 sm:pt-4 pb-2 px-3 sm:px-4 text-[10px] font-bold text-gray-400 uppercase">Laporan</div>

                <a href="{{ route('penjualan.index') }}" class="{{ request()->routeIs('penjualan.*') && !request()->routeIs('penjualan.create') ? $activeClass : $inactiveClass }} {{ $navClass }}">💰 Laporan Penjualan</a>
                <a href="{{ route('retur.index') }}" class="{{ request()->routeIs('retur.*') ? $activeClass : $inactiveClass }} {{ $navClass }}">🔄 Riwayat Retur</a>

                @if(Auth::user()->role === 'Pemilik')
                    <a href="{{ route('laporan.masuk') }}" class="{{ request()->routeIs('laporan.masuk') ? $activeClass : $inactiveClass }} {{ $navClass }}">📄 Laporan Barang Masuk</a>
                    <a href="{{ route('laporan.keluar') }}" class="{{ request()->routeIs('laporan.keluar') ? $activeClass : $inactiveClass }} {{ $navClass }}">📄 Laporan Barang Keluar dari Etalase</a>
                    <a href="{{ route('laporan.pengeluaran_gudang') }}" class="{{ request()->routeIs('laporan.pengeluaran_gudang') ? $activeClass : $inactiveClass }} {{ $navClass }}">📄 Laporan Pengeluaran Gudang</a>

                    <div class="pt-3 sm:pt-4 pb-2 px-3 sm:px-4 text-[10px] font-bold text-gray-400 uppercase">Admin</div>

                    <a href="{{ route('register') }}" class="{{ request()->routeIs('register') ? $activeClass : $inactiveClass }} {{ $navClass }}">👤 Tambah Pengguna</a>
                @endif
            @endif
        </div>
    </nav>
</div>
