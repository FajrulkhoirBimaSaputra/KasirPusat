<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <h2 class="font-bold text-xl text-gray-800 leading-tight">
                Laporan Keuangan Bulanan
            </h2>
        </div>
    </x-slot>

    {{-- Inisialisasi Alpine.js --}}
    <div x-data="{
        activeMonth: '{{ count($laporanBulanan) > 0 ? $laporanBulanan[0]['bulan'] : '' }}',
        allProducts: {{ json_encode($produkPerBulan) }},
        formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 0 }).format(number);
        }
    }">

        <div class="max-w-7xl mx-auto py-8 sm:px-6 lg:px-8">

            {{-- HEADER FILTER & INFO TAHUNAN --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 mb-1">Rekapitulasi Kinerja Bisnis</h3>
                    <p class="text-sm text-gray-500">Total pendapatan di tahun <b>{{ $tahunTerpilih }}</b> mencapai
                        <span class="text-primary font-bold text-base">Rp
                            {{ number_format($totalTahunan, 0, ',', '.') }}</span></p>
                </div>

                <form action="{{ route('laporan.bulanan') }}" method="GET"
                    class="flex items-center gap-3 bg-white border border-gray-200 rounded-xl px-5 py-3 shadow-sm w-full md:w-auto">
                    <span class="text-sm font-bold text-gray-400 uppercase tracking-wider">Tahun Laporan</span>
                    <div class="relative w-full md:w-32 border-l border-gray-200 pl-3">
                        <select name="tahun" onchange="this.form.submit()"
                            class="block w-full py-1 text-base font-black text-gray-900 bg-transparent border-none focus:ring-0 appearance-none cursor-pointer p-0">
                            @foreach ($daftarTahun as $t)
                                <option value="{{ $t }}" {{ $tahunTerpilih == $t ? 'selected' : '' }}>
                                    {{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                {{-- KOLOM KIRI: DAFTAR BULAN (Detail Metrik) --}}
                <div class="lg:col-span-7 space-y-4">

                    @forelse($laporanBulanan as $data)
                        <div class="bg-white rounded-2xl shadow-sm border overflow-hidden transition-all duration-300"
                            :class="activeMonth === '{{ $data['bulan'] }}' ?
                                'border-primary ring-2 ring-primary/20 scale-[1.01]' :
                                'border-gray-100 hover:border-gray-300'">

                            <div @click="activeMonth = activeMonth === '{{ $data['bulan'] }}' ? '' : '{{ $data['bulan'] }}'"
                                class="p-5 sm:p-6 cursor-pointer bg-white relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">

                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <div class="w-3 h-3 rounded-full transition-colors"
                                            :class="activeMonth === '{{ $data['bulan'] }}' ? 'bg-primary' : 'bg-gray-200'">
                                        </div>
                                        <h4 class="font-black text-gray-900 text-xl">{{ $data['bulan'] }}</h4>
                                        <span
                                            class="text-xs font-bold text-gray-600 bg-gray-100 px-3 py-1 rounded-full border border-gray-200">
                                            {{ $data['transaksi'] }} Transaksi
                                        </span>
                                    </div>
                                    <p
                                        class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-0.5 ml-6">
                                        Total Pendapatan</p>
                                    <p class="font-black text-primary text-2xl ml-6">Rp
                                        {{ number_format($data['pendapatan'], 0, ',', '.') }}</p>
                                </div>

                                <div
                                    class="flex-1 sm:text-right ml-6 sm:ml-0 border-t sm:border-t-0 sm:border-l border-gray-100 pt-3 sm:pt-0 sm:pl-6">
                                    <div class="mb-3">
                                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">
                                            Metode Pembayaran</p>
                                        <div class="w-full bg-gray-100 rounded-full h-2.5 mb-1.5 flex overflow-hidden">
                                            @php
                                                $pctCash =
                                                    $data['pendapatan'] > 0
                                                        ? ($data['total_cash'] / $data['pendapatan']) * 100
                                                        : 0;
                                                $pctQris =
                                                    $data['pendapatan'] > 0
                                                        ? ($data['total_qris'] / $data['pendapatan']) * 100
                                                        : 0;
                                            @endphp
                                            <div class="bg-emerald-500 h-2.5" style="width: {{ $pctCash }}%">
                                            </div>
                                            <div class="bg-blue-500 h-2.5" style="width: {{ $pctQris }}%"></div>
                                        </div>
                                        <div class="flex justify-between sm:justify-end gap-3 text-[10px] font-bold">
                                            <span class="text-emerald-600 flex items-center gap-1">
                                                <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div> Tunai
                                                ({{ round($pctCash) }}%)
                                            </span>
                                            <span class="text-blue-600 flex items-center gap-1">
                                                <div class="w-1.5 h-1.5 rounded-full bg-blue-500"></div> QRIS
                                                ({{ round($pctQris) }}%)
                                            </span>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">
                                            Rata-rata Per Transaksi</p>
                                        <p class="font-bold text-gray-700 text-sm">Rp
                                            {{ number_format($data['rata_rata'], 0, ',', '.') }}</p>
                                    </div>
                                </div>

                            </div>

                            {{-- Accordion Content Khusus Mobile (Detail Produk) --}}
                            <div x-show="activeMonth === '{{ $data['bulan'] }}'" x-collapse
                                class="lg:hidden border-t border-gray-100 bg-gray-50 p-5">
                                <h5
                                    class="text-xs font-bold text-gray-700 mb-4 flex items-center gap-1.5 uppercase tracking-wider">
                                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                    Detail Produk Terjual
                                </h5>

                                <template
                                    x-if="allProducts['{{ $data['bulan'] }}'] && allProducts['{{ $data['bulan'] }}'].length > 0">
                                    <div class="space-y-3">
                                        <template x-for="(item, index) in allProducts['{{ $data['bulan'] }}']"
                                            :key="index">
                                            <div
                                                class="flex items-center justify-between border-b border-gray-200/50 pb-2 last:border-0 last:pb-0">
                                                <div>
                                                    <p class="font-bold text-gray-800 text-sm leading-tight"
                                                        x-text="item.nama"></p>
                                                    <p class="text-[10px] text-gray-500 font-medium mt-0.5"
                                                        x-text="item.qty + ' porsi terjual'"></p>
                                                </div>
                                                <div class="text-xs font-bold text-primary">Rp <span
                                                        x-text="formatRupiah(item.total)"></span></div>
                                            </div>
                                        </template>
                                    </div>
                                </template>

                                <template
                                    x-if="!allProducts['{{ $data['bulan'] }}'] || allProducts['{{ $data['bulan'] }}'].length === 0">
                                    <p class="text-xs text-gray-500 text-center py-2">Belum ada data penjualan.</p>
                                </template>
                            </div>

                        </div>
                    @empty
                        <div
                            class="bg-white p-12 rounded-2xl shadow-sm border border-gray-100 text-center text-gray-500">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                            <p class="font-medium text-lg">Tidak ada data di tahun ini</p>
                        </div>
                    @endforelse
                </div>

                {{-- KOLOM KANAN: DETAIL PRODUK (DESKTOP SAJA - STICKY) --}}
                <div
                    class="hidden lg:flex lg:col-span-5 sticky top-6 bg-white rounded-3xl shadow-sm border border-gray-100 flex-col overflow-hidden max-h-[85vh]">
                    <div class="p-6 border-b border-gray-100 bg-gray-50/80 flex items-start justify-between shrink-0">
                        <div>
                            <h3 class="font-black text-gray-900 text-lg">Rincian Penjualan Produk</h3>
                            <p class="text-sm font-bold text-primary mt-1"
                                x-text="activeMonth ? 'Periode Bulan ' + activeMonth : 'Silakan Pilih Bulan di Samping'">
                            </p>
                        </div>
                        <div
                            class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                    </div>

                    <div class="p-6 flex-grow overflow-y-auto custom-scrollbar">
                        <template x-if="activeMonth && allProducts[activeMonth]">
                            <div>
                                <template x-for="(item, index) in allProducts[activeMonth]" :key="index">
                                    <div class="flex items-center justify-between mb-5 last:mb-0 group">
                                        <div class="flex items-center gap-4">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-gray-500 bg-gray-100 group-hover:bg-primary group-hover:text-white transition-colors"
                                                x-text="index + 1"></div>
                                            <div>
                                                <h4 class="font-bold text-gray-800 text-sm group-hover:text-primary transition-colors"
                                                    x-text="item.nama"></h4>
                                                <p
                                                    class="text-[11px] text-gray-500 font-medium uppercase tracking-wider mt-0.5">
                                                    <span class="font-bold text-gray-700" x-text="item.qty"></span>
                                                    porsi</p>
                                            </div>
                                        </div>
                                        <div
                                            class="text-right font-black text-gray-900 group-hover:text-primary transition-colors">
                                            Rp <span x-text="formatRupiah(item.total)"></span>
                                        </div>
                                    </div>
                                </template>

                                <div x-show="allProducts[activeMonth].length === 0"
                                    class="text-center py-16 flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-200 mb-4" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                    <p class="text-gray-400 font-bold">Belum ada transaksi di bulan ini.</p>
                                </div>
                            </div>
                        </template>

                        <template x-if="!activeMonth">
                            <div class="text-center py-20 flex flex-col items-center justify-center h-full">
                                <svg class="w-16 h-16 text-gray-200 mb-4 animate-bounce" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
                                </svg>
                                <p class="text-gray-400 font-bold">Klik salah satu bulan di samping<br>untuk melihat
                                    rincian produk.</p>
                            </div>
                        </template>
                    </div>
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
            background: #cbd5e1;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</x-app-layout>
