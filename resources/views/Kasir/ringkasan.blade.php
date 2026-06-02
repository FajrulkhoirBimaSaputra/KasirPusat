<x-app-layout>
    <div class="p-4 sm:p-6 flex justify-center bg-gray-100 min-h-screen">
        
        {{-- Kontainer Utama (Meniru Layar Tablet) --}}
        <div class="w-full max-w-2xl bg-white shadow-xl rounded-lg overflow-hidden border border-gray-200">
            
            {{-- Header Hijau --}}
            <div class="bg-primary text-white p-4 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <a href="{{ route('kasir.riwayat') }}" class="hover:text-gray-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </a>
                    <h1 class="text-xl font-medium">Shift</h1>
                </div>
                <div class="flex items-center gap-4">
                    <svg class="w-6 h-6 cursor-pointer hover:text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <svg class="w-6 h-6 cursor-pointer hover:text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                </div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="p-4 flex gap-4 border-b">
                <a href="{{ route('kasir.manajemen-kas') }}" class="flex-1 text-center py-2 text-primary border border-primary rounded font-medium hover:bg-green-50 transition block">
                    MANAJEMEN KAS
                </a>
                <button class="flex-1 py-2 text-primary border border-primary rounded font-medium hover:bg-green-50 transition">
                    TUTUP SHIFT
                </button>
            </div>

            {{-- Info Shift --}}
            <div class="p-4 border-b text-gray-700 text-sm space-y-3">
                <div class="flex justify-between">
                    <span>Nomor shift: {{ now()->format('d') }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Shift dibuka: {{ Auth::user()->name }}</span>
                    <span>{{ now()->format('d/m/y H.i') }}</span>
                </div>
            </div>

            {{-- Bagian Laci Uang --}}
            <div class="p-4 border-b">
                <h3 class="text-primary font-medium mb-3">Laci uang</h3>
                
                <div class="space-y-2 text-sm text-gray-600">
                    <div class="flex justify-between">
                        <span>Modal awal</span>
                        <span>Rp{{ number_format($modalAwal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Pembayaran tunai</span>
                        <span>Rp{{ number_format($tunai, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Uang yang dikembalikan</span>
                        <span>Rp{{ number_format($uangDikembalikan, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Pemasukan</span>
                        <span>Rp{{ number_format($pemasukan, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Pengeluaran</span>
                        <span>Rp{{ number_format($pengeluaran, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t flex justify-between font-bold text-gray-800">
                    <span>Jumlah uang tunai yang diharapkan</span>
                    <span>Rp{{ number_format($jumlahTunaiDiharapkan, 0, ',', '.') }}</span>
                </div>
            </div>

            {{-- Bagian Ringkasan Penjualan --}}
            <div class="p-4">
                <h3 class="text-primary font-medium mb-3">Ringkasan penjualan</h3>
                
                <div class="space-y-2 text-sm text-gray-600">
                    {{-- TAMBAHAN: Total Transaksi --}}
                    <div class="flex justify-between font-bold text-gray-800 border-b pb-2 mb-2">
                        <span>Total Transaksi Hari Ini</span>
                        <span>{{ $totalTransaksiHariIni }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span>Penjualan Kotor</span>
                        <span>Rp{{ number_format($penjualanKotor, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Pengembalian</span>
                        <span>Rp0</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Diskon</span>
                        <span>Rp0</span>
                    </div>
                    
                    <div class="pt-2 mt-2">
                        <div class="flex justify-between font-bold text-gray-800 mb-2">
                            <span>Penjualan bersih</span>
                            <span>Rp{{ number_format($penjualanBersih, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between ml-4">
                            <span>Tunai</span>
                            <span>Rp{{ number_format($tunai, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between ml-4 mt-2">
                            <span>Qris</span>
                            <span>Rp{{ number_format($qris, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>