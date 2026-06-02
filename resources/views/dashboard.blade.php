<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ringkasan Penjualan') }}
        </h2>
    </x-slot>

    @php 
        $currentPeriod = request('period', 'daily'); 
    @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- TAB FILTER RENTANG WAKTU --}}
            <div class="flex flex-wrap items-center justify-between gap-4 bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex bg-gray-100 p-1 rounded-xl">
                    <a href="{{ route('dashboard', ['period' => 'daily']) }}" 
                       class="px-6 py-2 rounded-lg text-sm font-bold transition-all {{ $currentPeriod == 'daily' ? 'bg-white text-green-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                        Harian
                    </a>
                    <a href="{{ route('dashboard', ['period' => 'monthly']) }}" 
                       class="px-6 py-2 rounded-lg text-sm font-bold transition-all {{ $currentPeriod == 'monthly' ? 'bg-white text-green-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                        Bulanan
                    </a>
                    <a href="{{ route('dashboard', ['period' => 'yearly']) }}" 
                       class="px-6 py-2 rounded-lg text-sm font-bold transition-all {{ $currentPeriod == 'yearly' ? 'bg-white text-green-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                        Tahunan
                    </a>
                </div>

                <div class="text-sm text-gray-500 font-medium px-2">
                    Periode: <span class="text-gray-800 font-bold uppercase">
                        @if($currentPeriod == 'daily') 30 Hari Terakhir
                        @elseif($currentPeriod == 'monthly') 12 Bulan Terakhir
                        @else Data Tahunan
                        @endif
                    </span>
                </div>
            </div>

            {{-- KOTAK RINGKASAN ATAS --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                {{-- Penjualan Kotor --}}
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <p class="text-gray-500 text-sm font-medium">Penjualan Kotor</p>
                    <h3 class="text-2xl font-bold text-gray-800">
                        Rp {{ number_format($totalSemuaPenjualan, 0, ',', '.') }}
                    </h3>

                    <div class="mt-2">
                        <div class="flex items-center gap-1 {{ $nominalDiffPenjualan >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            <span class="text-xs font-bold">
                                {{ $nominalDiffPenjualan >= 0 ? '↑' : '↓' }} Rp {{ number_format(abs($nominalDiffPenjualan), 0, ',', '.') }}
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
                    <p class="text-gray-500 text-sm font-medium">Total Transaksi</p>
                    <h3 class="text-2xl font-bold text-gray-800">
                        {{ number_format($totalTransaksi, 0, ',', '.') }}
                    </h3>

                    <div class="mt-2">
                        <div class="flex items-center gap-1 {{ $nominalDiffTransaksi >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            <span class="text-xs font-bold">
                                {{ $nominalDiffTransaksi >= 0 ? '↑' : '↓' }} {{ number_format(abs($nominalDiffTransaksi), 0, ',', '.') }}
                            </span>
                            <span class="text-[10px] font-bold">
                                ({{ number_format($diffTransaksiPersen, 2) }}%)
                            </span>
                        </div>
                        <p class="text-[10px] text-gray-400">vs periode sebelumnya</p>
                    </div>
                </div>

                {{-- Rerata Penjualan --}}
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <p class="text-gray-500 text-sm font-medium">
                        Rerata {{ $currentPeriod == 'daily' ? 'Harian' : ($currentPeriod == 'monthly' ? 'Bulanan' : 'Tahunan') }}
                    </p>
                    <h3 class="text-2xl font-bold text-gray-800">
                        Rp {{ number_format($rataRataHarian, 0, ',', '.') }}
                    </h3>
                    <p class="text-[10px] text-gray-400 mt-2">Berdasarkan periode yang dipilih</p>
                </div>
            </div>

            {{-- AREA GRAFIK --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4 text-gray-700">Tren Penjualan</h3>

                    <div class="relative w-full" style="height: 400px;">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- AREA PRODUK TERJUAL --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <h3 class="text-lg font-semibold text-gray-700">Laporan Produk Terjual</h3>

                    <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2">
                        {{-- Tetap simpan parameter period saat ganti tanggal --}}
                        <input type="hidden" name="period" value="{{ $currentPeriod }}">
                        
                        <label for="tanggal" class="text-sm font-medium text-gray-600 text-nowrap">Pilih Tanggal:</label>
                        <input type="date" name="tanggal" id="tanggal" value="{{ $tanggalProduk }}"
                            onchange="this.form.submit()"
                            class="border-gray-300 rounded-xl shadow-sm focus:ring-green-500 focus:border-green-500 text-sm">

                        @if($tanggalProduk != \Carbon\Carbon::today()->format('Y-m-d'))
                            <a href="{{ route('dashboard', ['period' => $currentPeriod]) }}"
                                class="text-xs text-blue-600 hover:text-blue-800 underline">Reset</a>
                        @endif
                    </form>
                </div>

                <div class="p-0 sm:p-6">
                    @if($produkTerjual->isEmpty())
                        <div class="text-center py-12 text-gray-500">
                            <svg class="w-16 h-16 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            <p>Tidak ada data penjualan untuk tanggal 
                                <strong>{{ \Carbon\Carbon::parse($tanggalProduk)->translatedFormat('d F Y') }}</strong>
                            </p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="bg-gray-50 border-y border-gray-100 text-xs uppercase text-gray-500 font-bold">
                                        <th class="px-6 py-4 text-center w-20">Rank</th>
                                        <th class="px-6 py-4">Menu</th>
                                        <th class="px-6 py-4 text-center">Terjual</th>
                                        <th class="px-6 py-4 text-right">Pendapatan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($produkTerjual as $index => $item)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4 text-center">
                                                @if($index == 0) <span class="text-xl">🥇</span>
                                                @elseif($index == 1) <span class="text-xl">🥈</span>
                                                @elseif($index == 2) <span class="text-xl">🥉</span>
                                                @else <span class="text-gray-400 font-medium">{{ $index + 1 }}</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 font-bold text-gray-800">
                                                {{ $item->menu->nama ?? 'Menu Terhapus' }}
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <span class="bg-gray-100 px-3 py-1 rounded-full text-sm font-semibold text-gray-700">
                                                    {{ $item->total_qty }} porsi
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-right font-bold text-green-600">
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

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('salesChart').getContext('2d');
            const labels = @json($labels);
            const dataPenjualan = @json($dataPenjualan);
            const period = "{{ $currentPeriod }}";

            new Chart(ctx, {
                // Gunakan tipe bar untuk tahunan agar lebih jelas, line untuk harian/bulanan
                type: period === 'yearly' ? 'bar' : 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Penjualan (Rp)',
                        data: dataPenjualan,
                        borderColor: '#16a34a',
                        backgroundColor: period === 'yearly' ? '#16a34a' : 'rgba(22, 163, 74, 0.1)',
                        borderWidth: 3,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#16a34a',
                        pointRadius: period === 'daily' ? 4 : 6,
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function (value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            padding: 12,
                            callbacks: {
                                label: function (context) {
                                    return 'Total: Rp ' + context.parsed.y.toLocaleString('id-ID');
                                }
                            }
                        },
                        legend: { display: false }
                    }
                }
            });
        });
    </script>
</x-app-layout>