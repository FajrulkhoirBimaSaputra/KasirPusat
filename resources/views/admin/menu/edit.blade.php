<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('menu.index') }}"
                class="p-2 -ml-2 mr-1 text-gray-400 hover:text-primary hover:bg-primary/10 rounded-xl transition-colors focus:outline-none"
                title="Kembali ke Daftar Menu">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h2 class="font-bold text-xl text-gray-800 leading-tight">
                Edit Data Menu
            </h2>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mt-2">

        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
            <p class="text-sm text-gray-500 font-medium">Perbarui informasi nama produk, kategori, harga, atau ubah foto
                menu.</p>
        </div>

        {{-- Error Validation Summary --}}
        @if ($errors->any())
            <div class="mx-6 mt-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-start gap-3">
                <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <span class="font-bold text-sm">Gagal memperbarui menu:</span>
                    <ul class="text-sm list-disc list-inside mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('menu.update', $menu) }}" enctype="multipart/form-data"
            class="p-6 sm:p-8 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Nama Menu --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Produk / Menu <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="nama" value="{{ old('nama', $menu->nama) }}" required autofocus
                        class="block w-full bg-gray-50 border-gray-200 focus:bg-white focus:border-primary focus:ring focus:ring-primary/20 rounded-xl transition duration-200">
                    @error('nama')
                        <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Kategori --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Kategori <span
                            class="text-red-500">*</span></label>
                    <select name="jenis" required
                        class="block w-full bg-gray-50 border-gray-200 focus:bg-white focus:border-primary focus:ring focus:ring-primary/20 rounded-xl transition duration-200 text-gray-700">
                        <option value="klasik" {{ old('jenis', $menu->jenis) == 'klasik' ? 'selected' : '' }}>KLASIK
                        </option>
                        <option value="asli buatan pusat"
                            {{ old('jenis', $menu->jenis) == 'asli buatan pusat' ? 'selected' : '' }}>ASLI BUATAN PUSAT
                        </option>
                        <option value="non-kopi" {{ old('jenis', $menu->jenis) == 'non-kopi' ? 'selected' : '' }}>
                            NON-KOPI</option>
                        <option value="makanan" {{ old('jenis', $menu->jenis) == 'makanan' ? 'selected' : '' }}>MAKANAN
                        </option>
                    </select>
                    @error('jenis')
                        <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Harga --}}
            <div class="w-full md:w-1/2 md:pr-3">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Harga Jual <span
                        class="text-red-500">*</span></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <span class="text-gray-500 font-bold">Rp</span>
                    </div>
                    <input type="number" name="harga" value="{{ old('harga', $menu->harga) }}" required
                        min="0" step="100"
                        class="block w-full pl-12 bg-gray-50 border-gray-200 focus:bg-white focus:border-primary focus:ring focus:ring-primary/20 rounded-xl transition duration-200">
                </div>
                @error('harga')
                    <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div class="border-t border-gray-100 pt-6 mt-6"></div>

            {{-- FOTO + PREVIEW (Alpine.js) --}}
            <div x-data="{
                preview: null,
                oldImage: '{{ $menu->image_path ? asset('storage/' . $menu->image_path) : '' }}'
            }" class="space-y-2">

                <label class="block text-sm font-semibold text-gray-700 mb-1">Foto Produk <span
                        class="text-gray-400 font-normal">(Opsional)</span></label>

                <div
                    class="flex flex-col sm:flex-row items-start sm:items-center gap-6 p-5 border border-gray-200 rounded-2xl bg-gray-50/50">

                    {{-- Thumbnail Area --}}
                    <div class="relative group flex-shrink-0">
                        <template x-if="preview">
                            <div class="relative">
                                <img :src="preview"
                                    class="h-32 w-32 object-cover rounded-xl shadow-sm border border-gray-200">
                                <span
                                    class="absolute -top-2 -right-2 bg-emerald-500 text-white text-[10px] font-bold px-2 py-1 rounded-full shadow-sm">Baru</span>
                            </div>
                        </template>

                        <template x-if="!preview && oldImage">
                            <div class="relative">
                                <img :src="oldImage"
                                    class="h-32 w-32 object-cover rounded-xl shadow-sm border border-gray-200">
                                <span
                                    class="absolute -bottom-2 -left-2 bg-gray-800 text-white text-[10px] font-bold px-2 py-1 rounded-full shadow-sm">Saat
                                    Ini</span>
                            </div>
                        </template>

                        <template x-if="!preview && !oldImage">
                            <div
                                class="h-32 w-32 bg-gray-200 rounded-xl flex items-center justify-center text-gray-400 border border-gray-300 border-dashed">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </template>
                    </div>

                    {{-- Input Area --}}
                    <div class="flex-1 space-y-3">
                        <div>
                            <input type="file" name="image" accept="image/png, image/jpeg, image/jpg"
                                @change="
                                    const file = $event.target.files[0];
                                    if (file) {
                                        if(file.size > 2097152) {
                                            alert('Ukuran file terlalu besar! Maksimal 2MB.');
                                            $event.target.value = '';
                                            preview = null;
                                        } else {
                                            preview = URL.createObjectURL(file);
                                        }
                                    }
                                "
                                class="block w-full text-sm text-gray-600
                                file:mr-4 file:py-2.5 file:px-4
                                file:rounded-xl file:border-0
                                file:text-sm file:font-semibold
                                file:bg-primary file:text-white
                                hover:file:bg-primary-dark hover:file:cursor-pointer transition-colors">
                        </div>
                        <p class="text-xs text-gray-500 leading-relaxed">
                            Pilih file gambar untuk mengganti foto produk. <br>
                            Biarkan kosong jika Anda <strong>tidak ingin</strong> mengubah foto saat ini. <br>
                            Maksimal 2MB (JPG, JPEG, PNG).
                        </p>
                    </div>
                </div>
                @error('image')
                    <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex items-center justify-end pt-4 gap-3 border-t border-gray-50 mt-6">
                <a href="{{ route('menu.index') }}"
                    class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 transition-colors">
                    Batal
                </a>

                <button type="submit"
                    class="flex items-center justify-center py-2.5 px-6 rounded-xl shadow-md text-sm font-bold text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all duration-300 transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                    </svg>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
