@php
    $navClass = "flex items-center px-4 py-3 text-sm font-bold rounded-lg transition-all mb-1";
    $activeClass = "bg-indigo-50 text-indigo-700 border-l-4 border-indigo-600 shadow-sm";
    $inactiveClass = "text-gray-600 hover:bg-gray-50 hover:text-gray-900 border-l-4 border-transparent";
@endphp

<div>
    <div x-show="sidebarOpen" class="relative z-50 lg:hidden" x-cloak>
        <div class="fixed inset-0 bg-gray-900/80 transition-opacity" x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false"></div>
        <div class="fixed inset-0 flex">
            <div x-show="sidebarOpen" x-transition:enter="transition ease-in-out duration-300 transform" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in-out duration-300 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="relative flex w-full max-w-xs flex-1 flex-col bg-white">
                <div class="flex h-16 shrink-0 items-center px-6 bg-indigo-600 text-white font-bold text-xl">Alwi College</div>
                <nav class="flex-1 overflow-y-auto p-4">
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? $activeClass : $inactiveClass }} {{ $navClass }}">🏠 Dashboard</a>
                    
                    <div class="pt-4 pb-2 px-4 text-[10px] font-bold text-gray-400 uppercase">Database</div>
                    
                    <a href="{{ route('kategori.index') }}" class="{{ request()->routeIs('kategori.*') ? $activeClass : $inactiveClass }} {{ $navClass }}">🏷️ Kategori Barang</a>
                    <a href="{{ route('makanan.index') }}" class="{{ request()->routeIs('makanan.*') ? $activeClass : $inactiveClass }} {{ $navClass }}">📦 Data Jajanan</a>
                    
                    <div class="pt-4 pb-2 px-4 text-[10px] font-bold text-gray-400 uppercase">Mutasi</div>
                    
                    <a href="{{ route('mutasi_masuk.create') }}" class="{{ request()->routeIs('mutasi_masuk.*') ? $activeClass : $inactiveClass }} {{ $navClass }}">➕ Barang Masuk</a>
                    @if(Auth::user()->role === 'Pemilik')
                        <a href="{{ route('mutasi_keluar.create') }}" class="{{ request()->routeIs('mutasi_keluar.*') ? $activeClass : $inactiveClass }} {{ $navClass }}">➖ Barang Keluar</a>
                        <a href="{{ route('log.aktivitas') }}" class="{{ request()->routeIs('log.aktivitas') ? $activeClass : $inactiveClass }} {{ $navClass }}">📋 Laporan Log</a>
                    @endif
                </nav>
            </div>
        </div>
    </div>

    <nav x-show="sidebarOpen" x-transition:enter="transition ease-in-out duration-300 transform" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in-out duration-300 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-64 lg:flex-col border-r border-gray-200 bg-white shadow-sm">
        <div class="flex h-16 shrink-0 items-center border-b border-indigo-700 bg-indigo-600 px-6 text-white font-bold text-xl shadow-md">Alwi College</div>
        <div class="flex flex-1 flex-col overflow-y-auto px-4 py-6">
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? $activeClass : $inactiveClass }} {{ $navClass }}">🏠 Dashboard</a>
            
            <div class="pt-4 pb-2 px-4 text-[10px] font-bold text-gray-400 uppercase">Database</div>
            
            <a href="{{ route('kategori.index') }}" class="{{ request()->routeIs('kategori.*') ? $activeClass : $inactiveClass }} {{ $navClass }}">🏷️ Kategori Barang</a>
            <a href="{{ route('makanan.index') }}" class="{{ request()->routeIs('makanan.*') ? $activeClass : $inactiveClass }} {{ $navClass }}">📦 Data Jajanan</a>
            
            <div class="pt-4 pb-2 px-4 text-[10px] font-bold text-gray-400 uppercase">Mutasi</div>
            
            <a href="{{ route('mutasi_masuk.create') }}" class="{{ request()->routeIs('mutasi_masuk.*') ? $activeClass : $inactiveClass }} {{ $navClass }}">➕ Barang Masuk</a>
            @if(Auth::user()->role === 'Pemilik')
                <a href="{{ route('mutasi_keluar.create') }}" class="{{ request()->routeIs('mutasi_keluar.*') ? $activeClass : $inactiveClass }} {{ $navClass }}">➖ Barang Keluar</a>
                <a href="{{ route('log.aktivitas') }}" class="{{ request()->routeIs('log.aktivitas') ? $activeClass : $inactiveClass }} {{ $navClass }}">📋 Laporan Log</a>
            @endif
        </div>
    </nav>
</div>