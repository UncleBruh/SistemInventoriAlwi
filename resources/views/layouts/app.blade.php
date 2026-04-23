<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Alwi College') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-50 text-gray-900" x-data="{ sidebarOpen: false }">
    
    @include('layouts.navigation')

    <div class="lg:pl-64 flex flex-col min-h-screen transition-all duration-300">
        
        <header class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 bg-white px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
            
            <button type="button" class="-m-2.5 p-2.5 text-gray-700 lg:hidden hover:bg-gray-100 rounded-md" @click="sidebarOpen = true">
                <span class="sr-only">Buka menu sidebar</span>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>

            <div class="h-6 w-px bg-gray-200 lg:hidden" aria-hidden="true"></div>

            <div class="flex flex-1 gap-x-4 items-center justify-between lg:gap-x-6">
                
                <div class="font-bold text-lg sm:text-xl text-gray-800 truncate">
                    {{ $header ?? 'Dashboard' }}
                </div>

                <div class="flex items-center gap-x-4 lg:gap-x-6">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex items-center gap-2 p-1.5 rounded-lg border border-transparent hover:border-gray-200 hover:bg-gray-50 transition">
                                <div class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold text-sm shadow-sm">
                                    {{ strtoupper(substr(Auth::user()->username, 0, 1)) }}
                                </div>
                                <span class="hidden lg:flex lg:items-center">
                                    <span class="text-sm font-semibold leading-6 text-gray-900 ml-2">{{ Auth::user()->username }}</span>
                                    <svg class="ml-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="px-4 py-2 border-b border-gray-100 bg-gray-50">
                                <p class="text-[10px] uppercase text-gray-500 tracking-wider">Hak Akses Sistem</p>
                                <p class="text-sm font-bold text-indigo-600">{{ Auth::user()->role }}</p>
                            </div>
                            <x-dropdown-link :href="route('profile.edit')">
                                👤 {{ __('Lihat Profil') }}
                            </x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="text-red-600 font-bold hover:bg-red-50">
                                    🚪 {{ __('Keluar (Log Out)') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>
        </header>

        <main class="py-6 sm:py-10">
            <div class="px-4 sm:px-6 lg:px-8">
                {{ $slot }}
            </div>
        </main>

    </div>
</body>
</html>