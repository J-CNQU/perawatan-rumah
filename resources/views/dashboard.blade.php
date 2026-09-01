<x-app-layout>

    {{-- Main Container dengan Alpine.js untuk kontrol Modal --}}
    <div x-data="{ openModal: false }" class="py-8 bg-gray-900 text-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- 1. Top Bar: Profil, Penghasilan, & Summary Ringkas --}}
            <div
                class="bg-gray-800 p-6 rounded-2xl border border-gray-700 flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex items-center space-x-4">
                    <div
                        class="w-14 h-14 bg-indigo-600 rounded-2xl flex items-center justify-center font-bold text-2xl text-white shadow-lg">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <div>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit"
                                class="px-2.5 py-1 text-xs font-medium text-red-400 hover:text-white hover:bg-red-500/20 border border-red-500/30 rounded-lg transition-all">
                                Log Out
                            </button>
                        </form>
                        <h1 class="text-xl font-bold text-white">Halo, {{ auth()->user()->name }}</h1>
                        <p class="text-sm text-gray-400">{{ auth()->user()->email }}</p>
                    </div>
                </div>

                {{-- Ringkasan Finansial di Pojok Kanan --}}
                <div class="flex items-center space-x-6 bg-gray-900/60 px-6 py-3 rounded-xl border border-gray-700/50">
                    <div class="text-right">
                        <p class="text-xs text-gray-400 uppercase tracking-wider">Penghasilan Bulanan</p>
                        <p class="text-lg font-bold text-emerald-400">
                            Rp {{ number_format(auth()->user()->monthly_income ?? 0, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="h-8 w-px bg-gray-700"></div>
                    <div class="text-right">
                        <p class="text-xs text-gray-400 uppercase tracking-wider">Total Properti</p>
                        <p class="text-lg font-bold text-indigo-400">{{ $properties->count() }} Lokasi</p>
                    </div>
                </div>
            </div>

            {{-- Alert Notifikasi Sukses --}}
            @if(session('success'))
                <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-xl text-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- 2. Grid Properti / Lokasi --}}
            <div>
                <h3 class="text-lg font-semibold text-gray-300 mb-4">Daftar Properti & Lokasi Aset</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    {{-- Tombol Card + Add Property (Triggers Modal) --}}
                    <button @click="openModal = true"
                        class="border-2 border-dashed border-gray-700 hover:border-indigo-500 hover:bg-gray-800/50 rounded-2xl p-8 flex flex-col items-center justify-center min-h-[200px] transition-all group">
                        <div
                            class="w-12 h-12 bg-gray-800 group-hover:bg-indigo-600 rounded-full flex items-center justify-center mb-3 transition-colors">
                            <span class="text-2xl font-light text-gray-300 group-hover:text-white">+</span>
                        </div>
                        <span class="font-medium text-gray-400 group-hover:text-white">Add Home / Office /
                            Address</span>
                        <span class="text-xs text-gray-500 mt-1">Tambah lokasi baru untuk asetmu</span>
                    </button>

                    {{-- Loop Menampilkan Properti Terdaftar --}}
                    @forelse($properties as $property)
                        <div
                            class="bg-gray-800 border border-gray-700 rounded-2xl p-6 flex flex-col justify-between hover:border-indigo-500/50 transition-all">
                            <div>
                                <div class="flex justify-between items-start mb-3">
                                    <span
                                        class="px-3 py-1 bg-indigo-500/10 text-indigo-400 text-xs rounded-full border border-indigo-500/20 font-medium">
                                        {{ $property->type }}
                                    </span>
                                    <span class="text-xs text-gray-400">{{ $property->assets->count() }} Aset</span>
                                </div>
                                <h4 class="text-xl font-bold text-white mb-1">{{ $property->name }}</h4>
                                <p class="text-sm text-gray-400 line-clamp-2">
                                    {{ $property->address ?? 'Belum ada alamat rinci' }}
                                </p>
                            </div>

                            <div class="mt-6 pt-4 border-t border-gray-700/50 flex justify-between items-center">
                                <span class="text-xs text-gray-500">Klik untuk kelola kategori</span>
                                <a href="{{ route('properties.show', $property->id) }}"
                                    class="text-xs font-semibold text-indigo-400 hover:text-indigo-300">Buka
                                    Properti &rarr;</a>
                            </div>
                        </div>
                    @empty
                        {{-- Keadaan saat belum ada data (Blankpage State) --}}
                    @endforelse

                </div>
            </div>

            {{-- 3. Modal Pop-Up Add Property --}}
            <div x-show="openModal" x-cloak
                class="fixed inset-0 z-50 overflow-y-auto bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">

                <div @click.away="openModal = false"
                    class="bg-gray-800 border border-gray-700 rounded-2xl max-w-md w-full p-6 space-y-6 shadow-2xl">

                    <div class="flex justify-between items-center border-b border-gray-700 pb-4">
                        <h3 class="text-lg font-bold text-white">Tambah Lokasi Baru</h3>
                        <button @click="openModal = false" class="text-gray-400 hover:text-white">&times;</button>
                    </div>

                    <form action="{{ route('properties.store') }}" method="POST" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-1">Nama Lokasi / Properti</label>
                            <input type="text" name="name" required placeholder="Contoh: Rumah Utama / Ruko Kalbar"
                                class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-1">Tipe Properti</label>
                            <select name="type" required
                                class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                <option value="Home">Home (Rumah)</option>
                                <option value="Office">Office (Kantor)</option>
                                <option value="Store">Store (Toko/Ruko)</option>
                                <option value="Other">Lainnya</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-1">Alamat Lengkap
                                (Opsional)</label>
                            <textarea name="address" rows="3" placeholder="Jl. Ahmad Yani No. 12..."
                                class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none"></textarea>
                        </div>

                        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-700">
                            <button type="button" @click="openModal = false"
                                class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded-xl text-xs font-medium text-gray-300">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 rounded-xl text-xs font-medium text-white shadow-lg shadow-indigo-600/30">
                                Simpan Lokasi
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>