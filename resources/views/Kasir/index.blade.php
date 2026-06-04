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

    <div class="max-w-7xl mx-auto p-2 sm:p-4 md:p-6" x-data="{ activeCategory: 'semua' }">

        {{-- ALERT SUCCESS --}}
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition.duration.500ms
                class="mb-6 flex items-center p-4 text-emerald-800 rounded-xl bg-emerald-50 border border-emerald-200 shadow-sm"
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

            <div class="flex flex-col lg:flex-row gap-6">

                {{-- ======================== --}}
                {{-- MENU SECTION (KIRI)      --}}
                {{-- ======================== --}}
                <div class="w-full lg:w-2/3 flex flex-col gap-4">

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between px-2 gap-3">
                        <h2 class="text-xl font-bold text-gray-800">Katalog Menu</h2>

                        {{-- Filter Kategori --}}
                        <div class="flex gap-2 overflow-x-auto pb-2 custom-scrollbar">
                            <button type="button" @click="activeCategory = 'semua'"
                                :class="activeCategory === 'semua' ? 'bg-gray-800 text-white' :
                                    'bg-white text-gray-600 hover:bg-gray-100'"
                                class="px-4 py-1.5 rounded-full text-xs font-bold whitespace-nowrap border shadow-sm transition-colors">
                                Semua
                            </button>
                            @foreach ($categories as $cat)
                                <button type="button" @click="activeCategory = '{{ $cat }}'"
                                    :class="activeCategory === '{{ $cat }}' ? 'bg-primary text-white' :
                                        'bg-white text-gray-600 hover:bg-gray-100'"
                                    class="px-4 py-1.5 rounded-full text-xs font-bold uppercase whitespace-nowrap border shadow-sm transition-colors">
                                    {{ $cat }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Grid Menu --}}
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
                        @foreach ($menus as $menu)
                            <div x-show="activeCategory === 'semua' || activeCategory === '{{ $menu->jenis }}'"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                onclick="addItem({{ $menu->id }}, '{{ addslashes($menu->nama) }}', {{ $menu->harga }})"
                                class="bg-white border border-gray-200 rounded-2xl p-2 sm:p-3 text-center cursor-pointer hover:border-primary hover:shadow-md transition-all duration-200 active:scale-95 group flex flex-col">

                                <div
                                    class="w-full aspect-square bg-gray-50 rounded-xl mb-3 flex items-center justify-center overflow-hidden border border-gray-100">
                                    @if ($menu->image_path)
                                        <img src="{{ asset('storage/' . $menu->image_path) }}"
                                            alt="{{ $menu->nama }}"
                                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                    @else
                                        <div class="flex flex-col items-center text-gray-300">
                                            <svg class="w-10 h-10" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <h3
                                    class="font-bold text-gray-800 text-xs sm:text-sm leading-tight h-10 flex items-center justify-center">
                                    {{ $menu->nama }}
                                </h3>
                                <p class="text-primary font-bold mt-auto text-sm">
                                    Rp {{ number_format($menu->harga, 0, ',', '.') }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- ======================== --}}
                {{-- CART SECTION (KANAN)     --}}
                {{-- ======================== --}}
                <div class="w-full lg:w-1/3">
                    <div
                        class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm lg:sticky lg:top-6 flex flex-col max-h-[90vh]">

                        <div class="flex items-center justify-between mb-4 shrink-0">
                            <h3 class="font-bold text-lg text-gray-800">Keranjang</h3>
                            <span class="bg-primary/10 text-primary text-xs font-bold py-1 px-2 rounded-lg"
                                id="item-count">0 Item</span>
                        </div>

                        {{-- Area Item Keranjang --}}
                        <div id="cart"
                            class="space-y-3 flex-1 overflow-y-auto pr-2 custom-scrollbar pb-4 min-h-[200px]">
                            <div class="h-full flex flex-col items-center justify-center text-gray-400">
                                <svg class="w-12 h-12 mb-2 opacity-50" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                                <p class="font-medium text-sm">Keranjang masih kosong</p>
                            </div>
                        </div>

                        {{-- Area Pembayaran --}}
                        <div class="border-t border-gray-100 mt-4 pt-4 shrink-0">

                            <div class="mb-4">
                                <label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Metode
                                    Pembayaran</label>
                                <select name="payment_method" id="payment_method" onchange="toggleCashInput()"
                                    class="w-full mt-1.5 bg-gray-50 border-gray-200 rounded-xl p-2.5 text-sm font-bold text-gray-700 focus:ring-primary focus:border-primary transition-colors cursor-pointer">
                                    <option value="" disabled selected>Pilih Metode Pembayaran</option>
                                    <option value="cash">Tunai / Cash</option>
                                    <option value="qris">QRIS (Midtrans)</option>
                                </select>
                            </div>

                            <div id="cash_input_area"
                                class="hidden mb-4 p-3 bg-gray-50 border border-gray-200 rounded-xl space-y-3">
                                <div>
                                    <label class="text-xs font-bold text-gray-700 block mb-1">Uang Diterima (Rp)</label>
                                    <input type="number" name="uang_bayar" id="uang_bayar" oninput="calculateChange()"
                                        class="w-full border-gray-300 rounded-lg p-2 text-sm font-bold text-gray-900 focus:ring-primary focus:border-primary"
                                        placeholder="0">
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="font-semibold text-gray-500">Kembalian:</span>
                                    <span class="font-bold text-emerald-600">Rp <span
                                            id="uang_kembali">0</span></span>
                                </div>
                            </div>

                            <div
                                class="flex justify-between items-end mb-5 bg-primary/5 p-3 rounded-xl border border-primary/10">
                                <span class="text-primary font-bold uppercase text-xs tracking-wider">Total
                                    Tagihan</span>
                                <div class="text-2xl font-black text-primary">
                                    <span class="text-sm mr-1">Rp</span><span id="total">0</span>
                                </div>
                            </div>

                            <input type="hidden" name="with_receipt" id="with_receipt">
                            <div id="items-input"></div>

                            <div class="grid grid-cols-2 gap-3">
                                <button type="button" onclick="checkout(0)"
                                    class="w-full bg-white border-2 border-gray-200 hover:bg-gray-50 hover:border-gray-300 text-gray-700 py-3 rounded-xl font-bold transition-all text-sm">
                                    Bayar Saja
                                </button>
                                <button type="button" onclick="checkout(1)"
                                    class="w-full bg-primary hover:bg-primary-dark text-white py-3 rounded-xl font-bold transition-all shadow-md shadow-primary/20 text-sm flex items-center justify-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            width: 5px;
            height: 5px;
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

        function toggleCashInput() {
            const method = document.getElementById('payment_method').value;
            const cashArea = document.getElementById('cash_input_area');
            if (method === 'cash') {
                cashArea.classList.remove('hidden');
                document.getElementById('uang_bayar').focus();
            } else {
                cashArea.classList.add('hidden');
                document.getElementById('uang_bayar').value = '';
                calculateChange();
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
                    <svg class="w-12 h-12 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <p class="font-medium text-sm">Keranjang masih kosong</p>
                </div>`;
            } else {
                entries.forEach(item => {
                    grandTotal += item.harga * item.qty;
                    count += item.qty;
                    html += `
                        <div class="flex flex-col bg-white border border-gray-100 p-3 rounded-xl shadow-sm">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex-1 pr-2">
                                    <p class="font-bold text-sm text-gray-800 leading-tight">${item.nama}</p>
                                    <p class="text-xs text-primary font-bold mt-0.5">Rp ${item.harga.toLocaleString('id-ID')}</p>
                                </div>
                                <div class="flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-lg p-0.5">
                                    <button type="button" onclick="changeQty(${item.menu_id}, -1)" class="w-6 h-6 flex items-center justify-center bg-white rounded shadow-sm text-gray-600 font-bold hover:text-red-500">-</button>
                                    <span class="text-xs font-bold min-w-[16px] text-center">${item.qty}</span>
                                    <button type="button" onclick="changeQty(${item.menu_id}, 1)" class="w-6 h-6 flex items-center justify-center bg-white rounded shadow-sm text-gray-600 font-bold hover:text-primary">+</button>
                                </div>
                            </div>
                            <input type="text" onchange="updateNote(${item.menu_id}, this.value)" value="${item.catatan}" placeholder="Catatan opsional (cth: Es dipisah)..." class="w-full text-xs bg-gray-50 border-transparent focus:border-gray-300 focus:ring-0 rounded-md p-1.5 text-gray-600 placeholder-gray-400">
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
                alert('Pilih menu terlebih dahulu!');
                return;
            }

            const payment = document.getElementById('payment_method').value;
            if (!payment) {
                alert('Silakan pilih metode pembayaran!');
                return;
            }

            if (payment === 'cash') {
                const bayar = parseInt(document.getElementById('uang_bayar').value) || 0;
                if (bayar < grandTotal) {
                    alert('Uang pembayaran tunai kurang dari total tagihan!');
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
                    alert("Pembayaran QRIS Berhasil!");

                    window.location.href =
                        "{{ url('/kasir/qris-success') }}/{{ session('order_id') }}?with_receipt={{ session('with_receipt') }}";
                },
                onPending: function(result) {
                    alert("Menunggu pembayaran Anda diselesaikan.");
                },
                onError: function(result) {
                    alert("Pembayaran gagal. Silakan coba lagi.");
                },
                onClose: function() {
                    alert('Anda menutup popup tanpa menyelesaikan pembayaran.');
                }
            });
        @endif
    </script>
</x-app-layout>
