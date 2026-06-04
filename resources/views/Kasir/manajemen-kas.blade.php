<x-app-layout>
    @php
        // Cek apakah hari ini sudah ada catatan "Modal Awal Shift"
        $latestShift = $kasHariIni->whereIn('keterangan', ['Modal Awal Shift', 'Tutup Shift'])->first();
        $belumBukaShift = !$latestShift || $latestShift->keterangan === 'Tutup Shift';
    @endphp

    <div class="max-w-5xl mx-auto p-4 sm:p-6 lg:p-8">

        {{-- HEADER --}}
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('kasir.ringkasan') }}"
                class="p-2 bg-white rounded-xl shadow-sm hover:bg-gray-50 text-gray-600 transition-colors border border-gray-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-black text-gray-800">Manajemen Kas Laci</h1>
                <p class="text-sm text-gray-500 font-medium mt-1">Catat aliran uang masuk dan keluar di luar transaksi
                    sistem.</p>
            </div>
        </div>

        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition
                class="mb-6 p-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center gap-3 shadow-sm">
                <div
                    class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <span class="font-bold text-emerald-700">{{ session('success') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6" x-data="{
            bukaShiftMode: {{ $belumBukaShift ? 'true' : 'false' }},
            jenis: '{{ $belumBukaShift ? 'pemasukan' : old('jenis') ?? '' }}',
            keterangan: '{{ $belumBukaShift ? 'Modal Awal Shift' : old('keterangan') ?? '' }}'
        }">

            {{-- BAGIAN KIRI: FORM CATAT KAS --}}
            <div class="lg:col-span-5 bg-white rounded-3xl shadow-sm border border-gray-100 p-6 h-fit">
                <h3 class="font-black text-lg text-gray-800 mb-5 flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Catat Kas Baru
                </h3>

                {{-- Alert Wajib Buka Shift --}}
                <template x-if="bukaShiftMode">
                    <div
                        class="mb-5 p-3.5 bg-blue-50 border border-blue-100 rounded-2xl flex gap-3 items-start animate-pulse">
                        <svg class="w-5 h-5 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-xs text-blue-800 font-medium leading-relaxed">Anda belum membuka shift hari ini.
                            Silakan masukkan <b>Modal Awal</b> laci terlebih dahulu.</p>
                    </div>
                </template>

                <form action="{{ route('kasir.store-kas') }}" method="POST">
                    @csrf

                    {{-- 1. Pilihan Jenis Kas --}}
                    <div class="mb-6">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Jenis Aliran
                            Dana <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-3 gap-3">

                            <!-- Pemasukan / Modal Awal -->
                            <label class="cursor-pointer relative">
                                <input type="radio" name="jenis" value="pemasukan" x-model="jenis"
                                    class="peer sr-only" required>
                                <div class="h-full rounded-2xl border-2 p-3 text-center transition-all duration-200"
                                    :class="jenis === 'pemasukan' ?
                                        'border-emerald-500 bg-emerald-50 text-emerald-700 shadow-sm' :
                                        'border-gray-100 bg-white text-gray-400 hover:border-emerald-200 hover:bg-emerald-50/50'">
                                    <svg class="w-6 h-6 mx-auto mb-1"
                                        :class="jenis === 'pemasukan' ? 'text-emerald-500' : 'text-gray-300'"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                    </svg>
                                    <span class="text-xs font-bold block"
                                        x-text="bukaShiftMode ? 'Modal Awal' : 'Masuk'"></span>
                                </div>
                            </label>

                            <!-- Pengeluaran -->
                            <label class="relative"
                                :class="bukaShiftMode ? 'cursor-not-allowed opacity-40' : 'cursor-pointer'">
                                <input type="radio" name="jenis" value="pengeluaran" x-model="jenis"
                                    class="peer sr-only" :disabled="bukaShiftMode">
                                <div class="h-full rounded-2xl border-2 p-3 text-center transition-all duration-200"
                                    :class="jenis === 'pengeluaran' ? 'border-red-500 bg-red-50 text-red-700 shadow-sm' :
                                        'border-gray-100 bg-white text-gray-400 ' + (!bukaShiftMode ?
                                            'hover:border-red-200 hover:bg-red-50/50' : '')">
                                    <svg class="w-6 h-6 mx-auto mb-1"
                                        :class="jenis === 'pengeluaran' ? 'text-red-500' : 'text-gray-300'"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                                    </svg>
                                    <span class="text-xs font-bold block">Keluar</span>
                                </div>
                            </label>

                            <!-- Refund -->
                            <label class="relative"
                                :class="bukaShiftMode ? 'cursor-not-allowed opacity-40' : 'cursor-pointer'">
                                <input type="radio" name="jenis" value="refund" x-model="jenis" class="peer sr-only"
                                    :disabled="bukaShiftMode">
                                <div class="h-full rounded-2xl border-2 p-3 text-center transition-all duration-200"
                                    :class="jenis === 'refund' ? 'border-amber-500 bg-amber-50 text-amber-700 shadow-sm' :
                                        'border-gray-100 bg-white text-gray-400 ' + (!bukaShiftMode ?
                                            'hover:border-amber-200 hover:bg-amber-50/50' : '')">
                                    <svg class="w-6 h-6 mx-auto mb-1"
                                        :class="jenis === 'refund' ? 'text-amber-500' : 'text-gray-300'"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z">
                                        </path>
                                    </svg>
                                    <span class="text-xs font-bold block">Refund</span>
                                </div>
                            </label>
                        </div>
                        @error('jenis')
                            <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 2. Input Nominal --}}
                    <div class="mb-6">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Nominal Uang
                            (Rp) <span class="text-red-500">*</span></label>
                        <div class="relative flex items-center">
                            <span class="absolute left-4 font-black text-gray-400 text-lg">Rp</span>
                            <input type="number" name="nominal" min="1" value="{{ old('nominal') }}"
                                required
                                class="w-full pl-12 pr-4 py-3 rounded-2xl border-gray-200 bg-gray-50 text-xl font-black text-gray-900 focus:bg-white focus:border-primary focus:ring focus:ring-primary/20 transition-all shadow-sm"
                                placeholder="0">
                        </div>
                        @error('nominal')
                            <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 3. Input Keterangan --}}
                    <div class="mb-8">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Keterangan /
                            Alasan <span class="text-red-500">*</span></label>
                        <textarea name="keterangan" rows="2" required x-model="keterangan" :readonly="bukaShiftMode"
                            :class="bukaShiftMode ? 'bg-gray-100 cursor-not-allowed text-gray-500 font-bold' :
                                'bg-gray-50 focus:bg-white text-gray-900'"
                            class="w-full rounded-2xl border-gray-200 focus:border-primary focus:ring focus:ring-primary/20 transition-all shadow-sm"
                            placeholder="Misal: Beli gas, Retur pesanan..."></textarea>
                        @error('keterangan')
                            <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                        class="w-full bg-primary hover:bg-primary-dark text-white font-bold py-3.5 px-4 rounded-2xl shadow-md shadow-primary/20 transition-all transform active:scale-95 flex justify-center items-center gap-2 text-base">
                        <span x-text="bukaShiftMode ? 'Buka Shift & Simpan Modal' : 'Simpan Pencatatan'"></span>
                    </button>
                </form>
            </div>

            {{-- BAGIAN KANAN: DAFTAR RIWAYAT --}}
            <div
                class="lg:col-span-7 bg-white rounded-3xl shadow-sm border border-gray-100 flex flex-col h-full max-h-[80vh]">
                <div
                    class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50 rounded-t-3xl">
                    <h3 class="font-black text-lg text-gray-800">Aktivitas Hari Ini</h3>
                    <span class="px-3 py-1 bg-white border border-gray-200 rounded-lg text-xs font-bold text-gray-500">
                        {{ now()->format('d M Y') }}
                    </span>
                </div>

                <div class="p-4 flex-1 overflow-y-auto custom-scrollbar">
                    @if ($kasHariIni->isEmpty())
                        <div class="h-full flex flex-col items-center justify-center text-center py-12">
                            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                            </div>
                            <h4 class="text-gray-800 font-bold mb-1">Shift Belum Dibuka</h4>
                            <p class="text-sm text-gray-400">Silakan setorkan Modal Awal untuk memulai aktivitas kas.
                            </p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach ($kasHariIni as $kas)
                                <div
                                    class="flex items-center p-4 bg-gray-50 hover:bg-gray-100/80 rounded-2xl transition-colors border border-gray-100/50 group">

                                    {{-- Ikon Bulat Sesuai Jenis --}}
                                    <div
                                        class="w-12 h-12 rounded-full flex items-center justify-center shrink-0 mr-4 shadow-sm
                                        {{ $kas->jenis == 'pemasukan' ? 'bg-emerald-100 text-emerald-600' : ($kas->jenis == 'refund' ? 'bg-amber-100 text-amber-600' : 'bg-red-100 text-red-600') }}">
                                        @if ($kas->jenis == 'pemasukan')
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                            </svg>
                                        @elseif($kas->jenis == 'refund')
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z">
                                                </path>
                                            </svg>
                                        @else
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                                            </svg>
                                        @endif
                                    </div>

                                    {{-- Info Keterangan & Waktu --}}
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-bold text-gray-800 truncate mb-0.5">
                                            {{ $kas->keterangan }}</p>
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="text-[10px] font-black uppercase tracking-wider 
                                                {{ $kas->jenis == 'pemasukan' ? 'text-emerald-500' : ($kas->jenis == 'refund' ? 'text-amber-500' : 'text-red-500') }}">
                                                {{ $kas->keterangan === 'Modal Awal Shift' ? 'Modal Awal' : $kas->jenis }}
                                            </span>
                                            <span class="text-gray-300">•</span>
                                            <span
                                                class="text-[10px] font-medium text-gray-500">{{ $kas->created_at->format('H:i') }}
                                                WIB</span>
                                        </div>
                                    </div>

                                    {{-- Nominal Uang --}}
                                    <div class="text-right pl-3">
                                        <p
                                            class="text-base font-black whitespace-nowrap {{ $kas->jenis == 'pemasukan' ? 'text-emerald-600' : 'text-gray-800' }}">
                                            {{ $kas->jenis == 'pemasukan' ? '+' : '-' }}Rp
                                            {{ number_format($kas->nominal, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #cbd5e1;
        }
    </style>
</x-app-layout>
