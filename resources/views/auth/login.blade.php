<x-guest-layout>
    <!-- Header / Title -->
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-bold text-white tracking-wide">Masuk ke Akun</h1>
        <p class="text-sm text-[#94A3B8] mt-1">Masukkan kredensial kamu untuk mengelola aset</p>
    </div>

    <!-- Session Status (Notifikasi jika ada pesan sukses/error dari backend) -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Card Container -->
    <div class="w-full sm:max-w-md px-8 py-8 bg-[#1D2433] shadow-2xl rounded-2xl border border-[#2D3748]">
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <div>
                <label for="email" class="block font-medium text-sm text-[#E2E8F0] mb-2">Email</label>
                <input id="email" 
                       class="block w-full rounded-lg bg-[#121620] border-[#2D3748] text-white focus:border-[#6366F1] focus:ring-[#6366F1]" 
                       type="email" 
                       name="email" 
                       :value="old('email')" 
                       required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-400" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <label for="password" class="block font-medium text-sm text-[#E2E8F0] mb-2">Password</label>
                <input id="password" 
                       class="block w-full rounded-lg bg-[#121620] border-[#2D3748] text-white focus:border-[#6366F1] focus:ring-[#6366F1]" 
                       type="password" 
                       name="password" 
                       required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-400" />
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between mt-4">
                <label for="remember_me" class="inline-flex items-center cursor-pointer">
                    <input id="remember_me" 
                           type="checkbox" 
                           class="rounded bg-[#121620] border-[#2D3748] text-[#5b54ff] shadow-sm focus:ring-[#6366F1] focus:ring-offset-[#1D2433]" 
                           name="remember">
                    <span class="ms-2 text-sm text-[#94A3B8] hover:text-white transition-colors">Ingat Saya</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="text-sm text-[#94A3B8] hover:text-white transition-colors" href="{{ route('password.request') }}">
                        Lupa password?
                    </a>
                @endif
            </div>

            <!-- Footer Action Buttons -->
            <div class="flex items-center justify-between mt-8">
                <a class="text-sm text-[#94A3B8] hover:text-white transition-colors" href="{{ route('register') }}">
                    Belum punya akun?
                </a>

                <button type="submit" 
                        class="px-6 py-2.5 bg-[#5b54ff] hover:bg-[#4943cc] rounded-lg font-semibold text-sm text-white shadow-lg transition">
                    Masuk
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>