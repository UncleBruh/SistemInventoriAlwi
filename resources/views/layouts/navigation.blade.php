@php
    $navClass = "flex items-center px-4 py-3 text-sm font-bold rounded-lg transition-all mb-1";
    $activeClass = "bg-indigo-50 text-indigo-700 border-l-4 border-indigo-600 shadow-sm";
    $inactiveClass = "text-gray-600 hover:bg-gray-50 hover:text-gray-900 border-l-4 border-transparent";
@endphp

<div>
    <div x-show="sidebarOpen" class="relative z-50 lg:hidden" role="dialog" aria-modal="true" style="display: none;">
        <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 bg-gray-900/80"></div>

        <div class="fixed inset-0 flex">
            <div x-show="sidebarOpen" 
                x-transition:enter="transition ease-in-out duration-300 transform" 
                x-transition:enter-start="-translate-x-full" 
                x-transition:enter-end="translate-x-0" 
                x-transition:leave="transition ease-in-out duration-300 transform" 
                x-transition:leave-start="translate-x-0" 
                x-transition:leave-end="-translate-x-full" 
                class="relative mr-16 flex w-full max-w-xs flex-1 flex-col bg-white">
                
                <div class="absolute left-full top-0 flex w-16 justify-center pt-5">
                    <button type="button" class="-m-2.5 p-2.5" @click="sidebarOpen = false">
                        <span class="sr-only">Tutup sidebar</span>
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="flex h-16 shrink-0 items-center px-6 bg-indigo-600">
                    <span class="font-bold text-xl text-white tracking-wide">Alwi College</span>
                </div>

                <nav class="flex flex-1 flex-col overflow-y-auto px-4 py-4">
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? $activeClass : $inactiveClass }} {{ $navClass }}">
                        <span class="mr-3 text-lg">🏠</span> Dashboard
                    </a>
                    <div class="pt-4 pb-2 px-4"><span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Database</span></div>
                    <a href="{{ route('makanan.index') }}" class="{{ request()->routeIs('makanan.*') ? $activeClass : $inactiveClass }} {{ $navClass }}">
                        <span class="mr-3 text-lg">📦</span> Data Jajanan
                    </a>
                    <div class="pt-4 pb-2 px-4"><span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Mutasi Barang</span></div>
                    <a href="{{ route('mutasi_masuk.index') }}" class="{{ request()->routeIs('mutasi_masuk.*') || request()->routeIs('log.create') ? $activeClass : $inactiveClass }} {{ $navClass }}">
                        <span class="mr-3 text-lg">➕</span> Barang Masuk
                    </a>
                    @if(Auth::user()->role === 'Pemilik')
                    <a href="{{ route('mutasi_keluar.index') }}" class="{{ request()->routeIs('mutasi_keluar.*') || request()->routeIs('log.keluar.create') ? $activeClass : $inactiveClass }} {{ $navClass }}">
                        <span class="mr-3 text-lg">➖</span> Barang Keluar
                    </a>
                    <a href="{{ route('log.aktivitas') }}" class="{{ request()->routeIs('log.aktivitas') ? $activeClass : $inactiveClass }} {{ $navClass }}">
                        <span class="mr-3 text-lg">📋</span> Laporan Log
                    </a>
                    @endif
                </nav>
            </div>
        </div>
    </div>

    <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-64 lg:flex-col border-r border-gray-200 bg-white shadow-sm">
        <div class="flex h-16 shrink-0 items-center border-b border-indigo-700 bg-indigo-600 px-6">
            <span class="font-bold text-xl text-white tracking-wide drop-shadow-md">Alwi College</span>
        </div>
        
        <nav class="flex flex-1 flex-col overflow-y-auto px-4 py-6">
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? $activeClass : $inactiveClass }} {{ $navClass }}">
                <span class="mr-3 text-lg">🏠</span> Dashboard
            </a>
            <div class="pt-4 pb-2 px-4"><span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Database</span></div>
            <a href="{{ route('makanan.index') }}" class="{{ request()->routeIs('makanan.*') ? $activeClass : $inactiveClass }} {{ $navClass }}">
                <span class="mr-3 text-lg">📦</span> Data Jajanan
            </a>
            <div class="pt-4 pb-2 px-4"><span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Mutasi Barang</span></div>
            <a href="{{ route('mutasi_masuk.index') }}" class="{{ request()->routeIs('mutasi_masuk.*') || request()->routeIs('log.create') ? $activeClass : $inactiveClass }} {{ $navClass }}">
                <span class="mr-3 text-lg">➕</span> Barang Masuk
            </a>
            @if(Auth::user()->role === 'Pemilik')
            <a href="{{ route('mutasi_keluar.index') }}" class="{{ request()->routeIs('mutasi_keluar.*') || request()->routeIs('log.keluar.create') ? $activeClass : $inactiveClass }} {{ $navClass }}">
                <span class="mr-3 text-lg">➖</span> Barang Keluar
            </a>
            <a href="{{ route('log.aktivitas') }}" class="{{ request()->routeIs('log.aktivitas') ? $activeClass : $inactiveClass }} {{ $navClass }}">
                <span class="mr-3 text-lg">📋</span> Laporan Log
            </a>
            @endif
        </nav>
    </div>
</div>