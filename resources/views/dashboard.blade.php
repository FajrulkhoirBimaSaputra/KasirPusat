<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $isAdmin ? __('Dashboard Admin Utama') : __('Dashboard Kinerja Kasir') }}
        </h2>
    </x-slot>

    @php
        $currentPeriod = request('period', 'daily');
    @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- AREA KHUSUS KASIR (STATUS & JADWAL SHIFT) --}}
            @if (!$isAdmin)
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    {{-- KIRI: Panel Status Shift (Lebar 2 Kolom) --}}
                    <div
                        class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-center">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-2">Status Shift
                                    Anda Hari Ini</h3>
                                <div class="flex items-center gap-3">
                                    @if (!$shiftStatus || $shiftStatus->keterangan === 'Tutup Shift')
                                        <span
                                            class="flex items-center gap-1.5 text-sm font-bold text-red-600 bg-red-50 px-3 py-1 rounded-full border border-red-100">
                                            <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span> Shift
                                            Ditutup
                                        </span>
                                        <p class="text-sm text-gray-500 font-medium">Ke Manajemen Kas untuk Buka Shift.
                                        </p>
                                    @else
                                        <span
                                            class="flex items-center gap-1.5 text-sm font-bold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-100">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Shift Aktif
                                        </span>
                                        <p class="text-sm text-gray-500 font-medium">Dibuka:
                                            {{ $shiftStatus->created_at->format('H:i') }} WIB</p>
                                    @endif
                                </div>
                            </div>

                            @if (!$shiftStatus || $shiftStatus->keterangan === 'Tutup Shift')
                                <a href="{{ route('kasir.manajemen-kas') }}"
                                    class="w-full sm:w-auto text-center px-5 py-2.5 bg-primary hover:bg-primary-dark text-white text-sm font-bold rounded-xl shadow-sm transition-colors whitespace-nowrap">
                                    Buka Shift Sekarang
                                </a>
                            @else
                                <a href="{{ route('kasir.index') }}"
                                    class="w-full sm:w-auto text-center px-5 py-2.5 bg-primary hover:bg-primary-dark text-white text-sm font-bold rounded-xl shadow-sm transition-colors whitespace-nowrap">
                                    Menuju Mesin POS
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- KANAN: Panel Jadwal Shift Mendatang (Lebar 1 Kolom) --}}
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                        <div class="flex items-center gap-2 mb-3">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider">Jadwal Shift Anda</h3>
                        </div>

                        <div class="space-y-2 max-h-[120px] overflow-y-auto custom-scrollbar pr-2">
                            @if ($jadwalShifts->isEmpty())
                                <p class="text-xs text-gray-400 font-medium text-center py-4">Belum ada jadwal shift
                                    mendatang dari Admin.</p>
                            @else
                                @foreach ($jadwalShifts as $shift)
                                    @php
                                        $isToday = \Carbon\Carbon::parse($shift->tanggal)->isToday();
                                    @endphp
                                    <div
                                        class="flex justify-between items-center p-2.5 rounded-xl border {{ $isToday ? 'bg-primary/5 border-primary/20' : 'bg-gray-50 border-gray-100' }}">
                                        <span
                                            class="text-xs font-bold {{ $isToday ? 'text-primary' : 'text-gray-700' }}">
                                            {{ $isToday ? 'Hari Ini' : \Carbon\Carbon::parse($shift->tanggal)->translatedFormat('l, d M Y') }}
                                        </span>
                                        @if ($isToday)
                                            <span
                                                class="text-[9px] font-black uppercase text-primary tracking-wider animate-pulse flex items-center gap-1">
                                                <span class="w-1.5 h-1.5 bg-primary rounded-full"></span> Now
                                            </span>
                                        @else
                                            <span
                                                class="text-[9px] font-black uppercase text-gray-400 tracking-wider">Terjadwal</span>
                                        @endif
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                </div>
            @endif

            {{-- TAB FILTER RENTANG WAKTU --}}
            <div
                class="flex flex-wrap items-center justify-between gap-4 bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex bg-gray-100 p-1 rounded-xl">
                    <a href="{{ route('dashboard', ['period' => 'daily']) }}"
                        class="px-6 py-2 rounded-lg text-sm font-bold transition-all {{ $currentPeriod == 'daily' ? 'bg-white text-primary shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                        Harian
                    </a>
                    <a href="{{ route('dashboard', ['period' => 'monthly']) }}"
                        class="px-6 py-2 rounded-lg text-sm font-bold transition-all {{ $currentPeriod == 'monthly' ? 'bg-white text-primary shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                        Bulanan
                    </a>
                    <a href="{{ route('dashboard', ['period' => 'yearly']) }}"
                        class="px-6 py-2 rounded-lg text-sm font-bold transition-all {{ $currentPeriod == 'yearly' ? 'bg-white text-primary shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                        Tahunan
                    </a>
                </div>

                <div class="text-sm text-gray-500 font-medium px-2">
                    Periode: <span class="text-gray-800 font-bold uppercase">
                        @if ($currentPeriod == 'daily')
                            30 Hari Terakhir
                        @elseif($currentPeriod == 'monthly')
                            12 Bulan Terakhir
                        @else
                            5 Tahun Terakhir
                        @endif
                    </span>
                </div>
            </div>

            {{-- KOTAK RINGKASAN ATAS --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                {{-- Penjualan Kotor --}}
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <p class="text-gray-500 text-sm font-medium">Total Pendapatan
                        {{ $isAdmin ? 'Keseluruhan' : 'Anda' }}</p>
                    <h3 class="text-2xl font-bold text-gray-800">
                        Rp {{ number_format($totalSemuaPenjualan, 0, ',', '.') }}
                    </h3>

                    <div class="mt-2">
                        <div
                            class="flex items-center gap-1 {{ $nominalDiffPenjualan >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                            <span class="text-xs font-bold">
                                {{ $nominalDiffPenjualan >= 0 ? '↑' : '↓' }} Rp
                                {{ number_format(abs($nominalDiffPenjualan), 0, ',', '.') }}
                            </span>
                            <span class="text-[10px] font-bold">
                                ({{ number_format($diffPenjualanPersen, 2) }}%)
                            </span>
                        </div>
                        <p class="text-[10px] text-gray-400">vs periode sebelumnya</p>
                    </div>
                </div>

                {{-- Total Transaksi --}}
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <p class="text-gray-500 text-sm font-medium">Total Struk / Transaksi</p>
                    <h3 class="text-2xl font-bold text-gray-800">
                        {{ number_format($totalTransaksi, 0, ',', '.') }} <span
                            class="text-sm text-gray-400">struk</span>
                    </h3>

                    <div class="mt-2">
                        <div
                            class="flex items-center gap-1 {{ $nominalDiffTransaksi >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                            <span class="text-xs font-bold">
                                {{ $nominalDiffTransaksi >= 0 ? '↑' : '↓' }}
                                {{ number_format(abs($nominalDiffTransaksi), 0, ',', '.') }}
                            </span>
                            <span class="text-[10px] font-bold">
                                ({{ number_format($diffTransaksiPersen, 2) }}%)
                            </span>
                        </div>
                        <p class="text-[10px] text-gray-400">vs periode sebelumnya</p>
                    </div>
                </div>

                {{-- Kotak ke-3 Disesuaikan Role --}}
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    @if ($isAdmin)
                        <p class="text-gray-500 text-sm font-medium">Rerata Pendapatan
                            {{ $currentPeriod == 'daily' ? 'Harian' : ($currentPeriod == 'monthly' ? 'Bulanan' : 'Tahunan') }}
                        </p>
                        <h3 class="text-2xl font-bold text-gray-800">
                            Rp {{ number_format($rataRataHarian, 0, ',', '.') }}
                        </h3>
                        <p class="text-[10px] text-gray-400 mt-2">Kinerja stabil berdasarkan periode dipilih</p>
                    @else
                        <p class="text-gray-500 text-sm font-medium">Rerata Nilai Per Struk (Basket Size)</p>
                        <h3 class="text-2xl font-bold text-primary">
                            Rp
                            {{ $totalTransaksi > 0 ? number_format($totalSemuaPenjualan / $totalTransaksi, 0, ',', '.') : 0 }}
                        </h3>
                        <p class="text-[10px] text-gray-400 mt-2">Rata-rata uang yang dihabiskan tiap pelanggan Anda</p>
                    @endif
                </div>
            </div>

            {{-- AREA GRAFIK (SEMUA DIUBAH JADI BAR CHART) --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4 text-gray-700">Tren Penjualan
                        {{ $isAdmin ? 'Toko' : 'Kasir' }}</h3>

                    <div class="relative w-full" style="height: 400px;">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- AREA PRODUK TERJUAL --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-xl sm:rounded-2xl border border-gray-100">

                {{-- HEADER & FILTER SECTION --}}
                <div
                    class="p-4 sm:p-6 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                    <h3 class="text-base sm:text-lg font-bold text-gray-700">Laporan Produk Terjual</h3>

                    <form method="GET" action="{{ route('dashboard') }}"
                        class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                        <input type="hidden" name="period" value="{{ $currentPeriod }}">

                        <label for="tanggal" class="text-xs sm:text-sm font-bold text-gray-500 whitespace-nowrap">
                            Pilih Tanggal:
                        </label>

                        <div class="flex items-center gap-2 flex-1 sm:flex-none">
                            <input type="date" name="tanggal" id="tanggal" value="{{ $tanggalProduk }}"
                                onchange="this.form.submit()"
                                class="w-full sm:w-auto border-gray-200 rounded-lg shadow-sm focus:ring-primary focus:border-primary text-xs sm:text-sm px-3 py-1.5 sm:px-3 sm:py-2">

                            @if ($tanggalProduk != \Carbon\Carbon::today()->format('Y-m-d'))
                                <a href="{{ route('dashboard', ['period' => $currentPeriod]) }}"
                                    class="text-[10px] sm:text-xs bg-red-50 text-red-600 border border-red-100 hover:bg-red-100 px-2 py-1.5 sm:px-3 sm:py-2 rounded-lg font-bold transition-colors whitespace-nowrap">
                                    Reset
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                {{-- KONTEN TABEL --}}
                <div class="p-0 sm:p-6">
                    @if ($produkTerjual->isEmpty())
                        <div class="text-center py-10 sm:py-12 text-gray-500">
                            <svg class="w-12 h-12 sm:w-16 sm:h-16 mx-auto mb-2 sm:mb-3 text-gray-300" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            <p class="text-xs sm:text-base px-4">
                                Tidak ada data penjualan untuk tanggal<br class="block sm:hidden">
                                <strong
                                    class="text-gray-700">{{ \Carbon\Carbon::parse($tanggalProduk)->translatedFormat('d F Y') }}</strong>
                            </p>
                        </div>
                    @else
                        <div class="overflow-x-auto custom-scrollbar">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr
                                        class="bg-gray-50 border-y border-gray-100 text-[10px] sm:text-xs uppercase text-gray-500 font-bold tracking-wider">
                                        <th class="px-2 py-2.5 sm:px-6 sm:py-4 text-center w-10 sm:w-20">Rank</th>
                                        <th class="px-2 py-2.5 sm:px-6 sm:py-4 whitespace-nowrap">Menu</th>
                                        <th class="px-2 py-2.5 sm:px-6 sm:py-4 text-center">Terjual</th>
                                        <th class="px-3 py-2.5 sm:px-6 sm:py-4 text-right">Pendapatan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach ($produkTerjual as $index => $item)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-2 py-3 sm:px-6 sm:py-4 text-center">
                                                @if ($index == 0)
                                                    <span class="text-base sm:text-xl">🥇</span>
                                                @elseif($index == 1)
                                                    <span class="text-base sm:text-xl">🥈</span>
                                                @elseif($index == 2)
                                                    <span class="text-base sm:text-xl">🥉</span>
                                                @else
                                                    <span
                                                        class="text-gray-400 font-black text-xs sm:text-base">{{ $index + 1 }}</span>
                                                @endif
                                            </td>
                                            <td
                                                class="px-2 py-3 sm:px-6 sm:py-4 font-bold text-gray-800 text-[11px] sm:text-sm">
                                                {{ $item->menu->nama ?? 'Menu Terhapus' }}
                                            </td>
                                            <td class="px-2 py-3 sm:px-6 sm:py-4 text-center">
                                                {{-- Mengubah gaya badge porsi agar lebih manis --}}
                                                <span
                                                    class="bg-blue-50 border border-blue-100 px-2 py-1 sm:px-3 sm:py-1 rounded-md sm:rounded-full text-[10px] sm:text-sm font-bold text-blue-700 whitespace-nowrap shadow-sm">
                                                    {{ $item->total_qty }} porsi
                                                </span>
                                            </td>
                                            <td
                                                class="px-3 py-3 sm:px-6 sm:py-4 text-right font-black text-primary text-[11px] sm:text-base whitespace-nowrap">
                                                Rp {{ number_format($item->total_pendapatan, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
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

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('salesChart').getContext('2d');
            const labels = @json($labels);
            const dataPenjualan = @json($dataPenjualan);

            // Chart sekarang diatur menjadi BAR secara permanen
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Pendapatan (Rp)',
                        data: dataPenjualan,
                        backgroundColor: '#16a34a', // Warna primary hijau
                        borderRadius: 6, // Ujung batang melengkung
                        borderSkipped: false,
                        barPercentage: 0.6,
                        hoverBackgroundColor: '#15803d' // Hijau lebih gelap saat hover
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)',
                                drawBorder: false,
                            },
                            ticks: {
                                callback: function(value) {
                                    // Ringkas angka besar (misal 1.000.000 jadi 1 Jt)
                                    if (value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(1) +
                                        ' Jt';
                                    if (value >= 1000) return 'Rp ' + (value / 1000).toFixed(1) + ' Rb';
                                    return 'Rp ' + value;
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            backgroundColor: 'rgba(17, 24, 39, 0.9)',
                            padding: 12,
                            titleFont: {
                                size: 13
                            },
                            bodyFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            callbacks: {
                                label: function(context) {
                                    return ' Rp ' + context.parsed.y.toLocaleString('id-ID');
                                }
                            }
                        },
                        legend: {
                            display: false
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>
