<x-app-layout>
    <div x-data="{
        assetModal: false,
        logModal: false,
        editAssetModal: false,
        editLogModal: false,
        assetForm: { id: '', name: '', category: '', condition: 'Normal', purchase_date: '', purchase_price: '', description: '' },
        logForm: { id: '', type: 'Routine Checkup', service_date: '', cost: '', notes: '' }
    }" class="min-h-screen bg-gray-900 text-gray-100 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <a href="{{ route('dashboard') }}"
                        class="inline-flex items-center text-sm text-indigo-400 hover:text-indigo-300 mb-3">
                        &larr; Kembali ke Dashboard
                    </a>
                    <h1 class="text-3xl font-bold text-white">{{ $property->name }}</h1>
                    <p class="text-gray-400 mt-1">{{ $property->type }} · {{ $property->address ?? 'Alamat belum diisi' }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <button type="button" @click="assetModal = true"
                        class="bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-lg shadow-indigo-600/25">
                        + Tambah Aset
                    </button>
                    <button type="button" @click="logModal = true"
                        class="bg-gray-700 hover:bg-gray-600 text-white text-sm font-medium px-4 py-2.5 rounded-xl border border-gray-600">
                        + Tambah Log
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <div class="xl:col-span-2 space-y-6">
                    <div class="bg-gray-800 border border-gray-700 rounded-2xl p-6">
                        <div class="flex items-center justify-between mb-5">
                            <div>
                                <p class="text-xs uppercase tracking-[0.2em] text-gray-400">Manajemen Aset</p>
                                <h2 class="text-xl font-bold text-white mt-2">Daftar Aset di Lokasi Ini</h2>
                            </div>
                            <span class="text-sm text-gray-300">{{ $property->assets->count() }} item</span>
                        </div>

                        @if($property->assets->isEmpty())
                            <div class="border border-dashed border-gray-700 rounded-2xl p-8 text-center">
                                <p class="text-gray-400">Belum ada aset tercatat untuk properti ini.</p>
                            </div>
                        @else
                            <div class="overflow-hidden rounded-2xl border border-gray-700">
                                <table class="min-w-full divide-y divide-gray-700">
                                    <thead class="bg-gray-900/80">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">Nama Aset</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">Kategori</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">Status</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">Pembelian</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-700 bg-gray-800">
                                        @foreach($property->assets as $asset)
                                            <tr class="hover:bg-gray-700/40 transition-colors">
                                                <td class="px-4 py-3">
                                                    <button type="button" class="text-left text-white font-medium hover:text-indigo-300">
                                                        {{ $asset->name }}
                                                    </button>
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-300">
                                                    {{ $asset->category?->name ?? 'Belum dikategorikan' }}
                                                </td>
                                                <td class="px-4 py-3">
                                                    <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-medium
                                                        @if($asset->condition === 'Normal') bg-emerald-500/10 text-emerald-400 border-emerald-500/20
                                                        @elseif($asset->condition === 'Perlu Servis') bg-yellow-500/10 text-yellow-300 border-yellow-500/20
                                                        @else bg-red-500/10 text-red-300 border-red-500/20 @endif">
                                                        {{ $asset->condition ?? 'Normal' }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-300">
                                                    {{ $asset->purchase_date ? \Carbon\Carbon::parse($asset->purchase_date)->translatedFormat('d M Y') : '—' }}
                                                    @if(!empty($asset->purchase_price))
                                                        <span class="block text-xs text-gray-400">Rp {{ number_format($asset->purchase_price, 0, ',', '.') }}</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="flex items-center gap-3">
                                                        <button type="button" @click="editAssetModal = true; assetForm = { id: {{ $asset->id }}, name: '{{ addslashes($asset->name) }}', category: '{{ addslashes($asset->category?->name ?? 'Elektronik') }}', condition: '{{ $asset->condition }}', purchase_date: '{{ $asset->purchase_date }}', purchase_price: '{{ $asset->purchase_price }}', description: '{{ addslashes($asset->description ?? '') }}' }"
                                                            class="text-xs font-medium text-indigo-400 hover:text-indigo-300">Edit</button>
                                                        <form action="{{ route('properties.assets.destroy', [$property, $asset]) }}" method="POST" onsubmit="return confirm('Hapus aset ini?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-xs font-medium text-red-400 hover:text-red-300">Hapus</button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    <div class="bg-gray-800 border border-gray-700 rounded-2xl p-6">
                        <div class="flex items-center justify-between mb-5">
                            <div>
                                <p class="text-xs uppercase tracking-[0.2em] text-gray-400">Maintenance Log</p>
                                <h2 class="text-xl font-bold text-white mt-2">Riwayat Perawatan</h2>
                            </div>
                            <button type="button" @click="logModal = true"
                                class="bg-gray-700 hover:bg-gray-600 text-sm text-white px-3 py-2 rounded-lg border border-gray-600">
                                + Tambah Log
                            </button>
                        </div>

                        @php
                            $allLogs = $property->assets->flatMap(fn ($asset) => $asset->maintenanceLogs)->sortByDesc('service_date');
                        @endphp

                        @if($allLogs->isEmpty())
                            <div class="border border-dashed border-gray-700 rounded-2xl p-8 text-center">
                                <p class="text-gray-400">Belum ada riwayat servis untuk aset di lokasi ini.</p>
                            </div>
                        @else
                            <div class="space-y-3">
                                @foreach($allLogs as $log)
                                    <div class="rounded-xl bg-gray-900 border border-gray-700 p-4">
                                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
                                            <div>
                                                <p class="font-semibold text-white">{{ $log->asset?->name ?? 'Aset tidak diketahui' }}</p>
                                                <p class="text-sm text-gray-400">{{ $log->notes ?? 'Tidak ada catatan' }}</p>
                                            </div>
                                            <div class="text-left md:text-right">
                                                <p class="text-sm font-medium text-emerald-400">Rp {{ number_format($log->cost, 0, ',', '.') }}</p>
                                                <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($log->service_date)->translatedFormat('d M Y') }}</p>
                                            </div>
                                        </div>
                                        <div class="mt-3 flex justify-end gap-3">
                                            <button type="button" @click="editLogModal = true; logForm = { id: {{ $log->id }}, type: '{{ $log->type }}', service_date: '{{ $log->service_date }}', cost: '{{ $log->cost }}', notes: '{{ addslashes($log->notes ?? '') }}' }"
                                                class="text-xs font-medium text-indigo-400 hover:text-indigo-300">Edit Log</button>
                                            <form action="{{ route('properties.maintenance_logs.destroy', [$property, $log]) }}" method="POST" onsubmit="return confirm('Hapus log perawatan ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs font-medium text-red-400 hover:text-red-300">Hapus Log</button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <aside class="space-y-6">
                    <div class="bg-gray-800 border border-gray-700 rounded-2xl p-6">
                        <p class="text-xs uppercase tracking-[0.2em] text-gray-400">Ringkasan</p>
                        <div class="mt-4 space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400">Total Aset</span>
                                <span class="font-bold text-white">{{ $property->assets->count() }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400">Total Biaya Perawatan</span>
                                <span class="font-bold text-emerald-400">Rp {{ number_format($totalMaintenanceCost, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400">Status Lokasi</span>
                                <span class="inline-flex rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 px-2.5 py-1 text-xs font-medium">
                                    Aktif
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-800 border border-gray-700 rounded-2xl p-6">
                        <p class="text-xs uppercase tracking-[0.2em] text-gray-400">Detail Properti</p>
                        <dl class="mt-4 space-y-3 text-sm">
                            <div class="flex justify-between gap-3">
                                <dt class="text-gray-400">Jenis</dt>
                                <dd class="text-white font-medium">{{ $property->type }}</dd>
                            </div>
                            <div class="flex justify-between gap-3">
                                <dt class="text-gray-400">Alamat</dt>
                                <dd class="text-white font-medium text-right">{{ $property->address ?? 'Belum ada alamat' }}</dd>
                            </div>
                            <div class="flex justify-between gap-3">
                                <dt class="text-gray-400">Aset Terpantau</dt>
                                <dd class="text-white font-medium">{{ $property->assets->count() }}</dd>
                            </div>
                        </dl>
                    </div>
                </aside>
            </div>
        </div>

        <div x-show="assetModal" x-cloak
            class="fixed inset-0 z-50 overflow-y-auto bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
            <div @click.away="assetModal = false" class="bg-gray-800 border border-gray-700 rounded-2xl max-w-lg w-full p-6 space-y-6 shadow-2xl">
                <div class="flex justify-between items-center border-b border-gray-700 pb-4">
                    <h3 class="text-lg font-bold text-white">Tambah Aset Baru</h3>
                    <button @click="assetModal = false" class="text-gray-400 hover:text-white text-2xl leading-none">&times;</button>
                </div>

                <form action="{{ route('properties.assets.store', $property) }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1">Nama Aset / Barang</label>
                        <input type="text" name="name" required placeholder="Contoh: AC Living Room"
                            class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1">Kategori</label>
                        <input type="text" name="category" required placeholder="Elektronik, Sanitasi, Utility, Vehicle"
                            class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-1">Tgl Pembelian</label>
                            <input type="date" name="purchase_date"
                                class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-1">Nilai Estimasi</label>
                            <input type="number" name="purchase_price" min="0" step="1000" placeholder="0"
                                class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1">Status</label>
                        <select name="condition" required
                            class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                            <option value="Normal">Normal</option>
                            <option value="Perlu Servis">Perlu Servis</option>
                            <option value="Rusak">Rusak</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1">Catatan</label>
                        <textarea name="description" rows="3" placeholder="Deskripsi aset..."
                            class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none"></textarea>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-700">
                        <button type="button" @click="assetModal = false" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded-xl text-xs font-medium text-gray-300">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 rounded-xl text-xs font-medium text-white shadow-lg shadow-indigo-600/30">Simpan Aset</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="logModal" x-cloak
            class="fixed inset-0 z-50 overflow-y-auto bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
            <div @click.away="logModal = false" class="bg-gray-800 border border-gray-700 rounded-2xl max-w-lg w-full p-6 space-y-6 shadow-2xl">
                <div class="flex justify-between items-center border-b border-gray-700 pb-4">
                    <h3 class="text-lg font-bold text-white">Tambah Log Perawatan</h3>
                    <button @click="logModal = false" class="text-gray-400 hover:text-white text-2xl leading-none">&times;</button>
                </div>

                <form action="{{ route('properties.maintenance_logs.store', $property) }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1">Pilih Aset</label>
                        <select name="asset_id" required
                            class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                            <option value="">-- Pilih aset --</option>
                            @foreach($property->assets as $asset)
                                <option value="{{ $asset->id }}">{{ $asset->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1">Jenis Perawatan</label>
                        <select name="type" required
                            class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                            <option value="Routine Checkup">Routine Checkup</option>
                            <option value="Repair">Repair</option>
                            <option value="Replacement">Replacement</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-1">Tanggal Servis</label>
                            <input type="date" name="service_date" required
                                class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-1">Biaya (Rp)</label>
                            <input type="number" name="cost" min="0" step="1000" required placeholder="0"
                                class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1">Catatan / Keterangan</label>
                        <textarea name="notes" rows="3" placeholder="Contoh: Cuci AC dan isi freon"
                            class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none"></textarea>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-700">
                        <button type="button" @click="logModal = false" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded-xl text-xs font-medium text-gray-300">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 rounded-xl text-xs font-medium text-white shadow-lg shadow-indigo-600/30">Simpan Log</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="editAssetModal" x-cloak
            class="fixed inset-0 z-50 overflow-y-auto bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
            <div @click.away="editAssetModal = false" class="bg-gray-800 border border-gray-700 rounded-2xl max-w-lg w-full p-6 space-y-6 shadow-2xl">
                <div class="flex justify-between items-center border-b border-gray-700 pb-4">
                    <h3 class="text-lg font-bold text-white">Edit Aset</h3>
                    <button @click="editAssetModal = false" class="text-gray-400 hover:text-white text-2xl leading-none">&times;</button>
                </div>

                <form :action="`{{ route('properties.show', $property) }}/assets/${assetForm.id}`" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1">Nama Aset / Barang</label>
                        <input type="text" name="name" x-model="assetForm.name" required
                            class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1">Kategori</label>
                        <input type="text" name="category" x-model="assetForm.category" required
                            class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-1">Tgl Pembelian</label>
                            <input type="date" name="purchase_date" x-model="assetForm.purchase_date"
                                class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-1">Nilai Estimasi</label>
                            <input type="number" name="purchase_price" x-model="assetForm.purchase_price" min="0" step="1000"
                                class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1">Status</label>
                        <select name="condition" x-model="assetForm.condition" required
                            class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                            <option value="Normal">Normal</option>
                            <option value="Perlu Servis">Perlu Servis</option>
                            <option value="Rusak">Rusak</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1">Catatan</label>
                        <textarea name="description" x-model="assetForm.description" rows="3"
                            class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none"></textarea>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-700">
                        <button type="button" @click="editAssetModal = false" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded-xl text-xs font-medium text-gray-300">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 rounded-xl text-xs font-medium text-white shadow-lg shadow-indigo-600/30">Update Aset</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="editLogModal" x-cloak
            class="fixed inset-0 z-50 overflow-y-auto bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
            <div @click.away="editLogModal = false" class="bg-gray-800 border border-gray-700 rounded-2xl max-w-lg w-full p-6 space-y-6 shadow-2xl">
                <div class="flex justify-between items-center border-b border-gray-700 pb-4">
                    <h3 class="text-lg font-bold text-white">Edit Log Perawatan</h3>
                    <button @click="editLogModal = false" class="text-gray-400 hover:text-white text-2xl leading-none">&times;</button>
                </div>

                <form :action="`{{ route('properties.show', $property) }}/maintenance-logs/${logForm.id}`" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1">Jenis Perawatan</label>
                        <select name="type" x-model="logForm.type" required
                            class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                            <option value="Routine Checkup">Routine Checkup</option>
                            <option value="Repair">Repair</option>
                            <option value="Replacement">Replacement</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-1">Tanggal Servis</label>
                            <input type="date" name="service_date" x-model="logForm.service_date" required
                                class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-1">Biaya (Rp)</label>
                            <input type="number" name="cost" x-model="logForm.cost" min="0" step="1000" required
                                class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1">Catatan / Keterangan</label>
                        <textarea name="notes" x-model="logForm.notes" rows="3"
                            class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none"></textarea>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-700">
                        <button type="button" @click="editLogModal = false" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded-xl text-xs font-medium text-gray-300">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 rounded-xl text-xs font-medium text-white shadow-lg shadow-indigo-600/30">Update Log</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
