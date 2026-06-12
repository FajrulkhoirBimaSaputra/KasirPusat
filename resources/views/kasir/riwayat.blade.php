<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h2 class="font-bold text-xl text-gray-800 leading-tight">
                Riwayat Transaksi
            </h2>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">

        @php
            // Menghitung total pendapatan untuk tanggal yang difilter
            $totalPendapatan = $orders->where('payment_status', 'paid')->sum('total');
        @endphp

        <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4 items-stretch">

            {{-- 1. Card Filter Tanggal --}}
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-center">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Filter Tanggal</h3>
                <form method="GET" action="{{ url()->current() }}" class="flex items-center gap-2">
                    <input type="date" name="tanggal" id="tanggal"
                        value="{{ request('tanggal', \Carbon\Carbon::today()->format('Y-m-d')) }}"
                        onchange="this.form.submit()"
                        class="w-full py-2 px-3 border-gray-200 rounded-xl shadow-sm focus:ring-primary focus:border-primary text-sm font-bold text-gray-700 cursor-pointer transition-colors">

                    @if (request()->has('tanggal') && request('tanggal') != \Carbon\Carbon::today()->format('Y-m-d'))
                        <a href="{{ url()->current() }}"
                            class="p-2 text-gray-400 hover:text-red-500 bg-gray-50 hover:bg-red-50 rounded-xl transition-colors"
                            title="Reset ke Hari Ini">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </a>
                    @endif
                </form>
            </div>

            {{-- 2. Card Total Pendapatan --}}
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
                <div
                    class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-0.5">Total Pendapatan</p>
                    <p class="text-xl md:text-2xl font-black text-gray-900 leading-none">Rp
                        {{ number_format($totalPendapatan, 0, ',', '.') }}</p>
                </div>
            </div>

            {{-- 3. Tombol Laporan Akhir Shift --}}
            <div class="flex items-center">
                <a href="{{ route('kasir.ringkasan') }}"
                    class="w-full h-full min-h-[72px] flex items-center justify-center gap-2 bg-primary hover:bg-primary-dark text-white text-sm font-bold p-4 rounded-2xl shadow-sm shadow-primary/20 transition-all group">
                    <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Laporan Akhir Shift
                </a>
            </div>

        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            @if ($orders->isEmpty())
                <div class="text-center py-16 px-4">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4">
                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <p class="text-gray-500 font-medium">Belum ada transaksi pada tanggal yang dipilih.</p>
                </div>
            @else
                {{-- TAMPILAN MOBILE (Card List) --}}
                <div class="block md:hidden divide-y divide-gray-50">
                    @foreach ($orders as $order)
                        <div class="p-4" x-data="{ openModal: false }">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <p class="text-[10px] font-bold text-gray-400 mb-0.5">
                                        {{ $order->created_at->format('d M Y • H:i') }} WIB</p>
                                    <p class="font-bold text-gray-900">Order #{{ $order->id }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-black text-primary">Rp
                                        {{ number_format($order->total, 0, ',', '.') }}</p>
                                    <span
                                        class="inline-block mt-1 px-2 py-0.5 text-[9px] font-bold uppercase rounded {{ $order->payment_method === 'cash' ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700' }}">
                                        {{ $order->payment_method }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-50">
                                <div>
                                    @if ($order->payment_status === 'paid')
                                        <span
                                            class="flex items-center text-[10px] font-bold text-emerald-600 gap-1"><span
                                                class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> LUNAS</span>
                                    @else
                                        <span class="flex items-center text-[10px] font-bold text-amber-500 gap-1"><span
                                                class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                            PENDING</span>
                                    @endif
                                </div>
                                <button @click="openModal = true"
                                    class="text-xs font-bold text-primary hover:text-primary-dark underline underline-offset-2">
                                    Lihat Detail
                                </button>
                            </div>

                            {{-- Panggil Template Modal --}}
                            @include('kasir.partials._modal-detail-transaksi', ['order' => $order])
                        </div>
                    @endforeach
                </div>

                {{-- TAMPILAN DESKTOP (Tabel) --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr
                                class="bg-gray-50/80 border-b border-gray-100 text-xs font-bold text-gray-500 uppercase tracking-wider">
                                <th class="px-6 py-4">Waktu</th>
                                <th class="px-6 py-4">ID Order</th>
                                <th class="px-6 py-4">Total Belanja</th>
                                <th class="px-6 py-4 text-center">Metode</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach ($orders as $order)
                                <tr class="hover:bg-gray-50/50 transition-colors" x-data="{ openModal: false }">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-800">
                                            {{ $order->created_at->format('H:i') }} WIB</div>
                                        <div class="text-[10px] font-medium text-gray-400">
                                            {{ $order->created_at->format('d M Y') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-bold text-gray-600">#{{ $order->id }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-black text-gray-900">Rp
                                            {{ number_format($order->total, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span
                                            class="px-2.5 py-1 text-[10px] font-bold uppercase rounded-md shadow-sm border {{ $order->payment_method === 'cash' ? 'bg-emerald-50 border-emerald-100 text-emerald-700' : 'bg-blue-50 border-blue-100 text-blue-700' }}">
                                            {{ $order->payment_method }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if ($order->payment_status === 'paid')
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2 py-1 rounded-full text-[10px] font-bold bg-gray-100 text-emerald-600">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Lunas
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-600 border border-amber-200">
                                                <span
                                                    class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <button @click="openModal = true"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-gray-100 hover:bg-primary hover:text-white text-gray-600 text-xs font-bold rounded-lg transition-colors">
                                            Detail
                                        </button>
                                        {{-- Panggil Template Modal --}}
                                        @include('kasir.partials._modal-detail-transaksi', [
                                            'order' => $order,
                                        ])
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</x-app-layout>
