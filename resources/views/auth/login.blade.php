<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 text-center mb-2">Masuk ke Akun</h2>
        <p class="text-center text-gray-600 text-sm">Gunakan kredensial Anda untuk mengakses sistem</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="username" value="Username" class="block text-sm font-semibold text-gray-700 mb-2" />
            <x-text-input id="username" class="block w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition" type="text" name="username" :value="old('username')" required autofocus autocomplete="username" placeholder="Masukkan username Anda" />
            <x-input-error :messages="$errors->get('username')" class="mt-1 text-sm" />
        </div>

        <div>
            <x-input-label for="password" value="Password" class="block text-sm font-semibold text-gray-700 mb-2" />
            <x-text-input id="password" class="block w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition"
                            type="password"
                            name="password"
                            required autocomplete="current-password" placeholder="Masukkan password Anda" />
            <x-input-error :messages="$errors->get('password')" class="mt-1 text-sm" />
        </div>

        <div class="block pt-2">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 w-4 h-4" name="remember">
                <span class="ms-2 text-sm text-gray-700">Ingat saya</span>
            </label>
        </div>

        <div class="pt-4">
            <x-primary-button class="w-full justify-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200 transform hover:scale-105 active:scale-95">
                {{ __('Masuk') }}
            </x-primary-button>
        </div>
    </form>

    <div class="mt-6 text-center text-sm text-gray-600">
        <p>Belum punya akun? <a href="#" class="text-indigo-600 hover:text-indigo-700 font-semibold">Hubungi Administrator</a></p>
    </div>
</x-guest-layout>
