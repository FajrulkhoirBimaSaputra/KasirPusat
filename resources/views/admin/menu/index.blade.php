<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
            <h2 class="font-bold text-xl text-gray-800 leading-tight">
                Manajemen Menu
            </h2>
        </div>
    </x-slot>

    <div>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Katalog Produk</h3>
                <p class="text-sm text-gray-500 mt-1">Atur ketersediaan menu, kategori, dan harga untuk kasir.</p>
            </div>

            <a href="{{ route('menu.create') }}"
                class="inline-flex items-center justify-center px-4 py-2.5 bg-primary border border-transparent rounded-xl shadow-sm text-sm font-bold text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all duration-200 transform hover:-translate-y-0.5">
                <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Tambah Menu Baru
            </a>
        </div>

        @if (session('success'))
            <div class="mb-6 flex items-center p-4 mb-4 text-emerald-800 rounded-xl bg-emerald-50 border border-emerald-200"
                role="alert">
                <svg class="flex-shrink-0 w-5 h-5 mr-3 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                </svg>
                <div class="text-sm font-medium">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        <div class="block md:hidden space-y-4">
            @forelse($menus as $menu)
                <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex gap-4">
                    <img src="{{ $menu->image_path ? asset('storage/' . $menu->image_path) : 'https://ui-avatars.com/api/?name=' . $menu->nama . '&background=random' }}"
                        alt="{{ $menu->nama }}"
                        class="h-20 w-20 object-cover rounded-xl border border-gray-100 flex-shrink-0">

                    <div class="flex-1 flex flex-col justify-between min-w-0">
                        <div>
                            <h4 class="font-bold text-gray-900 text-sm truncate">{{ $menu->nama }}</h4>
                            <span
                                class="inline-block mt-1 px-2 py-0.5 bg-gray-100 text-gray-600 text-[10px] rounded uppercase font-bold tracking-wider">
                                {{ $menu->jenis }}
                            </span>
                        </div>

                        <div class="flex items-end justify-between mt-2">
                            <span class="font-bold text-primary text-sm">
                                Rp {{ number_format($menu->harga, 0, ',', '.') }}
                            </span>

                            <div class="flex items-center gap-1.5">
                                <a href="{{ route('menu.edit', $menu) }}"
                                    class="p-1.5 text-blue-500 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                        </path>
                                    </svg>
                                </a>
                                <form action="{{ route('menu.destroy', $menu) }}" method="POST" class="inline m-0 p-0"
                                    onsubmit="return confirm('Hapus menu ini?');">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="p-1.5 text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 text-center">
                    <p class="text-gray-500">Belum ada menu yang ditambahkan.</p>
                </div>
            @endforelse
        </div>

        <div class="hidden md:block bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Produk</th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Kategori</th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Harga</th>
                            <th scope="col"
                                class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($menus as $menu)
                            <tr class="hover:bg-gray-50/50 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-4">
                                        <img src="{{ $menu->image_path ? asset('storage/' . $menu->image_path) : 'https://ui-avatars.com/api/?name=' . $menu->nama . '&background=random' }}"
                                            alt="{{ $menu->nama }}"
                                            class="h-12 w-12 object-cover rounded-xl border border-gray-200 flex-shrink-0">
                                        <div class="text-sm font-bold text-gray-900">{{ $menu->nama }}</div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-gray-100 text-gray-700">
                                        {{ $menu->jenis }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-bold text-primary">
                                        Rp {{ number_format($menu->harga, 0, ',', '.') }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('menu.edit', $menu) }}"
                                            class="text-blue-500 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 p-2 rounded-lg transition-colors"
                                            title="Edit Menu">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                        </a>

                                        <form action="{{ route('menu.destroy', $menu) }}" method="POST"
                                            class="inline m-0 p-0"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus menu ini?');">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 p-2 rounded-lg transition-colors"
                                                title="Hapus Menu">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                    <p class="text-base font-semibold text-gray-900">Belum ada menu</p>
                                    <p class="text-sm mt-1">Silakan tambah produk baru ke katalog.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if (method_exists($menus, 'hasPages') && $menus->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                    {{ $menus->links() }}
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
