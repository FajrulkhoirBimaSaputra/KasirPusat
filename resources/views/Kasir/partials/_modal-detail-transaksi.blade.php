<div x-show="openModal" class="fixed inset-0 z-[100] overflow-y-auto" x-cloak>
    <div class="flex items-end sm:items-center justify-center min-h-screen p-4 text-center sm:p-0">
        
        <div x-show="openModal" 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
             @click="openModal = false" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"></div>

        <div x-show="openModal" 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            
            {{-- Header Modal --}}
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-black text-gray-900">Detail Order #{{ $order->id }}</h3>
                    <p class="text-xs font-medium text-gray-500 mt-0.5">{{ $order->created_at->format('d M Y, H:i') }} WIB</p>
                </div>
                <button @click="openModal = false" class="text-gray-400 hover:text-gray-600 bg-white hover:bg-gray-100 rounded-full p-1.5 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            {{-- Body Modal --}}
            <div class="px-6 py-5 max-h-[60vh] overflow-y-auto">
                
                {{-- Info Utama --}}
                <div class="grid grid-cols-2 gap-4 mb-6 bg-gray-50 p-4 rounded-xl border border-gray-100">
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Kasir Bertugas</p>
                        <p class="text-sm font-bold text-gray-800">{{ $order->user->name ?? 'Sistem' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Status Pembayaran</p>
                        @if($order->payment_status === 'paid')
                            <span class="text-sm font-bold text-emerald-600">LUNAS ({{ strtoupper($order->payment_method) }})</span>
                        @else
                            <span class="text-sm font-bold text-amber-500">PENDING ({{ strtoupper($order->payment_method) }})</span>
                        @endif
                    </div>
                </div>

                {{-- Tabel Item Belanja --}}
                <div class="mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Rincian Pembelian</div>
                <div class="border border-gray-200 rounded-xl overflow-hidden mb-6">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200 text-xs font-bold text-gray-500">
                            <tr>
                                <th class="py-2.5 px-4">Item</th>
                                <th class="py-2.5 px-4 text-center">Qty</th>
                                <th class="py-2.5 px-4 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($order->items as $item)
                                <tr>
                                    <td class="py-3 px-4">
                                        <p class="font-bold text-gray-800">{{ $item->menu->nama ?? 'Menu Terhapus' }}</p>
                                        {{-- Menampilkan Catatan jika ada --}}
                                        @if($item->catatan)
                                            <p class="text-[10px] font-medium text-amber-600 mt-0.5 bg-amber-50 inline-block px-1.5 py-0.5 rounded border border-amber-100">Note: {{ $item->catatan }}</p>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-center font-bold text-gray-600">{{ $item->qty }}</td>
                                    <td class="py-3 px-4 text-right font-bold text-gray-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Kalkulasi Uang (Hanya tampil jika bayar Cash) --}}
                <div class="space-y-2 text-sm border-b border-gray-100 pb-4 mb-4">
                    <div class="flex justify-between items-center">
                        <span class="font-semibold text-gray-500">Subtotal Belanja</span>
                        <span class="font-bold text-gray-800">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                    @if($order->payment_method === 'cash')
                        <div class="flex justify-between items-center">
                            <span class="font-semibold text-gray-500">Uang Diterima</span>
                            <span class="font-bold text-gray-800">Rp {{ number_format($order->uang_bayar ?? $order->total, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-emerald-600">
                            <span class="font-semibold">Uang Kembalian</span>
                            <span class="font-bold">Rp {{ number_format($order->uang_kembali ?? 0, 0, ',', '.') }}</span>
                        </div>
                    @endif
                </div>

                {{-- Grand Total --}}
                <div class="flex justify-between items-center p-3 bg-primary/10 rounded-xl border border-primary/20">
                    <span class="text-sm font-black text-primary uppercase tracking-wider">Total Akhir</span>
                    <span class="text-xl font-black text-primary">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                </div>

            </div>

            {{-- Footer Modal --}}
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex flex-col sm:flex-row-reverse gap-3">
                @if($order->payment_status === 'paid')
                    <a href="{{ route('kasir.struk', $order) }}" target="_blank" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 py-2 px-6 rounded-xl shadow-sm text-sm font-bold text-white bg-primary hover:bg-primary-dark transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                        Cetak Struk Ulang
                    </a>
                @endif
                <button @click="openModal = false" class="w-full sm:w-auto inline-flex justify-center items-center py-2 px-6 border border-gray-300 rounded-xl shadow-sm text-sm font-bold text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                    Tutup
                </button>
            </div>
            
        </div>
    </div>
</div>