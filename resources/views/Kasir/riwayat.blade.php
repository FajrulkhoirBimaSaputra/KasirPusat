<x-app-layout>
    <div class="p-4">
        {{-- Header & Tombol --}}
        <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-4 gap-4">
            <h1 class="text-2xl font-bold">Riwayat Transaksi</h1>
            
            <a href="{{ route('kasir.ringkasan') }}" 
               class="bg-primary hover:bg-primary-dark text-white font-medium py-2 px-4 rounded-lg shadow transition text-center">
                <svg class="w-5 h-5 inline-block mr-1 -mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Ringkasan Penjualan Hari Ini
            </a>
        </div>

        {{-- Form Filter Tanggal --}}
        <div class="bg-white rounded-lg shadow p-4 mb-4">
            <form method="GET" action="{{ url()->current() }}" class="flex flex-wrap items-end gap-3">
                <div>
                    <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-1">Pilih Tanggal</label>
                    {{-- Atribut onchange ditambahkan agar otomatis submit form saat tanggal dipilih --}}
                    <input type="date" name="tanggal" id="tanggal" 
                           value="{{ request('tanggal', \Carbon\Carbon::today()->format('Y-m-d')) }}" 
                           onchange="this.form.submit()"
                           class="border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary">
                </div>
                
                {{-- Tombol Reset akan muncul jika sedang melihat data selain hari ini --}}
                @if(request()->has('tanggal') && request('tanggal') != \Carbon\Carbon::today()->format('Y-m-d'))
                    <a href="{{ url()->current() }}" class="text-gray-500 hover:text-gray-700 underline text-sm py-2">
                        Reset ke Hari Ini
                    </a>
                @endif
            </form>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            @if($orders->isEmpty())
                <p class="text-gray-500 text-center py-4">Belum ada transaksi pada tanggal ini.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-max">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="border-b p-2 text-gray-600 font-semibold">Waktu</th>
                                <th class="border-b p-2 text-gray-600 font-semibold">ID Order</th>
                                <th class="border-b p-2 text-gray-600 font-semibold">Total</th>
                                <th class="border-b p-2 text-gray-600 font-semibold">Metode</th>
                                <th class="border-b p-2 text-gray-600 font-semibold text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                // Mengelompokkan data orders berdasarkan format tanggal
                                $groupedOrders = $orders->groupBy(function($order) {
                                    return $order->created_at->format('d F Y');
                                });
                            @endphp

                            @foreach($groupedOrders as $tanggal => $dailyOrders)
                                {{-- Baris Pemisah Tanggal --}}
                                <tr class="bg-blue-50/50">
                                    <td colspan="5" class="p-3 border-b border-t font-bold text-primary">
                                        📅 Transaksi Tanggal: {{ $tanggal }}
                                    </td>
                                </tr>

                                {{-- Loop Data Transaksi di Tanggal Tersebut --}}
                                @foreach($dailyOrders as $order)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        {{-- Waktu --}}
                                        <td class="border-b p-2 text-gray-600 font-medium">
                                            {{ $order->created_at->format('H:i') }} WIB
                                        </td>
                                        <td class="border-b p-2">#{{ $order->id }}</td>
                                        <td class="border-b p-2 font-semibold">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                                        <td class="border-b p-2">
                                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $order->payment_method === 'cash' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                                                {{ strtoupper($order->payment_method) }}
                                            </span>
                                        </td>
                                        
                                        {{-- Kolom Detail (Modal Alpine.js) --}}
                                        <td class="border-b p-2 text-center" x-data="{ openModal: false }">
                                            
                                            <button @click="openModal = true" class="text-primary hover:text-primary-dark font-medium text-sm underline focus:outline-none">
                                                Lihat Detail
                                            </button>

                                            {{-- Modal Pop up --}}
                                            <div x-show="openModal" 
                                                 style="display: none;"
                                                 class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm"
                                                 x-transition.opacity>
                                                
                                                <div @click.away="openModal = false" class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4 overflow-hidden text-left" x-transition>
                                                    
                                                    {{-- Header Modal --}}
                                                    <div class="px-4 py-3 border-b flex justify-between items-center bg-gray-50">
                                                        <h3 class="text-lg font-bold text-gray-800">Detail Transaksi #{{ $order->id }}</h3>
                                                        <button @click="openModal = false" class="text-gray-400 hover:text-red-500 text-2xl font-bold leading-none">&times;</button>
                                                    </div>

                                                    {{-- Body Modal --}}
                                                    <div class="p-4 max-h-96 overflow-y-auto text-sm">
                                                        <div class="mb-4 text-gray-600 space-y-1">
                                                            <p><span class="font-semibold w-24 inline-block">Waktu</span> : {{ $order->created_at->format('d F Y, H:i') }}</p>
                                                            <p><span class="font-semibold w-24 inline-block">Kasir</span> : {{ $order->user->name ?? 'Sistem' }}</p>
                                                            <p><span class="font-semibold w-24 inline-block">Pembayaran</span> : {{ strtoupper($order->payment_method) }}</p>
                                                        </div>

                                                        <table class="w-full text-left border-t border-b mt-2 mb-4">
                                                            <thead>
                                                                <tr class="text-gray-500 bg-gray-50">
                                                                    <th class="py-2 px-1">Menu</th>
                                                                    <th class="py-2 px-1 text-center">Qty</th>
                                                                    <th class="py-2 px-1 text-right">Subtotal</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="divide-y divide-gray-100">
                                                                @foreach($order->items as $item)
                                                                    <tr>
                                                                        <td class="py-2 px-1 text-gray-800">{{ $item->menu->nama ?? 'Menu Terhapus' }}</td>
                                                                        <td class="py-2 px-1 text-center text-gray-800">{{ $item->qty }}</td>
                                                                        <td class="py-2 px-1 text-right font-medium text-gray-800">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>

                                                        <div class="flex justify-between items-center text-lg font-bold text-gray-800">
                                                            <span>Total Belanja:</span>
                                                            <span class="text-blue-600">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                                                        </div>
                                                    </div>

                                                    {{-- Footer Modal --}}
                                                    <div class="px-4 py-3 border-t bg-gray-50 flex justify-end gap-2">
                                                        @if($order->with_receipt)
                                                            <a href="{{ route('kasir.struk', $order) }}" class="px-4 py-2 bg-blue-100 text-blue-700 hover:bg-blue-200 rounded text-sm font-medium transition">
                                                                Cetak Struk
                                                            </a>
                                                        @endif
                                                        <button @click="openModal = false" class="px-4 py-2 bg-gray-200 text-gray-700 hover:bg-gray-300 rounded text-sm font-medium transition">
                                                            Tutup
                                                        </button>
                                                    </div>

                                                </div>
                                            </div>

                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>