<x-app-layout>
    <div x-data="{ 
        openModal: false, 
        editMode: false, 
        currentItem: {id: '', nama: '', stok: ''},
        getActionUrl() {
            return this.editMode ? '/admin/stok/' + this.currentItem.id : '/admin/stok';
        }
    }" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        <!-- HEADER -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-dark tracking-tight">Log Pembaruan Stok</h1>
                <p class="text-gray-500 mt-1">Daftar bahan baku beserta informasi kasir yang memperbarui stok.</p>
            </div>

            <button @click="openModal = true; editMode = false; currentItem = {id: '', nama: '', stok: ''}"
                class="inline-flex items-center justify-center gap-2 bg-primary hover:bg-primary-dark text-white font-semibold px-6 py-3 rounded-xl shadow-lg shadow-primary/20 transition-all transform hover:-translate-y-0.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Bahan
            </button>
        </div>

        <!-- NOTIFIKASI -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-r-xl shadow-sm">
                <span class="font-bold">Berhasil!</span> {{ session('success') }}
            </div>
        @endif

        <!-- TABEL DATA -->
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr
                        class="bg-gray-50/50 border-b border-gray-100 text-gray-400 text-[11px] tracking-widest font-black">
                        <th class="px-6 py-5 text-center w-16">#</th>
                        <th class="px-6 py-5">Nama Bahan</th>
                        <th class="px-6 py-5 text-center">Jumlah Stok</th>
                        <th class="px-6 py-5">Diperbarui Oleh</th>
                        <th class="px-6 py-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($ingredients as $index => $item)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 text-center text-gray-400 font-medium text-sm">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 font-bold text-gray-800">{{ $item->nama }}</td>
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="bg-gray-100 text-gray-800 px-4 py-1.5 rounded-xl font-black text-sm border border-gray-200">
                                    {{ $item->stok }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-xs uppercase">
                                        {{ substr($item->user->name ?? 'A', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-gray-800">{{ $item->user->name ?? 'System' }}
                                        </div>
                                        <div class="text-[10px] text-gray-400 font-medium uppercase tracking-tighter">
                                            {{ $item->updated_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-1">
                                    <!-- Edit -->
                                    <button
                                        @click="openModal = true; editMode = true; currentItem = { id: '{{ $item->id }}', nama: '{{ addslashes($item->nama) }}', stok: '{{ $item->stok }}' }"
                                        class="p-2 text-gray-400 hover:text-primary hover:bg-primary/5 rounded-lg transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>

                                    <!-- Delete -->
                                    <form action="{{ route('admin.stok.destroy', $item) }}" method="POST"
                                        onsubmit="return confirm('Hapus bahan ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1-1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400 italic">Belum ada data bahan baku.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- MODAL (TAMBAH / EDIT) -->
        <div x-show="openModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak x-transition.opacity>
            <div class="flex items-center justify-center min-h-screen px-4">
                <div @click="openModal = false" class="fixed inset-0 bg-dark/60 backdrop-blur-sm"></div>

                <div
                    class="bg-white rounded-3xl overflow-hidden shadow-2xl transform transition-all sm:max-w-md sm:w-full z-50 border border-gray-100">
                    <form :action="getActionUrl()" method="POST">
                        @csrf
                        <input type="hidden" name="_method" :value="editMode ? 'PUT' : 'POST'">

                        <div class="px-8 py-6 border-b border-gray-50 bg-gray-50/50">
                            <h3 class="text-xl font-black text-gray-800"
                                x-text="editMode ? 'Edit Bahan Baku' : 'Tambah Bahan Baru'"></h3>
                        </div>

                        <div class="p-8 space-y-5">
                            <div>
                                <label
                                    class="block text-[11px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Nama
                                    Bahan</label>
                                <input type="text" name="nama" x-model="currentItem.nama" required
                                    placeholder="Contoh: Cup 16oz"
                                    class="w-full rounded-2xl border-gray-100 bg-gray-50 font-bold text-gray-800 focus:border-primary focus:ring-primary shadow-sm">
                            </div>
                            <div>
                                <label
                                    class="block text-[11px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Jumlah
                                    Stok Fisik</label>
                                <input type="number" name="stok" x-model="currentItem.stok" required min="0"
                                    placeholder="0"
                                    class="w-full rounded-2xl border-gray-100 bg-gray-50 font-bold text-gray-800 focus:border-primary focus:ring-primary shadow-sm">
                            </div>
                        </div>

                        <div class="px-8 py-6 bg-gray-50/50 flex flex-col gap-2">
                            <button type="submit"
                                class="w-full bg-primary text-white py-3 rounded-2xl font-black shadow-lg shadow-primary/20 hover:bg-primary-dark transition">
                                Simpan Perubahan
                            </button>
                            <button type="button" @click="openModal = false"
                                class="w-full py-3 text-gray-400 font-bold text-sm">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>