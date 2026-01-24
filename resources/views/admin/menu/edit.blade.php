<x-app-layout>
    <div class="max-w-3xl mx-auto px-4 py-10">
        <div class="bg-secondary rounded-2xl shadow-sm border border-accent/20 overflow-hidden">
            <div class="px-8 py-6 border-b border-accent/10 bg-dark text-white">
                <h1 class="text-2xl font-bold">Edit Menu</h1>
                <p class="text-accent/80 text-sm">Sesuaikan detail produk katalog Anda.</p>
            </div>

            <form method="POST" action="{{ route('menu.update', $menu) }}" enctype="multipart/form-data" class="p-8 space-y-6">
                @csrf @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- Jenis --}}
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700">Jenis Menu</label>
                        <select name="jenis" required
                            class="w-full rounded-xl border-gray-200 focus:border-primary focus:ring-primary text-sm transition">
                            <option value="klasik" {{ $menu->jenis == 'klasik' ? 'selected' : '' }}>KLASIK</option>
                            <option value="asli buatan pusat" {{ $menu->jenis == 'asli buatan pusat' ? 'selected' : '' }}>ASLI BUATAN PUSAT</option>
                            <option value="non-kopi" {{ $menu->jenis == 'non-kopi' ? 'selected' : '' }}>NON-KOPI</option>
                            <option value="makanan" {{ $menu->jenis == 'makanan' ? 'selected' : '' }}>MAKANAN</option>
                        </select>
                    </div>

                    {{-- Nama --}}
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700">Nama Menu</label>
                        <input type="text" name="nama"
                            value="{{ old('nama', $menu->nama) }}"
                            class="w-full rounded-xl border-gray-200 focus:border-primary focus:ring-primary text-sm transition"
                            required>
                    </div>

                    {{-- Harga --}}
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700">Harga (Rp)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">Rp</span>
                            <input type="number" name="harga"
                                value="{{ old('harga', $menu->harga) }}"
                                class="w-full pl-11 rounded-xl border-gray-200 focus:border-primary focus:ring-primary text-sm transition"
                                required>
                        </div>
                    </div>

                    {{-- IMAGE (TAMBAH PREVIEW BARU TANPA UBAH UI) --}}
                    <div class="space-y-2 md:col-span-2"
                        x-data="{
                            preview: null,
                            oldImage: '{{ $menu->image_path ? asset('storage/'.$menu->image_path) : '' }}'
                        }"
                    >
                        <label class="text-sm font-semibold text-gray-700">Foto Produk</label>

                        <div class="flex flex-col md:flex-row items-start md:items-center gap-6 p-4 border-2 border-dashed border-gray-200 rounded-2xl bg-gray-50/30">

                            {{-- Preview --}}
                            <div class="relative group">
                                <template x-if="preview">
                                    <img :src="preview"
                                        class="h-28 w-28 object-cover rounded-xl shadow-md border border-white">
                                </template>

                                <template x-if="!preview && oldImage">
                                    <div class="relative">
                                        <img :src="oldImage"
                                            class="h-28 w-28 object-cover rounded-xl shadow-md border border-white">
                                        <span class="absolute -top-2 -right-2 bg-accent text-black text-[10px] px-2 py-1 rounded-full shadow-sm">
                                            Foto Saat Ini
                                        </span>
                                    </div>
                                </template>

                                <template x-if="!preview && !oldImage">
                                    <div class="h-28 w-28 bg-gray-200 rounded-xl flex items-center justify-center text-gray-400">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                </template>
                            </div>

                            {{-- Input --}}
                            <div class="flex-1 space-y-1">
                                <input type="file" name="image"
                                    accept="image/*"
                                    @change="
                                        const file = $event.target.files[0];
                                        if (file) preview = URL.createObjectURL(file)
                                    "
                                    class="block w-full text-sm text-gray-500
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-full file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-primary file:text-white
                                    hover:file:bg-primary transition">

                                <p class="text-xs text-gray-400 italic mt-2">
                                    *Biarkan kosong jika tidak ingin mengubah foto. Format: JPG, PNG (Maks 2MB)
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action --}}
                <div class="pt-6 border-t border-accent/10 flex justify-end gap-3">
                    <a href="{{ route('menu.index') }}"
                        class="px-6 py-2.5 text-sm font-bold text-dark hover:bg-accent/10 rounded-xl transition">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-8 py-2.5 bg-primary hover:bg-primary-dark text-white font-bold rounded-xl shadow-lg shadow-primary/20 transition-all">
                        Update Menu
                    </button>
                </div>

            </form>
        </div>
    </div>
</x-app-layout>
