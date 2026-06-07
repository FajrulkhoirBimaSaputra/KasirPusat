<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('kasir.riwayat') }}"
                class="p-1.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h2 class="font-bold text-xl text-gray-800 leading-tight">
                Laporan Akhir Shift
            </h2>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">

        {{-- TOMBOL AKSI ATAS --}}
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-6">
            <div class="w-full sm:w-auto flex flex-col">
                <p class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-0.5">Shift Aktif</p>
                <div class="flex items-center gap-2 text-gray-800 font-bold">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    {{ Auth::user()->name }}
                    <span class="text-gray-300">|</span>
                    <span class="text-gray-500 font-medium">Buka: {{ $waktuBukaShift->format('d M Y • H:i') }}
                        WIB</span>
                </div>
            </div>

            <div class="w-full sm:w-auto flex gap-3">
                <a href="{{ route('kasir.manajemen-kas') }}"
                    class="flex-1 sm:flex-none inline-flex justify-center items-center gap-2 px-5 py-2.5 bg-white border-2 border-gray-200 hover:border-primary hover:text-primary text-gray-700 rounded-xl font-bold text-sm transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Manajemen Kas
                </a>

                {{-- DI SINI BAGIAN BARU: IMPLEMENTASI CUSTOM MODAL DENGAN ALPINE.JS --}}
                <div x-data="{ showConfirmModal: false }" class="flex-1 sm:flex-none">

                    {{-- Form Rahasia untuk Tutup Shift --}}
                    <form id="form-tutup-shift" action="{{ route('kasir.tutup-shift') }}" method="POST" class="hidden">
                        @csrf
                        <input type="hidden" name="expected_cash" value="{{ $jumlahTunaiDiharapkan }}">
                    </form>

                    {{-- Tombol Pemicu Modal --}}
                    <button type="button" @click="showConfirmModal = true"
                        class="w-full inline-flex justify-center items-center gap-2 px-5 py-2.5 bg-primary hover:bg-primary-dark text-white rounded-xl font-bold text-sm shadow-sm transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Cetak & Tutup Shift
                    </button>

                    {{-- POP-UP MODAL BOX --}}
                    <div x-show="showConfirmModal"
                        class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-0" x-cloak>
                        {{-- Background Overlay Gelap Halus --}}
                        <div x-show="showConfirmModal" x-transition:enter="ease-out duration-300"
                            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                            x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm"
                            @click="showConfirmModal = false"></div>

                        {{-- Box Panel Modal --}}
                        <div x-show="showConfirmModal" x-transition:enter="ease-out duration-300"
                            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                            x-transition:leave="ease-in duration-200"
                            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                            class="relative bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all">

                            <div class="h-2 bg-red-500 w-full"></div>

                            <div class="p-6 sm:p-8 text-center">
                                <div
                                    class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-50 mb-5 border-4 border-red-100">
                                    <svg class="h-8 w-8 text-red-500" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>

                                <h3 class="text-xl font-black text-gray-900 mb-2">Akhiri Shift Sekarang?</h3>
                                <p class="text-sm text-gray-500 font-medium leading-relaxed mb-6">
                                    Setelah menekan tombol ini, mesin kasir akan mencetak laporan dan <b>akses menu akan
                                        otomatis dikunci</b>. Anda harus membuka shift baru untuk bertransaksi kembali.
                                </p>

                                <div class="flex flex-col sm:flex-row gap-3">
                                    <button type="button" @click="showConfirmModal = false"
                                        class="w-full inline-flex justify-center items-center px-4 py-3 bg-white border-2 border-gray-200 text-gray-700 hover:bg-gray-50 hover:border-gray-300 rounded-xl font-bold text-sm transition-colors focus:outline-none">
                                        Batal
                                    </button>

                                    <button type="button" onclick="prosesTutupShiftModal()"
                                        class="w-full inline-flex justify-center items-center px-4 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold text-sm shadow-md shadow-red-600/20 transition-colors focus:outline-none">
                                        Ya, Cetak & Tutup
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Script Print Terpisah di Bawah Modal --}}
                <script>
                    function prosesTutupShiftModal() {
                        window.print();
                        setTimeout(() => {
                            document.getElementById('form-tutup-shift').submit();
                        }, 1000);
                    }
                </script>

            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- KIRI: KONTROL LACI UANG (CASH DRAWER) --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
                <div class="p-5 border-b border-gray-100 bg-gray-50 flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800">Kontrol Laci Uang</h3>
                </div>

                <div class="p-5 flex-1 flex flex-col justify-between">
                    <div class="space-y-4">
                        <div class="flex justify-between items-center text-sm">
                            <span class="font-semibold text-gray-500">Modal Awal</span>
                            <span class="font-bold text-gray-800">Rp
                                {{ number_format($modalAwal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="font-semibold text-gray-500">Pembayaran Tunai (Masuk)</span>
                            <span class="font-bold text-emerald-600">+ Rp
                                {{ number_format($tunai, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="font-semibold text-gray-500">Kas Pemasukan Tambahan</span>
                            <span class="font-bold text-emerald-600">+ Rp
                                {{ number_format($pemasukan, 0, ',', '.') }}</span>
                        </div>
                        <hr class="border-gray-100 border-dashed">
                        <div class="flex justify-between items-center text-sm">
                            <span class="font-semibold text-gray-500">Refund Pesanan (Keluar)</span>
                            <span class="font-bold text-amber-500">- Rp
                                {{ number_format($uangDikembalikan, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="font-semibold text-gray-500">Kas Pengeluaran Keluar</span>
                            <span class="font-bold text-red-500">- Rp
                                {{ number_format($pengeluaran, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="mt-8 p-4 bg-primary/10 border border-primary/20 rounded-xl text-center">
                        <p class="text-xs font-bold text-primary uppercase tracking-wider mb-1">Uang Fisik Diharapkan
                            di
                            Laci</p>
                        <p class="text-3xl font-black text-primary leading-tight">Rp
                            {{ number_format($jumlahTunaiDiharapkan, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            {{-- KANAN: RINGKASAN PENJUALAN --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
                <div class="p-5 border-b border-gray-100 bg-gray-50 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800">Ringkasan Penjualan</h3>
                </div>

                <div class="p-5 flex-1 flex flex-col">

                    {{-- Box Jumlah Transaksi --}}
                    <div
                        class="flex justify-between items-center bg-gray-50 p-4 rounded-xl border border-gray-200 mb-6">
                        <div>
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-0.5">Total Transaksi
                            </p>
                            <p class="text-sm font-medium text-gray-400">Jumlah struk hari ini</p>
                        </div>
                        <p class="text-2xl font-black text-gray-800">{{ $totalTransaksiHariIni }}</p>
                    </div>

                    <div class="space-y-4">
                        <div class="flex justify-between items-center text-sm">
                            <span class="font-semibold text-gray-500">Penjualan Kotor</span>
                            <span class="font-bold text-gray-800">Rp
                                {{ number_format($penjualanKotor, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="font-semibold text-gray-500">Diskon Produk</span>
                            <span class="font-bold text-gray-800">Rp 0</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="font-semibold text-gray-500">Refund / Pengembalian</span>
                            <span class="font-bold text-amber-500">- Rp
                                {{ number_format($uangDikembalikan, 0, ',', '.') }}</span>
                        </div>

                        <div class="pt-4 border-t border-gray-100 mt-2">
                            <div class="flex justify-between items-center mb-3">
                                <span class="font-bold text-gray-800 text-base">Penjualan Bersih</span>
                                <span class="font-black text-gray-900 text-lg">Rp
                                    {{ number_format($penjualanBersih, 0, ',', '.') }}</span>
                            </div>

                            {{-- Rincian Metode --}}
                            <div class="pl-4 border-l-2 border-gray-200 space-y-2">
                                <div class="flex justify-between items-center text-sm">
                                    <span class="font-semibold text-gray-500 flex items-center gap-1.5">
                                        <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div> Tunai (Cash)
                                    </span>
                                    <span class="font-bold text-gray-700">Rp
                                        {{ number_format($tunai, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="font-semibold text-gray-500 flex items-center gap-1.5">
                                        <div class="w-1.5 h-1.5 rounded-full bg-blue-500"></div> QRIS (Midtrans)
                                    </span>
                                    <span class="font-bold text-gray-700">Rp
                                        {{ number_format($qris, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>

    </div>

    <script>
        function prosesTutupShiftModal() {
            // 1. Buka halaman struk di tab/jendela baru (Otomatis akan memicu print di tab tersebut)
            window.open("{{ route('kasir.struk-shift') }}", "_blank");

            // 2. Beri jeda 0.5 detik, lalu submit form tutup shift di halaman utama ini
            setTimeout(() => {
                document.getElementById('form-tutup-shift').submit();
            }, 500);
        }
    </script>

    <style>
        /* Mencegah modal berkedip sebelum Alpine.js ter-load sempurna */
        [x-cloak] {
            display: none !important;
        }

        @media print {
            body {
                background-color: white !important;
            }

            header,
            nav,
            button,
            a {
                display: none !important;
            }

            .max-w-4xl {
                max-width: 100% !important;
                padding: 0 !important;
            }

            .grid {
                display: block !important;
            }

            .bg-white {
                box-shadow: none !important;
                border: 1px solid #e5e7eb !important;
                margin-bottom: 20px !important;
            }
        }
    </style>
</x-app-layout>
