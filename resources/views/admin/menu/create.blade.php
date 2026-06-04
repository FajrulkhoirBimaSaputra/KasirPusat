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
                Tambah Menu Baru
            </h2>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mt-2">

        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
            <p class="text-sm text-gray-500 font-medium">Masukkan informasi detail menu, harga, dan foto untuk
                ditampilkan di sistem kasir.</p>
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
                    <span class="font-bold text-sm">Gagal menyimpan menu:</span>
                    <ul class="text-sm list-disc list-inside mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('menu.store') }}" enctype="multipart/form-data"
            class="p-6 sm:p-8 space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Nama Menu --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Produk / Menu <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="nama" value="{{ old('nama') }}" required autofocus
                        class="block w-full bg-gray-50 border-gray-200 focus:bg-white focus:border-primary focus:ring focus:ring-primary/20 rounded-xl transition duration-200"
                        placeholder="Contoh: Caramel Macchiato">
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
                        <option value="" disabled {{ old('jenis') ? '' : 'selected' }}>Pilih Kategori Produk
                        </option>
                        <option value="klasik" {{ old('jenis') == 'klasik' ? 'selected' : '' }}>KLASIK</option>
                        <option value="asli buatan pusat" {{ old('jenis') == 'asli buatan pusat' ? 'selected' : '' }}>
                            ASLI BUATAN PUSAT</option>
                        <option value="non-kopi" {{ old('jenis') == 'non-kopi' ? 'selected' : '' }}>NON-KOPI</option>
                        <option value="makanan" {{ old('jenis') == 'makanan' ? 'selected' : '' }}>MAKANAN</option>
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
                    <input type="number" name="harga" value="{{ old('harga') }}" required min="0"
                        step="100"
                        class="block w-full pl-12 bg-gray-50 border-gray-200 focus:bg-white focus:border-primary focus:ring focus:ring-primary/20 rounded-xl transition duration-200"
                        placeholder="0">
                </div>
                <p class="text-xs text-gray-500 mt-1">Masukkan nominal angka tanpa titik (contoh: 15000)</p>
                @error('harga')
                    <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div class="border-t border-gray-100 pt-6 mt-6"></div>

            {{-- FOTO + PREVIEW (Alpine.js) --}}
            <div x-data="{ preview: null }" class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Foto Produk <span
                        class="text-gray-400 font-normal">(Opsional)</span></label>

                <label
                    class="flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-2xl bg-gray-50 hover:bg-gray-100 hover:border-primary/50 transition-colors cursor-pointer p-8 relative overflow-hidden group">

                    {{-- Preview Area --}}
                    <template x-if="preview">
                        <div class="relative z-10 w-full flex flex-col items-center">
                            <img :src="preview"
                                class="h-48 w-48 object-cover rounded-2xl shadow-md border-4 border-white mb-3">
                            <span class="text-sm font-medium text-primary bg-primary/10 px-3 py-1 rounded-full">Ganti
                                Foto</span>
                        </div>
                    </template>

                    {{-- Placeholder Area --}}
                    <template x-if="!preview">
                        <div class="text-center relative z-10">
                            <div
                                class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-white shadow-sm mb-4 group-hover:scale-110 transition-transform duration-300">
                                <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 48 48"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28" />
                                </svg>
                            </div>
                            <p class="text-sm text-gray-700 font-bold mb-1">Klik untuk upload foto</p>
                            <p class="text-xs text-gray-500">Maksimal ukuran file 2MB (PNG, JPG, JPEG)</p>
                        </div>
                    </template>

                    <input type="file" name="image" accept="image/png, image/jpeg, image/jpg" class="hidden"
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
                        ">
                </label>
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
                    Simpan Menu
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
