<x-app-layout>
    <div class="max-w-3xl mx-auto px-4 py-10">
        <div class="bg-secondary rounded-2xl shadow-sm border border-accent/30 overflow-hidden">
            <div class="px-8 py-6 border-b border-accent/10 bg-accent/5">
                <h1 class="text-2xl font-bold text-dark">Tambah Menu Baru</h1>
                <p class="text-dark/60 text-sm">Masukkan informasi menu untuk ditampilkan di katalog.</p>
            </div>

            <form method="POST" action="{{ route('menu.store') }}" enctype="multipart/form-data" class="p-8 space-y-6">
                @csrf

                {{-- Kategori --}}
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-700">Kategori</label>
                    <select name="jenis" required
                        class="w-full rounded-xl border-gray-200 focus:border-primary focus:ring-primary text-sm transition">
                        <option value="" disabled selected>Pilih Kategori</option>
                        <option value="klasik">KLASIK</option>
                        <option value="asli buatan pusat">ASLI BUATAN PUSAT</option>
                        <option value="non-kopi">NON-KOPI</option>
                        <option value="makanan">MAKANAN</option>
                    </select>
                </div>

                {{-- Nama --}}
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-700">Nama Menu</label>
                    <input type="text" name="nama" required
                        class="w-full rounded-xl border-gray-200 focus:border-primary focus:ring-primary text-sm transition"
                        placeholder="Contoh: Caramel Macchiato">
                </div>

                {{-- Harga --}}
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-700">Harga Jual</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">Rp</span>
                        <input type="number" name="harga" required
                            class="w-full pl-11 rounded-xl border-gray-200 focus:border-primary focus:ring-primary text-sm transition"
                            placeholder="0">
                    </div>
                </div>

                {{-- FOTO + PREVIEW --}}
                <div
                    x-data="{ preview: null }"
                    class="space-y-2"
                >
                    <label class="text-sm font-semibold text-gray-700">Foto Produk</label>

                    <label
                        class="flex flex-col items-center justify-center border-2 border-dashed border-gray-200 rounded-2xl bg-gray-50/50 p-6 cursor-pointer hover:border-primary transition"
                    >
                        {{-- Preview --}}
                        <template x-if="preview">
                            <img
                                :src="preview"
                                class="h-40 w-40 object-cover rounded-xl shadow-md border-4 border-white mb-4"
                            >
                        </template>

                        {{-- Placeholder --}}
                        <template x-if="!preview">
                            <div class="text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 48 48" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28" />
                                </svg>
                                <p class="mt-3 text-sm text-gray-600 font-semibold">
                                    Klik untuk upload gambar
                                </p>
                                <p class="text-xs text-gray-400">PNG / JPG max 2MB</p>
                            </div>
                        </template>

                        <input
                            type="file"
                            name="image"
                            accept="image/*"
                            class="hidden"
                            @change="
                                const file = $event.target.files[0];
                                if (file) preview = URL.createObjectURL(file)
                            "
                        >
                    </label>
                </div>

                {{-- Submit --}}
                <div class="pt-6 border-t border-accent/10 flex justify-end">
                    <button type="submit"
                        class="px-10 py-3 bg-primary hover:bg-primary-dark text-white font-bold rounded-xl shadow-lg shadow-primary/20 transition active:scale-95">
                        Simpan Menu
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
