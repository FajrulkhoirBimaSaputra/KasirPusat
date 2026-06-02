<x-app-layout>
    {{-- Inisialisasi Alpine.js dengan bulan pertama sebagai default --}}
    <div x-data="{ 
            activeMonth: '{{ $laporanBulanan[0]['bulan'] }}', 
            allProducts: {{ json_encode($produkPerBulan) }} 
         }" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        <!-- HEADER & FILTER TAHUN (Sama seperti sebelumnya) -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Laporan Bulanan</h1>
                <p class="text-gray-500 mt-1">Klik pada baris bulan untuk melihat detail produk terjual.</p>
            </div>
            <form action="{{ route('laporan.bulanan') }}" method="GET"
                class="bg-white p-2 rounded-2xl shadow-sm border border-gray-100">
                <select name="tahun" onchange="this.form.submit()"
                    class="border-none focus:ring-0 text-sm font-bold text-gray-700 rounded-xl">
                    @foreach($daftarTahun as $t)
                        <option value="{{ $t }}" {{ $tahunTerpilih == $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- TABEL PENDAPATAN (2 Kolom) --}}
            <div class="lg:col-span-2 bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-xs font-bold text-gray-500 uppercase">
                        <tr>
                            <th class="px-6 py-4">Bulan</th>
                            <th class="px-6 py-4 text-center">Transaksi</th>
                            <th class="px-6 py-4 text-right">Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($laporanBulanan as $data)
                            <tr @click="activeMonth = '{{ $data['bulan'] }}'" class="cursor-pointer transition-all"
                                :class="activeMonth === '{{ $data['bulan'] }}' ? 'bg-green-50' : 'hover:bg-gray-50'">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div x-show="activeMonth === '{{ $data['bulan'] }}'"
                                            class="w-2 h-2 bg-green-500 rounded-full"></div>
                                        <span class="font-bold"
                                            :class="activeMonth === '{{ $data['bulan'] }}' ? 'text-green-700' : 'text-gray-700'">{{ $data['bulan'] }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center text-gray-600">{{ $data['transaksi'] }}</td>
                                <td class="px-6 py-4 text-right font-bold text-gray-900">Rp
                                    {{ number_format($data['pendapatan'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- DETAIL PRODUK TERJUAL (1 Kolom - Dinamis) --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 flex flex-col">
                <div class="p-6 border-b border-gray-50 bg-gray-50/50">
                    <h3 class="font-bold text-gray-800 italic">Detail Produk Terjual</h3>
                    <p class="text-sm text-green-600 font-semibold" x-text="'Bulan ' + activeMonth"></p>
                </div>

                <div class="p-6 flex-grow">
                    {{-- Loop Produk Menggunakan Alpine x-for --}}
                    <template x-for="(item, index) in allProducts[activeMonth]" :key="index">
                        <div class="flex items-center justify-between mb-6 last:mb-0 animate-fadeIn">
                            <div class="flex items-center gap-4">
                                <div class="w-8 h-8 bg-gray-100 text-gray-600 rounded-lg flex items-center justify-center text-xs font-bold"
                                    x-text="index + 1"></div>
                                <div>
                                    <h4 class="font-bold text-gray-800 text-sm" x-text="item.nama"></h4>
                                    <p class="text-[10px] text-gray-500" x-text="item.qty + ' terjual'"></p>
                                </div>
                            </div>
                            <div class="text-right text-sm font-bold text-gray-900">
                                Rp <span x-text="item.total"></span>
                            </div>
                        </div>
                    </template>

                    {{-- Tampilan Jika Kosong --}}
                    <div x-show="allProducts[activeMonth].length === 0" class="text-center py-10">
                        <p class="text-gray-400 text-sm">Tidak ada penjualan di bulan ini.</p>
                    </div>
                </div>

                <div class="p-6 bg-gray-50 border-t border-gray-100 rounded-b-3xl">
                    <p class="text-[10px] text-gray-400 leading-tight">Data di atas adalah rincian 5 produk paling laris
                        pada bulan yang dipilih.</p>
                </div>
            </div>
        </div>
    </div>

    <style>
        .animate-fadeIn {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</x-app-layout>