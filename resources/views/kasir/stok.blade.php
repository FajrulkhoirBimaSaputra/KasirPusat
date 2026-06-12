<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pantau Stok Bahan & Packaging') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                    class="mb-6 p-4 bg-emerald-100 border-l-4 border-emerald-500 text-emerald-700 rounded-r-xl shadow-sm">
                    <p class="text-sm font-bold">{{ session('success') }}</p>
                </div>
            @endif

            @if ($errors->any())
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                    class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-r-xl shadow-sm">
                    <ul class="list-disc list-inside text-sm font-bold">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @forelse($ingredients as $item)
                    <div
                        class="bg-white overflow-hidden shadow-sm rounded-3xl border border-gray-100 transition-all hover:shadow-md">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div class="bg-gray-100 p-2 rounded-xl">
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                </div>
                                <span
                                    class="text-[10px] font-black uppercase tracking-widest text-gray-400">Inventory</span>
                            </div>

                            <h3 class="text-lg font-extrabold text-gray-800 mb-1 truncate" title="{{ $item->nama }}">
                                {{ $item->nama }}</h3>

                            <div class="flex items-baseline gap-1 mb-6">
                                <span class="text-3xl font-black text-primary">{{ $item->stok }}</span>
                                <span class="text-xs font-medium text-gray-500 italic">tersisa</span>
                            </div>

                            <hr class="border-gray-50 mb-4">

                            <form action="{{ route('kasir.updateStok', $item->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="space-y-3">
                                    <label class="text-[10px] font-bold text-gray-400 uppercase ml-1">Lapor Sisa
                                        Fisik:</label>
                                    <div class="flex gap-2">
                                        <input type="number" name="stok" value="{{ $item->stok }}" min="0"
                                            max="{{ $item->stok }}"
                                            class="block w-full rounded-xl border-gray-200 bg-gray-50 text-sm font-bold focus:border-primary focus:ring-primary transition"
                                            required>
                                        <button type="submit"
                                            class="bg-dark text-white px-4 py-2 rounded-xl hover:bg-gray-800 transition shadow-lg shadow-dark/10"
                                            title="Simpan Laporan">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        @if ($item->stok <= 5)
                            <div class="bg-amber-50 py-2 px-6 border-t border-amber-100">
                                <p
                                    class="text-[10px] font-bold text-amber-600 uppercase tracking-tighter flex items-center gap-1.5">
                                    <span class="relative flex h-2 w-2">
                                        <span
                                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                                    </span>
                                    Perhatikan Sisa Stok
                                </p>
                            </div>
                        @endif
                    </div>
                @empty
                    <div
                        class="col-span-full bg-white p-12 rounded-3xl text-center border-2 border-dashed border-gray-200">
                        <p class="text-gray-400 font-medium">Belum ada data bahan baku yang didaftarkan Admin.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
