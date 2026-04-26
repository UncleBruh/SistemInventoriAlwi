<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Warung Biebie') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .select2-container .select2-selection--single {
            height: 42px !important;
            border-color: #d1d5db !important;
            border-radius: 0.375rem !important;
            padding-top: 6px;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            font-size: 0.875rem;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #374151 !important;
            font-weight: 500;
            font-size: 0.875rem;
        }
        .select2-dropdown {
            font-size: 0.875rem;
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900" x-data="{ sidebarOpen: window.innerWidth >= 1024 }">

    <div>
        @include('layouts.navigation')

        <div :class="sidebarOpen ? 'lg:pl-64' : 'lg:pl-0'" class="flex flex-col min-h-screen transition-all duration-300 ease-in-out">
            <header class="sticky top-0 z-40 flex h-14 sm:h-16 shrink-0 items-center gap-x-3 sm:gap-x-4 border-b border-gray-200 bg-white px-3 sm:px-4 shadow-sm lg:px-8">
                <button type="button" class="-m-2.5 p-2.5 text-gray-700 hover:bg-gray-100 rounded-md transition" @click="sidebarOpen = !sidebarOpen">
                    <span class="sr-only">Toggle Sidebar</span>
                    <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>

                <div class="flex flex-1 gap-x-3 sm:gap-x-4 items-center justify-between min-w-0">
                    <div class="font-bold text-base sm:text-lg text-gray-800 truncate">
                        {{ $header ?? 'Sistem Inventori' }}
                    </div>

                    <div class="flex items-center gap-x-3 sm:gap-x-4 flex-shrink-0">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-gray-50 transition border border-transparent hover:border-gray-200">
                                    <div class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold text-xs sm:text-sm">
                                        {{ strtoupper(substr(Auth::user()->username, 0, 1)) }}
                                    </div>
                                    <span class="hidden sm:block text-xs sm:text-sm font-semibold text-gray-900 truncate max-w-[100px]">{{ Auth::user()->username }}</span>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <x-dropdown-link :href="route('profile.edit')">👤 Profil</x-dropdown-link>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="text-red-600 font-bold">🚪 Keluar</x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                </div>
            </header>

            <main class="py-4 sm:py-6 px-3 sm:px-4 lg:px-8">
                {{ $slot }}
            </main>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.searchable-select').select2({
                width: '100%',
                placeholder: "Ketik untuk mencari...",
                allowClear: true
            });
        });
    </script>
</body>
</html>
