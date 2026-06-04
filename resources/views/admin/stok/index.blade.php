<x-app-layout>
    <div x-data="{
        openModal: false,
        openHistoryModal: false,
        editMode: false,
        currentItem: { id: '', nama: '', stok: '' },
        activeIngredientName: '',
        activeHistories: [],
        filterDate: '', // State untuk filter tanggal
    
        // Fungsi dinamis url action form
        getActionUrl() {
            return this.editMode ? '/admin/stok/' + this.currentItem.id : '/admin/stok';
        },
    
        // Computed property untuk memfilter riwayat berdasarkan tanggal
        get filteredHistories() {
            if (!this.filterDate) return this.activeHistories;
            return this.activeHistories.filter(history => {
                // history.created_at format aslinya YYYY-MM-DDTHH:mm:ss.000000Z
                return history.created_at.startsWith(this.filterDate);
            });
        }
    }">

        <x-slot name="header">
            <div class="flex items-center gap-2">
                <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">
                    Manajemen Stok Bahan
                </h2>
            </div>
        </x-slot>

        <div>
            {{-- HEADER HALAMAN & TOMBOL TAMBAH --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Log Pembaruan Stok</h3>
                    <p class="text-sm text-gray-500 mt-1">Pantau pergerakan masuk dan keluarnya bahan baku secara
                        real-time.</p>
                </div>

                <button @click="openModal = true; editMode = false; currentItem = {id: '', nama: '', stok: ''}"
                    class="inline-flex items-center justify-center px-4 py-2.5 bg-primary border border-transparent rounded-xl shadow-sm text-sm font-bold text-white hover:bg-primary-dark transition-all duration-200 transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Tambah Bahan Baku
                </button>
            </div>

            {{-- ALERT NOTIFIKASI --}}
            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition.duration.500ms
                    class="mb-6 flex items-center p-4 text-emerald-800 rounded-xl bg-emerald-50 border border-emerald-200">
                    <svg class="flex-shrink-0 w-5 h-5 mr-3 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    <div class="text-sm font-medium">{{ session('success') }}</div>
                </div>
            @endif

            @if (session('error'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition.duration.500ms
                    class="mb-6 flex items-center p-4 text-red-800 rounded-xl bg-red-50 border border-red-200">
                    <svg class="flex-shrink-0 w-5 h-5 mr-3 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                    <div class="text-sm font-medium">{{ session('error') }}</div>
                </div>
            @endif

            {{-- TAMPILAN MOBILE (Card View) --}}
            <div class="block md:hidden space-y-4 mb-6">
                @forelse($ingredients as $index => $item)
                    @php $lastHistory = $item->histories->first(); @endphp
                    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
                        <div class="flex items-start justify-between gap-4 mb-3">
                            <div class="flex-1 min-w-0">
                                <h4 class="font-bold text-gray-900 text-base truncate">{{ $item->nama }}</h4>
                                <div class="flex items-center gap-2 mt-1.5">
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-bold {{ $item->stok < 5 ? 'bg-red-100 text-red-600' : 'bg-primary/10 text-primary' }}">
                                        Stok: {{ $item->stok }}
                                    </span>

                                    @if ($item->stok <= 5)
                                        <span
                                            class="flex items-center gap-1 text-[9px] font-bold text-red-500 uppercase bg-red-50 px-1.5 py-0.5 rounded border border-red-100">
                                            <span class="relative flex h-1.5 w-1.5">
                                                <span
                                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                                <span
                                                    class="relative inline-flex rounded-full h-1.5 w-1.5 bg-red-500"></span>
                                            </span>
                                            Restok
                                        </span>
                                    @endif

                                    {{-- Keterangan +/- di Mobile --}}
                                    @if ($lastHistory && $lastHistory->difference != 0)
                                        <span
                                            class="text-[10px] font-black {{ $lastHistory->difference > 0 ? 'text-emerald-500' : 'text-red-500' }}">
                                            ({{ $lastHistory->difference > 0 ? '+' : '' }}{{ $lastHistory->difference }})
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Tombol Aksi Mobile --}}
                            <div class="flex items-center gap-1">
                                <button
                                    @click="openHistoryModal = true; activeIngredientName = '{{ addslashes($item->nama) }}'; activeHistories = {{ json_encode($item->histories) }}; filterDate = '';"
                                    class="p-2 text-purple-500 hover:bg-purple-50 rounded-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </button>
                                <button
                                    @click="openModal = true; editMode = true; currentItem = { id: '{{ $item->id }}', nama: '{{ addslashes($item->nama) }}', stok: '{{ $item->stok }}' }"
                                    class="p-2 text-blue-500 hover:bg-blue-50 rounded-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <form action="{{ route('admin.stok.destroy', $item->id) }}" method="POST"
                                    class="m-0 p-0"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus bahan ini?');">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="p-2 text-red-500 hover:bg-red-50 border border-transparent hover:border-red-200 rounded-lg transition-all"
                                        title="Hapus Bahan">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white p-6 rounded-2xl text-center text-gray-500 text-sm">Belum ada data bahan baku.
                    </div>
                @endforelse
            </div>

            {{-- TAMPILAN DESKTOP (Table View) --}}
            <div class="hidden md:block bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50/50 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider w-16">No
                                </th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Nama
                                    Bahan</th>
                                <th
                                    class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Sisa Stok</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Update
                                    Terakhir</th>
                                <th
                                    class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($ingredients as $index => $item)
                                @php $lastHistory = $item->histories->first(); @endphp
                                <tr class="hover:bg-gray-50/50 transition-colors duration-200">
                                    <td class="px-6 py-4 text-gray-500 font-medium text-sm">{{ $index + 1 }}</td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-bold text-gray-900">{{ $item->nama }}</div>
                                    </td>

                                    {{-- Kolom Sisa Stok dengan Keterangan +/- --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex flex-col items-center justify-center gap-1">
                                            <span
                                                class="inline-flex items-center justify-center px-3 py-1 rounded-lg text-sm font-bold {{ $item->stok < 5 ? 'bg-red-100 text-red-600 border border-red-200' : 'bg-primary/10 text-primary border border-primary/20' }} min-w-[3rem]">
                                                {{ $item->stok }}
                                            </span>

                                            @if ($item->stok <= 5)
                                                <span
                                                    class="flex items-center gap-1 text-[9px] font-bold text-red-500 uppercase tracking-widest mt-0.5">
                                                    <span class="relative flex h-1.5 w-1.5">
                                                        <span
                                                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                                        <span
                                                            class="relative inline-flex rounded-full h-1.5 w-1.5 bg-red-500"></span>
                                                    </span>
                                                    SEGERA RESTOK
                                                </span>
                                            @endif

                                            {{-- Label kecil penanda perubahan terakhir --}}
                                            @if ($lastHistory && $lastHistory->difference != 0)
                                                <span
                                                    class="text-[10px] font-black tracking-wide {{ $lastHistory->difference > 0 ? 'text-emerald-500' : 'text-red-500' }}">
                                                    {{ $lastHistory->difference > 0 ? '+' : '' }}{{ $lastHistory->difference }}
                                                    <span
                                                        class="font-medium text-gray-400">({{ substr($lastHistory->user->name ?? '?', 0, 8) }})</span>
                                                </span>
                                            @elseif(!$lastHistory)
                                                <span class="text-[10px] font-medium text-gray-400">Baru</span>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 font-bold text-xs uppercase shrink-0">
                                                {{ substr($item->user->name ?? 'A', 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="text-sm font-bold text-gray-800">
                                                    {{ $item->user->name ?? 'System' }}</div>
                                                <div
                                                    class="text-[10px] text-gray-500 font-medium tracking-wide mt-0.5">
                                                    {{ $item->updated_at->diffForHumans() }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            {{-- Tombol Riwayat --}}
                                            <button
                                                @click="openHistoryModal = true; activeIngredientName = '{{ addslashes($item->nama) }}'; activeHistories = {{ json_encode($item->histories) }}; filterDate = '';"
                                                class="p-2 text-purple-500 hover:bg-purple-50 border border-transparent hover:border-purple-200 rounded-lg transition-all"
                                                title="Lihat Riwayat">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </button>

                                            {{-- Tombol Edit --}}
                                            <button
                                                @click="openModal = true; editMode = true; currentItem = { id: '{{ $item->id }}', nama: '{{ addslashes($item->nama) }}', stok: '{{ $item->stok }}' }"
                                                class="p-2 text-blue-500 hover:bg-blue-50 border border-transparent hover:border-blue-200 rounded-lg transition-all"
                                                title="Edit Stok">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>

                                            {{-- Tombol Hapus --}}
                                            <form action="{{ route('admin.stok.destroy', $item->id) }}"
                                                method="POST" class="m-0 p-0"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus bahan ini?');">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="p-2 text-red-500 hover:bg-red-50 border border-transparent hover:border-red-200 rounded-lg transition-all"
                                                    title="Hapus Bahan">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                                        <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                        <p class="font-medium">Belum ada data bahan baku.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- MODAL TAMBAH/EDIT BAHAN --}}
        <div x-show="openModal" class="fixed inset-0 z-[100] overflow-y-auto" x-cloak>
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="openModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm"
                    @click="openModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div x-show="openModal"
                    class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full">
                    <form :action="getActionUrl()" method="POST">
                        @csrf
                        <template x-if="editMode"><input type="hidden" name="_method" value="PUT"></template>
                        <div
                            class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                            <h3 class="text-lg font-bold text-gray-900"
                                x-text="editMode ? 'Edit Bahan Baku' : 'Tambah Bahan Baru'"></h3>
                            <button type="button" @click="openModal = false"
                                class="text-gray-400 hover:text-gray-600 focus:outline-none">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <div class="px-6 py-6 space-y-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Bahan Baku</label>
                                <input type="text" name="nama" x-model="currentItem.nama" required
                                    class="w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-primary focus:ring focus:ring-primary/20">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Update Stok Akhir</label>
                                <input type="number" name="stok" x-model="currentItem.stok" required
                                    min="0"
                                    class="w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-primary focus:ring focus:ring-primary/20">
                                <p class="text-[10px] text-gray-400 mt-1.5">*Sistem akan otomatis mencatat selisih stok
                                    (Riwayat).</p>
                            </div>
                        </div>
                        <div class="px-6 py-5 bg-gray-50/80 border-t flex flex-col sm:flex-row-reverse gap-3">
                            <button type="submit"
                                class="w-full sm:w-auto py-2.5 px-6 rounded-xl shadow-md text-sm font-bold text-white bg-primary hover:bg-primary-dark">Simpan
                                Data</button>
                            <button type="button" @click="openModal = false"
                                class="w-full sm:w-auto py-2.5 px-6 border border-gray-300 rounded-xl text-sm font-bold text-gray-700 bg-white hover:bg-gray-50">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- MODAL RIWAYAT TRACKING STOK (DENGAN FILTER TANGGAL) --}}
        <div x-show="openHistoryModal" class="fixed inset-0 z-[105] overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
                <div x-show="openHistoryModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm"
                    @click="openHistoryModal = false"></div>

                <div x-show="openHistoryModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    class="relative inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl w-full">

                    {{-- Header Modal & Filter --}}
                    <div
                        class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center bg-gray-50 gap-4">
                        <div>
                            <h3 class="text-lg font-black text-gray-900" x-text="activeIngredientName"></h3>
                            <p class="text-xs font-medium text-gray-500 mt-0.5">Riwayat Pergerakan Stok</p>
                        </div>

                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            <div class="relative w-full sm:w-auto">
                                <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <input type="date" x-model="filterDate"
                                    class="w-full text-xs font-bold text-gray-600 rounded-lg border-gray-200 pl-8 pr-3 py-1.5 focus:border-primary focus:ring-1 focus:ring-primary shadow-sm bg-white cursor-pointer"
                                    title="Saring berdasarkan tanggal">
                            </div>

                            <template x-if="filterDate">
                                <button @click="filterDate = ''"
                                    class="p-1.5 bg-red-50 text-red-500 hover:bg-red-100 rounded-lg transition-colors"
                                    title="Hapus Filter">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </template>
                            <template x-if="!filterDate">
                                <button @click="openHistoryModal = false"
                                    class="p-1.5 bg-gray-200 text-gray-500 hover:bg-gray-300 hover:text-gray-700 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </template>
                        </div>
                    </div>

                    {{-- Body: Timeline Riwayat --}}
                    <div class="px-6 py-6 max-h-[60vh] overflow-y-auto custom-scrollbar bg-white">
                        <div class="relative border-l-2 border-gray-100 ml-3 space-y-6">

                            <template x-for="history in filteredHistories" :key="history.id">
                                <div class="relative pl-6">
                                    <div class="absolute w-4 h-4 rounded-full -left-[9px] top-1 border-2 border-white shadow-sm"
                                        :class="history.difference > 0 ? 'bg-emerald-500' : (history.difference < 0 ?
                                            'bg-red-500' : 'bg-gray-400')">
                                    </div>

                                    <div
                                        class="bg-gray-50 p-4 rounded-xl border border-gray-100 hover:border-gray-200 transition-colors">
                                        <div class="flex justify-between items-start mb-3">
                                            <div class="flex items-center gap-2">
                                                <div class="w-5 h-5 rounded-full bg-gray-200 flex items-center justify-center text-[9px] font-black text-gray-600 uppercase"
                                                    x-text="(history.user && history.user.name) ? history.user.name.charAt(0) : 'S'">
                                                </div>
                                                <p class="text-sm font-bold text-gray-800"
                                                    x-text="history.user ? history.user.name : 'System'"></p>
                                            </div>
                                            <p class="text-[10px] font-bold text-gray-400"
                                                x-text="new Date(history.created_at).toLocaleString('id-ID', {day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute:'2-digit'})">
                                            </p>
                                        </div>

                                        <div
                                            class="flex items-center justify-between mt-2 pt-3 border-t border-gray-200/60 border-dashed">
                                            <div class="flex flex-col">
                                                <span
                                                    class="text-[9px] text-gray-400 uppercase font-black tracking-wider mb-0.5">Stok
                                                    Awal</span>
                                                <span class="text-sm font-bold text-gray-600"
                                                    x-text="history.old_stok"></span>
                                            </div>

                                            <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                            </svg>

                                            <div class="px-3 py-1 rounded-md border text-xs font-black shadow-sm"
                                                :class="history.difference > 0 ?
                                                    'bg-emerald-50 border-emerald-200 text-emerald-600' : (history
                                                        .difference < 0 ? 'bg-red-50 border-red-200 text-red-600' :
                                                        'bg-gray-100 border-gray-200 text-gray-600')"
                                                x-text="(history.difference > 0 ? '+' : '') + history.difference">
                                            </div>

                                            <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                            </svg>

                                            <div class="flex flex-col text-right">
                                                <span
                                                    class="text-[9px] text-gray-400 uppercase font-black tracking-wider mb-0.5">Stok
                                                    Akhir</span>
                                                <span class="text-sm font-black text-gray-900"
                                                    x-text="history.new_stok"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <template x-if="filteredHistories.length === 0">
                                <div class="py-8 text-center">
                                    <svg class="mx-auto h-10 w-10 text-gray-300 mb-2" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                    </svg>
                                    <p class="text-sm font-medium text-gray-500"
                                        x-text="filterDate ? 'Tidak ada riwayat pergerakan stok pada tanggal tersebut.' : 'Bahan ini belum memiliki riwayat stok.'">
                                    </p>
                                </div>
                            </template>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <style>
        [x-cloak] {
            display: none !important;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f8fafc;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</x-app-layout>
