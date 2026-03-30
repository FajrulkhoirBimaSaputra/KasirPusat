<x-app-layout>

    <div class="max-w-7xl mx-auto p-4">

        <form method="POST" action="{{ route('kasir.store') }}" id="kasirForm">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                {{-- MENU --}}
                <div class="md:col-span-2 grid grid-cols-2 sm:grid-cols-3 gap-4">

                    @foreach($menus as $menu)

                        <div onclick="addItem({{ $menu->id }}, '{{ $menu->nama }}', {{ $menu->harga }})"
                            class="border rounded-xl p-4 text-center cursor-pointer hover:bg-blue-50">

                            <h3 class="font-bold">{{ $menu->nama }}</h3>

                            <p class="text-sm text-gray-600">
                                Rp {{ number_format($menu->harga) }}
                            </p>

                        </div>

                    @endforeach

                </div>



                {{-- CART --}}
                <div class="border rounded-xl p-4">

                    <h3 class="font-bold mb-3">Rincian Order</h3>

                    <div id="cart"></div>

                    <select name="payment_method" class="w-full mt-4 border rounded-lg p-2">

                        <option value="">Metode Pembayaran</option>
                        <option value="cash">Cash</option>
                        <option value="qris">QRIS</option>

                    </select>

                    <div class="mt-4 font-bold">
                        Total: Rp <span id="total">0</span>
                    </div>


                    <input type="hidden" name="with_receipt" id="with_receipt">

                    <div class="grid grid-cols-2 gap-2 mt-4">

                        <button type="button" onclick="checkout(0)"
                            class="bg-yellow-500 hover:bg-yellow-600 text-white py-3 rounded-xl font-semibold">
                            Bayar Tanpa Struk
                        </button>

                        <button type="button" onclick="checkout(1)"
                            class="bg-green-600 hover:bg-green-700 text-white py-3 rounded-xl font-semibold">
                            Bayar Dengan Struk
                        </button>

                    </div>



                    {{-- RIWAYAT ORDER --}}
                    <div class="mt-6">

                        <h3 class="font-bold mb-3">Riwayat Order Terbaru</h3>

                        @forelse($orders as $order)

                            <div class="border rounded-lg p-3 mb-2

                                        {{ $order->with_receipt ? 'bg-green-50 border-green-300' : 'bg-yellow-50 border-yellow-300' }}

                                        ">

                                <div class="flex justify-between">

                                    <span class="font-semibold">
                                        Order #{{ $order->id }}
                                    </span>

                                    <span class="text-sm font-bold">

                                        {{ $order->with_receipt ? 'Dengan Struk' : 'Tanpa Struk' }}

                                    </span>

                                </div>

                                <div class="text-sm text-gray-600">

                                    Rp {{ number_format($order->total) }}

                                </div>

                            </div>

                        @empty

                            <p class="text-gray-500 text-sm">
                                Belum ada transaksi
                            </p>

                        @endforelse

                    </div>

                </div>

            </div>



            {{-- HIDDEN INPUT ITEMS --}}
            <div id="items-input"></div>

        </form>

    </div>



    <script>

        let cart = {};

        function addItem(id, nama, harga) {

            if (!cart[id]) {

                cart[id] = {
                    menu_id: id,
                    nama: nama,
                    harga: harga,
                    qty: 1
                };

            } else {

                cart[id].qty++;

            }

            renderCart();

        }


        function changeQty(id, diff) {

            cart[id].qty += diff;

            if (cart[id].qty <= 0) {
                delete cart[id];
            }

            renderCart();

        }



        function renderCart() {

            let html = '';
            let total = 0;

            Object.values(cart).forEach(item => {

                total += item.harga * item.qty;

                html += `

<div class="flex justify-between items-center mb-2">

<span>${item.nama}</span>

<div class="flex items-center gap-2">

<button type="button"
onclick="changeQty(${item.menu_id}, -1)"
class="px-2 border rounded"
>
-
</button>

<span>${item.qty}</span>

<button type="button"
onclick="changeQty(${item.menu_id}, 1)"
class="px-2 border rounded"
>
+
</button>

</div>

</div>

`;

            });

            document.getElementById('cart').innerHTML = html;

            document.getElementById('total').innerText =
                total.toLocaleString();

        }



        function checkout(receipt) {

            if (Object.keys(cart).length === 0) {
                alert('Keranjang kosong');
                return;
            }

            const payment = document.querySelector('[name="payment_method"]').value;

            if (!payment) {
                alert('Pilih metode pembayaran');
                return;
            }

            document.getElementById('with_receipt').value = receipt;

            const itemsInput = document.getElementById('items-input');
            itemsInput.innerHTML = '';

            let index = 0;

            Object.values(cart).forEach(item => {

                itemsInput.innerHTML += `
            <input type="hidden" name="items[${index}][menu_id]" value="${item.menu_id}">
            <input type="hidden" name="items[${index}][harga]" value="${item.harga}">
            <input type="hidden" name="items[${index}][qty]" value="${item.qty}">
        `;

                index++;

            });

            document.getElementById('kasirForm').submit();

        }

    </script>

</x-app-layout>