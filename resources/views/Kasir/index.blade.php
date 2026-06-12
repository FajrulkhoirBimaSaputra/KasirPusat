<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <h2 class="font-bold text-xl text-gray-800 leading-tight">
                Mesin Kasir (POS)
            </h2>
        </div>
    </x-slot>

    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>

    {{-- CONTAINER UNTUK TOAST NOTIFICATION --}}
    <div id="toast-container"
        class="fixed top-5 left-1/2 transform -translate-x-1/2 z-[100] flex flex-col gap-2 pointer-events-none"></div>

    <div class="max-w-7xl mx-auto p-2 sm:p-4 md:p-6" x-data="{ activeCategory: 'semua' }">

        {{-- ALERT SUCCESS BEKAS SUBMIT SEBELUMNYA --}}
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition.duration.500ms
                class="mb-6 flex items-center p-3 sm:p-4 text-emerald-800 rounded-xl bg-emerald-50 border border-emerald-200 shadow-sm"
                role="alert">
                <svg class="flex-shrink-0 w-5 h-5 mr-3 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                </svg>
                <div class="text-sm font-medium">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        {{-- IFRAME AUTO-PRINT STRUK --}}
        @if (session('print_struk_url'))
            <iframe src="{{ session('print_struk_url') }}" class="hidden" id="printFrame"
                onload="this.contentWindow.print();"></iframe>
        @endif

        <form method="POST" action="{{ route('kasir.store') }}" id="kasirForm">
            @csrf

            <div class="flex flex-col lg:flex-row gap-4 sm:gap-6">

                {{-- ======================== --}}
                {{-- MENU SECTION (KIRI)      --}}
                {{-- ======================== --}}
                <div class="w-full lg:w-2/3 flex flex-col gap-3 sm:gap-4">

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between px-1 sm:px-2 gap-3">
                        <h2 class="text-lg sm:text-xl font-bold text-gray-800">Katalog Menu</h2>

                        {{-- Filter Kategori (Diperkecil di Mobile) --}}
                        <div class="flex gap-1.5 sm:gap-2 overflow-x-auto pb-2 custom-scrollbar">
                            <button type="button" @click="activeCategory = 'semua'"
                                :class="activeCategory === 'semua' ? 'bg-gray-800 text-white' :
                                    'bg-white text-gray-600 hover:bg-gray-100'"
                                class="px-3 py-1.5 sm:px-4 sm:py-1.5 rounded-full text-[10px] sm:text-xs font-bold whitespace-nowrap border shadow-sm transition-colors">
                                Semua
                            </button>
                            @foreach ($categories as $cat)
                                <button type="button" @click="activeCategory = '{{ $cat }}'"
                                    :class="activeCategory === '{{ $cat }}' ? 'bg-primary text-white' :
                                        'bg-white text-gray-600 hover:bg-gray-100'"
                                    class="px-3 py-1.5 sm:px-4 sm:py-1.5 rounded-full text-[10px] sm:text-xs font-bold uppercase whitespace-nowrap border shadow-sm transition-colors">
                                    {{ $cat }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Grid Menu (3 Kolom di Mobile, lebih ringkas) --}}
                    <div class="grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-4 gap-2 sm:gap-4">
                        @foreach ($menus as $menu)
                            <div x-show="activeCategory === 'semua' || activeCategory === '{{ $menu->jenis }}'"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                onclick="addItem({{ $menu->id }}, '{{ addslashes($menu->nama) }}', {{ $menu->harga }})"
                                class="bg-white border border-gray-200 rounded-xl sm:rounded-2xl p-1.5 sm:p-3 text-center cursor-pointer hover:border-primary hover:shadow-md transition-all duration-200 active:scale-95 group flex flex-col">

                                <div
                                    class="w-full aspect-square bg-gray-50 rounded-lg sm:rounded-xl mb-1.5 sm:mb-3 flex items-center justify-center overflow-hidden border border-gray-100 relative">
                                    @if ($menu->image_path)
                                        <img src="{{ asset('storage/' . $menu->image_path) }}"
                                            alt="{{ $menu->nama }}"
                                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                    @else
                                        <div class="flex flex-col items-center text-gray-300">
                                            <svg class="w-6 h-6 sm:w-10 sm:h-10" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <h3
                                    class="font-bold text-gray-800 text-[10px] sm:text-sm leading-tight h-8 sm:h-10 flex items-center justify-center overflow-hidden">
                                    {{ $menu->nama }}
                                </h3>
                                <p class="text-primary font-bold mt-auto text-[11px] sm:text-sm">
                                    Rp {{ number_format($menu->harga, 0, ',', '.') }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- ======================== --}}
                {{-- CART SECTION (KANAN)     --}}
                {{-- ======================== --}}
                <div class="w-full lg:w-1/3 mt-2 lg:mt-0">
                    <div
                        class="bg-white border border-gray-100 rounded-2xl p-4 sm:p-5 shadow-sm lg:sticky lg:top-6 flex flex-col max-h-[85vh] sm:max-h-[90vh]">

                        <div class="flex items-center justify-between mb-3 sm:mb-4 shrink-0">
                            <h3 class="font-bold text-base sm:text-lg text-gray-800">Keranjang</h3>
                            <span
                                class="bg-primary/10 text-primary text-[10px] sm:text-xs font-bold py-1 px-2 rounded-lg"
                                id="item-count">0 Item</span>
                        </div>

                        {{-- Area Item Keranjang --}}
                        <div id="cart"
                            class="space-y-2 sm:space-y-3 flex-1 overflow-y-auto pr-2 custom-scrollbar pb-2 sm:pb-4 min-h-[150px] sm:min-h-[200px]">
                            <div class="h-full flex flex-col items-center justify-center text-gray-400">
                                <svg class="w-10 h-10 sm:w-12 sm:h-12 mb-2 opacity-50" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                                <p class="font-medium text-xs sm:text-sm">Keranjang masih kosong</p>
                            </div>
                        </div>

                        {{-- Area Pembayaran --}}
                        <div class="border-t border-gray-100 mt-3 pt-3 sm:mt-4 sm:pt-4 shrink-0">

                            {{-- UI BARU: Visual Card Payment Method --}}
                            <div class="mb-4">
                                <label
                                    class="text-[9px] sm:text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2 block">Metode
                                    Pembayaran</label>
                                <input type="hidden" name="payment_method" id="payment_method" value="">

                                <div class="grid grid-cols-2 gap-2 sm:gap-3">
                                    {{-- Opsi Tunai --}}
                                    <button type="button" id="btn-cash" onclick="selectPayment('cash')"
                                        class="flex flex-col items-center justify-center p-2 sm:p-3 border-2 border-gray-100 bg-gray-50 rounded-xl hover:border-emerald-400 hover:bg-emerald-50 transition-all focus:outline-none group">
                                        <svg class="w-5 h-5 sm:w-6 sm:h-6 mb-1 sm:mb-1.5 text-gray-400 group-hover:text-emerald-500 transition-colors"
                                            id="icon-cash" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        <span
                                            class="text-[10px] sm:text-xs font-bold text-gray-600 group-hover:text-emerald-700"
                                            id="text-cash">Tunai / Cash</span>
                                    </button>

                                    {{-- Opsi QRIS --}}
                                    <button type="button" id="btn-qris" onclick="selectPayment('qris')"
                                        class="flex flex-col items-center justify-center p-2 sm:p-3 border-2 border-gray-100 bg-gray-50 rounded-xl hover:border-blue-400 hover:bg-blue-50 transition-all focus:outline-none group">
                                        <svg class="w-5 h-5 sm:w-6 sm:h-6 mb-1 sm:mb-1.5 text-gray-400 group-hover:text-blue-500 transition-colors"
                                            id="icon-qris" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                        </svg>
                                        <span
                                            class="text-[10px] sm:text-xs font-bold text-gray-600 group-hover:text-blue-700"
                                            id="text-qris">QRIS (Digital)</span>
                                    </button>
                                </div>
                            </div>

                            <div id="cash_input_area"
                                class="hidden mb-3 sm:mb-4 p-3 sm:p-4 bg-emerald-50 border border-emerald-100 rounded-xl space-y-2 sm:space-y-3">
                                <div>
                                    <label class="text-[10px] sm:text-xs font-bold text-emerald-800 block mb-1">Uang
                                        Diterima dari
                                        Pelanggan (Rp)</label>
                                    <input type="number" name="uang_bayar" id="uang_bayar"
                                        oninput="calculateChange()"
                                        class="w-full border-emerald-200 shadow-inner rounded-lg p-2 sm:p-2.5 text-sm font-bold text-gray-900 focus:ring-emerald-500 focus:border-emerald-500"
                                        placeholder="Ketik nominal uang...">
                                </div>
                                <div
                                    class="flex justify-between items-center text-xs sm:text-sm pt-2 border-t border-emerald-200/50">
                                    <span class="font-bold text-emerald-700">Kembalian:</span>
                                    <span class="font-black text-emerald-600 text-base sm:text-lg">Rp <span
                                            id="uang_kembali">0</span></span>
                                </div>
                            </div>

                            <div
                                class="flex justify-between items-end mb-4 sm:mb-5 bg-gray-800 p-3 sm:p-4 rounded-xl shadow-inner">
                                <span
                                    class="text-gray-400 font-bold uppercase text-[10px] sm:text-xs tracking-wider">Total
                                    Tagihan</span>
                                <div class="text-2xl sm:text-3xl font-black text-white">
                                    <span class="text-sm sm:text-base font-bold text-gray-400 mr-1">Rp</span><span
                                        id="total">0</span>
                                </div>
                            </div>

                            <input type="hidden" name="with_receipt" id="with_receipt">
                            <div id="items-input"></div>

                            <div class="grid grid-cols-2 gap-2 sm:gap-3">
                                <button type="button" onclick="checkout(0)"
                                    class="w-full bg-white border-2 border-gray-200 hover:bg-gray-50 hover:border-gray-300 text-gray-700 py-2 sm:py-3 rounded-xl font-bold transition-all text-xs sm:text-sm">
                                    Hanya Bayar
                                </button>
                                <button type="button" onclick="checkout(1)"
                                    class="w-full bg-primary hover:bg-primary-dark text-white py-2 sm:py-3 rounded-xl font-bold transition-all shadow-md shadow-primary/20 text-xs sm:text-sm flex items-center justify-center gap-1.5">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                    </svg>
                                    Cetak & Bayar
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </form>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
            height: 4px;
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

    <script>
        let cart = {};
        let grandTotal = 0;

        // FUNGSI UNTUK MENAMPILKAN TOAST NOTIFICATION MODERN (Menggantikan Alert)
        function showToast(message, type = 'error') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');

            let bgClass = type === 'error' ? 'bg-red-50 border-red-200 text-red-800' :
                (type === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-800' :
                    'bg-amber-50 border-amber-200 text-amber-800');

            let iconSvg = type === 'error' ?
                `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />` :
                (type === 'success' ?
                    `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />` :
                    `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />`
                );

            let iconColor = type === 'error' ? 'text-red-500' : (type === 'success' ? 'text-emerald-500' :
                'text-amber-500');

            toast.className =
                `transform transition-all duration-300 -translate-y-10 opacity-0 flex items-center p-3 sm:p-4 rounded-xl sm:rounded-2xl shadow-lg border ${bgClass} pointer-events-auto mx-4 w-[calc(100%-2rem)] sm:w-auto`;
            toast.innerHTML = `
                <svg class="w-5 h-5 sm:w-6 sm:h-6 mr-2 sm:mr-3 ${iconColor} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">${iconSvg}</svg>
                <span class="font-bold text-xs sm:text-sm">${message}</span>
            `;

            container.appendChild(toast);

            setTimeout(() => {
                toast.classList.remove('-translate-y-10', 'opacity-0');
            }, 10);

            setTimeout(() => {
                toast.classList.add('-translate-y-10', 'opacity-0');
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }, 3500);
        }

        // FUNGSI UNTUK INTERAKSI KARTU METODE PEMBAYARAN
        function selectPayment(method) {
            document.getElementById('payment_method').value = method;
            const cashArea = document.getElementById('cash_input_area');

            const btnCash = document.getElementById('btn-cash');
            const iconCash = document.getElementById('icon-cash');
            const textCash = document.getElementById('text-cash');

            const btnQris = document.getElementById('btn-qris');
            const iconQris = document.getElementById('icon-qris');
            const textQris = document.getElementById('text-qris');

            [btnCash, btnQris].forEach(btn => btn.className =
                "flex flex-col items-center justify-center p-2 sm:p-3 border-2 border-gray-100 bg-gray-50 rounded-xl hover:border-gray-300 transition-all focus:outline-none group"
            );
            [iconCash, iconQris].forEach(icon => icon.className =
                "w-5 h-5 sm:w-6 sm:h-6 mb-1 sm:mb-1.5 text-gray-400 transition-colors");
            [textCash, textQris].forEach(text => text.className = "text-[10px] sm:text-xs font-bold text-gray-600");

            if (method === 'cash') {
                btnCash.className =
                    "flex flex-col items-center justify-center p-2 sm:p-3 border-2 border-emerald-500 bg-emerald-50 rounded-xl shadow-sm transition-all focus:outline-none scale-105";
                iconCash.className = "w-5 h-5 sm:w-6 sm:h-6 mb-1 sm:mb-1.5 text-emerald-600 transition-colors";
                textCash.className = "text-[10px] sm:text-xs font-bold text-emerald-700";

                cashArea.classList.remove('hidden');
                setTimeout(() => document.getElementById('uang_bayar').focus(), 100);
            } else {
                btnQris.className =
                    "flex flex-col items-center justify-center p-2 sm:p-3 border-2 border-blue-500 bg-blue-50 rounded-xl shadow-sm transition-all focus:outline-none scale-105";
                iconQris.className = "w-5 h-5 sm:w-6 sm:h-6 mb-1 sm:mb-1.5 text-blue-600 transition-colors";
                textQris.className = "text-[10px] sm:text-xs font-bold text-blue-700";

                cashArea.classList.add('hidden');
                document.getElementById('uang_bayar').value = '';
                calculateChange();
            }
        }

        function addItem(id, nama, harga) {
            if (!cart[id]) {
                cart[id] = {
                    menu_id: id,
                    nama: nama,
                    harga: harga,
                    qty: 1,
                    catatan: ''
                };
            } else {
                cart[id].qty++;
            }
            renderCart();
        }

        function changeQty(id, diff) {
            cart[id].qty += diff;
            if (cart[id].qty <= 0) delete cart[id];
            renderCart();
            calculateChange();
        }

        function updateNote(id, val) {
            if (cart[id]) {
                cart[id].catatan = val;
            }
        }

        function calculateChange() {
            const bayar = parseInt(document.getElementById('uang_bayar').value) || 0;
            let kembali = bayar - grandTotal;
            document.getElementById('uang_kembali').innerText = kembali > 0 ? kembali.toLocaleString('id-ID') : '0';
        }

        function renderCart() {
            let html = '';
            grandTotal = 0;
            let count = 0;
            const entries = Object.values(cart);

            if (entries.length === 0) {
                html = `
                <div class="h-full flex flex-col items-center justify-center text-gray-400">
                    <svg class="w-10 h-10 sm:w-12 sm:h-12 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <p class="font-medium text-xs sm:text-sm">Keranjang masih kosong</p>
                </div>`;
            } else {
                entries.forEach(item => {
                    grandTotal += item.harga * item.qty;
                    count += item.qty;
                    html += `
                        <div class="flex flex-col bg-white border border-gray-100 p-2 sm:p-3 rounded-lg sm:rounded-xl shadow-sm">
                            <div class="flex justify-between items-start mb-1.5 sm:mb-2">
                                <div class="flex-1 pr-2">
                                    <p class="font-bold text-xs sm:text-sm text-gray-800 leading-tight">${item.nama}</p>
                                    <p class="text-[10px] sm:text-xs text-primary font-bold mt-0.5">Rp ${item.harga.toLocaleString('id-ID')}</p>
                                </div>
                                <div class="flex items-center gap-1.5 sm:gap-2 bg-gray-50 border border-gray-200 rounded-md sm:rounded-lg p-0.5">
                                    <button type="button" onclick="changeQty(${item.menu_id}, -1)" class="w-5 h-5 sm:w-6 sm:h-6 flex items-center justify-center bg-white rounded shadow-sm text-gray-600 font-bold hover:text-red-500">-</button>
                                    <span class="text-[10px] sm:text-xs font-bold min-w-[14px] sm:min-w-[16px] text-center">${item.qty}</span>
                                    <button type="button" onclick="changeQty(${item.menu_id}, 1)" class="w-5 h-5 sm:w-6 sm:h-6 flex items-center justify-center bg-white rounded shadow-sm text-gray-600 font-bold hover:text-primary">+</button>
                                </div>
                            </div>
                            <input type="text" onchange="updateNote(${item.menu_id}, this.value)" value="${item.catatan}" placeholder="Catatan (cth: Es dipisah)..." class="w-full text-[10px] sm:text-xs bg-gray-50 border-transparent focus:border-gray-300 focus:ring-0 rounded p-1 sm:p-1.5 text-gray-600 placeholder-gray-400">
                        </div>
                    `;
                });
            }

            document.getElementById('cart').innerHTML = html;
            document.getElementById('total').innerText = grandTotal.toLocaleString('id-ID');
            document.getElementById('item-count').innerText = `${count} Item`;
            calculateChange();
        }

        function checkout(receipt) {
            if (Object.keys(cart).length === 0) {
                showToast('Pilih menu terlebih dahulu sebelum membayar!', 'error');
                return;
            }

            const payment = document.getElementById('payment_method').value;
            if (!payment) {
                showToast('Silakan pilih salah satu Metode Pembayaran!', 'error');
                return;
            }

            if (payment === 'cash') {
                const bayar = parseInt(document.getElementById('uang_bayar').value) || 0;
                if (bayar < grandTotal) {
                    showToast('Nominal uang tunai kurang dari total tagihan!', 'error');
                    return;
                }
            }

            document.getElementById('with_receipt').value = receipt;
            const itemsInput = document.getElementById('items-input');
            itemsInput.innerHTML = '';

            Object.values(cart).forEach((item, index) => {
                itemsInput.innerHTML += `
                    <input type="hidden" name="items[${index}][menu_id]" value="${item.menu_id}">
                    <input type="hidden" name="items[${index}][harga]" value="${item.harga}">
                    <input type="hidden" name="items[${index}][qty]" value="${item.qty}">
                    <input type="hidden" name="items[${index}][catatan]" value="${item.catatan}">
                `;
            });

            document.getElementById('kasirForm').submit();
        }

        // ==========================================
        // HANDLE MIDTRANS SNAP POPUP (JIKA QRIS)
        // ==========================================
        @if (session('snap_token'))
            snap.pay('{{ session('snap_token') }}', {
                onSuccess: function(result) {
                    showToast("Pembayaran QRIS Berhasil!", "success");
                    setTimeout(() => {
                        window.location.href =
                            "{{ url('/kasir/qris-success') }}/{{ session('order_id') }}?with_receipt={{ session('with_receipt') }}";
                    }, 1000);
                },
                onPending: function(result) {
                    showToast("Menunggu pembayaran Anda diselesaikan.", "warning");
                },
                onError: function(result) {
                    showToast("Pembayaran QRIS gagal. Silakan coba lagi.", "error");
                },
                onClose: function() {
                    showToast("Popup ditutup tanpa menyelesaikan pembayaran.", "warning");
                }
            });
        @endif
    </script>
</x-app-layout>
