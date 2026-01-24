<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-dark tracking-tight">Manajemen Menu</h1>
                <p class="text-gray-500 mt-1">Atur ketersediaan produk katalog Anda.</p>
            </div>

            <a href="{{ route('menu.create') }}"
               class="inline-flex items-center justify-center gap-2 bg-primary hover:bg-primary-dark 
                      text-white font-semibold px-6 py-3 rounded-xl shadow-lg shadow-primary/20 
                      transition-all duration-200 transform hover:-translate-y-0.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Menu Baru
            </a>
        </div>

        <div class="bg-secondary rounded-2xl shadow-sm border border-accent/30 overflow-hidden">
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-accent/10 border-b border-accent/20 text-dark text-xs uppercase tracking-wider font-bold">
                            <th class="px-6 py-4 text-center w-16">#</th>
                            <th class="px-6 py-4">Produk</th>
                            <th class="px-6 py-4">Kategori</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-accent/10">
                        @forelse($menus as $menu)
                        <tr class="hover:bg-accent/5 transition-colors group">
                            <td class="px-6 py-4 text-center text-dark/60 font-medium">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <img src="{{ $menu->image_path ? asset('storage/'.$menu->image_path) : 'https://ui-avatars.com/api/?name='.$menu->nama }}" 
                                         class="h-12 w-12 object-cover rounded-xl border border-accent/20">
                                    <div class="text-sm font-bold text-dark">{{ $menu->nama }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="bg-dark text-accent px-3 py-1 rounded-full text-[10px] font-bold uppercase">
                                    {{ $menu->jenis }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('menu.edit', $menu) }}" class="p-2 text-dark hover:text-primary transition"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-width="2"></path></svg></a>
                                    <form action="{{ route('menu.destroy', $menu) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button onclick="return confirm('Hapus?')" class="p-2 text-primary hover:text-primary-dark transition"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2"></path></svg></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-6 py-10 text-center text-dark/40">Belum ada menu.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>