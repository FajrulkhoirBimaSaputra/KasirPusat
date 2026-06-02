<x-app-layout>
    <div class="max-w-7xl mx-auto p-2 sm:p-4 md:p-6">
        <form method="POST" action="{{ route('kasir.store') }}" id="kasirForm">
            @csrf

            <div class="flex flex-col lg:flex-row gap-6">

                {{-- MENU SECTION --}}
                <div class="w-full lg:w-2/3">
                    <div class="flex items-center justify-between mb-4 px-2">
                        <h2 class="text-xl font-bold text-gray-800">Pilih Menu</h2>
                        <span class="text-sm text-gray-500">{{ count($menus) }} Produk Tersedia</span>
                    </div>
                    
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
                        @foreach($menus as $menu)
                            <div onclick="addItem({{ $menu->id }}, '{{ $menu->nama }}', {{ $menu->harga }})"
                                class="bg-white border border-gray-200 rounded-2xl p-2 sm:p-3 text-center cursor-pointer 
                                       hover:border-blue-500 hover:shadow-md transition-all duration-200 active:scale-95 group">
                                
                                {{-- FRAME GAMBAR --}}
                                <div class="w-full aspect-square bg-gray-100 rounded-xl mb-3 flex items-center justify-center overflow-hidden border border-gray-50">
                                    @if($menu->image_path)
                                        <img src="{{ asset('storage/' . $menu->image_path) }}" 
                                             alt="{{ $menu->nama }}" 
                                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                    @else
                                        {{-- Fallback jika gambar tidak ada --}}
                                        <div class="flex flex-col items-center">
                                            <span class="text-gray-300 font-bold text-3xl">{{ substr($menu->nama, 0, 1) }}</span>
                                            <span class="text-[10px] text-gray-400 mt-1">No Image</span>
                                        </div>
                                    @endif
                                </div>

                                <h3 class="font-semibold text-gray-800 text-sm sm:text-base leading-tight h-10 flex items-center justify-center px-1">
                                    {{ $menu->nama }}
                                </h3>

                                <p class="text-blue-600 font-bold mt-2 text-sm sm:text-base">
                                    Rp {{ number_format($menu->harga, 0, ',', '.') }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- CART SECTION --}}
                <div class="w-full lg:w-1/3">
                    <div class="bg-white border rounded-2xl p-5 shadow-lg lg:sticky lg:top-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-bold text-lg text-gray-800">Rincian Order</h3>
                            <span class="bg-gray-100 text-gray-600 text-xs py-1 px-2 rounded-lg" id="item-count">0 Items</span>
                        </div>

                        <div id="cart" class="space-y-3 max-h-[40vh] lg:max-h-[50vh] overflow-y-auto pr-2 custom-scrollbar">
                            <p class="text-gray-400 text-center py-8">Keranjang masih kosong</p>
                        </div>

                        <div class="border-t mt-6 pt-4">
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Metode Pembayaran</label>
                            <select name="payment_method" class="w-full mt-1 bg-gray-50 border-gray-200 rounded-xl p-3 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Pilih Metode</option>
                                <option value="cash">Cash</option>
                                <option value="qris">QRIS</option>
                            </select>

                            <div class="mt-6 flex justify-between items-end">
                                <span class="text-gray-600 uppercase text-xs font-bold">Total Tagihan</span>
                                <div class="text-2xl font-black text-blue-600">
                                    <span class="text-sm">Rp</span> <span id="total">0</span>
                                </div>
                            </div>

                            <input type="hidden" name="with_receipt" id="with_receipt">

                            <div class="grid grid-cols-1 gap-3 mt-6">
                                <button type="button" onclick="checkout(1)"
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white py-4 rounded-xl font-bold transition-colors flex items-center justify-center gap-2 shadow-lg shadow-blue-200">
                                    <span>Cetak Struk & Bayar</span>
                                </button>
                                
                                <button type="button" onclick="checkout(0)"
                                    class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 py-3 rounded-xl font-semibold transition-colors">
                                    Bayar Tanpa Struk
                                </button>
                            </div>
                        </div>

                        {{-- RIWAYAT ORDER (Mini Version) --}}
                        <div class="mt-8 border-t pt-6">
                            <h3 class="font-bold mb-4 text-gray-800">Riwayat Terakhir</h3>
                            <div class="space-y-3">
                                @forelse($orders->take(3) as $order)
                                    <div class="flex items-center justify-between p-3 rounded-xl {{ $order->with_receipt ? 'bg-green-50' : 'bg-yellow-50' }}">
                                        <div>
                                            <p class="text-xs text-gray-500">#{{ $order->id }} • {{ $order->created_at->format('H:i') }}</p>
                                            <p class="font-bold text-sm">Rp {{ number_format($order->total) }}</p>
                                        </div>
                                        <span class="text-[10px] font-bold uppercase px-2 py-1 rounded bg-white shadow-sm">
                                            {{ $order->with_receipt ? 'Struk' : 'No-Struk' }}
                                        </span>
                                    </div>
                                @empty
                                    <p class="text-gray-400 text-center text-sm italic">Belum ada transaksi</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="items-input"></div>
        </form>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #ddd; border-radius: 10px; }
    </style>

    <script>
        let cart = {};

        function addItem(id, nama, harga) {
            if (!cart[id]) {
                cart[id] = { menu_id: id, nama: nama, harga: harga, qty: 1 };
            } else {
                cart[id].qty++;
            }
            renderCart();
        }

        function changeQty(id, diff) {
            cart[id].qty += diff;
            if (cart[id].qty <= 0) delete cart[id];
            renderCart();
        }

        function renderCart() {
            let html = '';
            let total = 0;
            let count = 0;
            const entries = Object.values(cart);

            if (entries.length === 0) {
                html = '<p class="text-gray-400 text-center py-8 font-medium">Keranjang masih kosong</p>';
            } else {
                entries.forEach(item => {
                    total += item.harga * item.qty;
                    count += item.qty;
                    html += `
                        <div class="flex justify-between items-center bg-gray-50 p-3 rounded-xl">
                            <div class="flex-1">
                                <p class="font-semibold text-sm text-gray-800">${item.nama}</p>
                                <p class="text-xs text-blue-500 font-medium"> ${item.harga.toLocaleString('id-ID')}</p>
                            </div>
                            <div class="flex items-center gap-3 bg-white border rounded-lg p-1">
                                <button type="button" onclick="changeQty(${item.menu_id}, -1)" class="w-7 h-7 flex items-center justify-center hover:bg-gray-100 rounded text-gray-600 font-bold">-</button>
                                <span class="text-sm font-bold min-w-[20px] text-center">${item.qty}</span>
                                <button type="button" onclick="changeQty(${item.menu_id}, 1)" class="w-7 h-7 flex items-center justify-center hover:bg-gray-100 rounded text-gray-600 font-bold">+</button>
                            </div>
                        </div>
                    `;
                });
            }

            document.getElementById('cart').innerHTML = html;
            document.getElementById('total').innerText = total.toLocaleString('id-ID');
            document.getElementById('item-count').innerText = `${count} Items`;
        }

        function checkout(receipt) {
            if (Object.keys(cart).length === 0) {
                alert('Pilih menu terlebih dahulu!');
                return;
            }
            const payment = document.querySelector('[name="payment_method"]').value;
            if (!payment) {
                alert('Pilih metode pembayaran!');
                return;
            }

            document.getElementById('with_receipt').value = receipt;
            const itemsInput = document.getElementById('items-input');
            itemsInput.innerHTML = '';

            Object.values(cart).forEach((item, index) => {
                itemsInput.innerHTML += `
                    <input type="hidden" name="items[${index}][menu_id]" value="${item.menu_id}">
                    <input type="hidden" name="items[${index}][harga]" value="${item.harga}">
                    <input type="hidden" name="items[${index}][qty]" value="${item.qty}">
                `;
            });

            document.getElementById('kasirForm').submit();
        }
    </script>
</x-app-layout>