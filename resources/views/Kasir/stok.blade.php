<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pantau Stok Bahan & Packaging') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Alert Notifikasi -->
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-r-xl shadow-sm animate-pulse">
                    <p class="text-sm font-bold">{{ session('success') }}</p>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @forelse($ingredients as $item)
                    <div class="bg-white overflow-hidden shadow-sm rounded-3xl border border-gray-100 transition-all hover:shadow-md">
                        <div class="p-6">
                            <!-- Label Nama -->
                            <div class="flex justify-between items-start mb-4">
                                <div class="bg-gray-100 p-2 rounded-xl">
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                </div>
                                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Inventory</span>
                            </div>

                            <h3 class="text-lg font-extrabold text-gray-800 mb-1 truncate">{{ $item->nama }}</h3>
                            
                            <!-- Display Stok -->
                            <div class="flex items-baseline gap-1 mb-6">
                                <span class="text-3xl font-black text-primary">{{ $item->stok }}</span>
                                <span class="text-xs font-medium text-gray-500 italic">tersisa</span>
                            </div>

                            <hr class="border-gray-50 mb-4">

                            <!-- Form Update Stok untuk Kasir -->
                            <form action="{{ route('kasir.updateStok', $item) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="space-y-3">
                                    <label class="text-[10px] font-bold text-gray-400 uppercase ml-1">Update Jumlah Baru:</label>
                                    <div class="flex gap-2">
                                        <input type="number" 
                                               name="stok" 
                                               value="{{ $item->stok }}" 
                                               min="0"
                                               class="block w-full rounded-xl border-gray-200 bg-gray-50 text-sm font-bold focus:border-primary focus:ring-primary transition"
                                               required>
                                        <button type="submit" 
                                                class="bg-dark text-white px-4 py-2 rounded-xl hover:bg-gray-800 transition shadow-lg shadow-dark/10">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Indikator Stok Menipis -->
                        @if($item->stok <= 10)
                            <div class="bg-red-50 py-2 px-6 border-t border-red-100">
                                <p class="text-[10px] font-bold text-red-500 uppercase tracking-tighter flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 bg-red-500 rounded-full animate-ping"></span>
                                    Segera Restock!
                                </p>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="col-span-full bg-white p-12 rounded-3xl text-center border-2 border-dashed border-gray-200">
                        <p class="text-gray-400 font-medium">Belum ada data bahan baku yang didaftarkan Admin.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>