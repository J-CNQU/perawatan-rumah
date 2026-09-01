<x-guest-layout>
    <!-- Header / Title -->
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-bold text-white tracking-wide">Daftar Akun</h1>
        <p class="text-sm text-[#94A3B8] mt-1">Lengkapi form di bawah untuk mengelola asetmu</p>
    </div>

    <!-- Card Container -->
    <div class="w-full sm:max-w-md px-8 py-8 bg-[#1D2433] shadow-2xl rounded-2xl border border-[#2D3748]">
        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Name -->
            <div>
                <label for="name" class="block font-medium text-sm text-[#E2E8F0] mb-2">Nama Lengkap</label>
                <input id="name" 
                       class="block w-full rounded-lg bg-[#121620] border-[#2D3748] text-white focus:border-[#6366F1] focus:ring-[#6366F1]" 
                       type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-400" />
            </div>

            <!-- Email Address -->
            <div class="mt-4">
                <label for="email" class="block font-medium text-sm text-[#E2E8F0] mb-2">Email</label>
                <input id="email" 
                       class="block w-full rounded-lg bg-[#121620] border-[#2D3748] text-white focus:border-[#6366F1] focus:ring-[#6366F1]" 
                       type="email" name="email" :value="old('email')" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-400" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <label for="password" class="block font-medium text-sm text-[#E2E8F0] mb-2">Password</label>
                <input id="password" 
                       class="block w-full rounded-lg bg-[#121620] border-[#2D3748] text-white focus:border-[#6366F1] focus:ring-[#6366F1]" 
                       type="password" name="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-400" />
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <label for="password_confirmation" class="block font-medium text-sm text-[#E2E8F0] mb-2">Konfirmasi Password</label>
                <input id="password_confirmation" 
                       class="block w-full rounded-lg bg-[#121620] border-[#2D3748] text-white focus:border-[#6366F1] focus:ring-[#6366F1]" 
                       type="password" name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-400" />
            </div>

            <div class="flex items-center justify-between mt-8">
                <a class="text-sm text-[#94A3B8] hover:text-white transition-colors" href="{{ route('login') }}">
                    {{ __('Sudah punya akun?') }}
                </a>

                <button type="submit" 
                        class="px-6 py-2.5 bg-[#5b54ff] hover:bg-[#4943cc] rounded-lg font-semibold text-sm text-white shadow-lg transition">
                    {{ __('Daftar') }}
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>