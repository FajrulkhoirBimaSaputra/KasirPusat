<x-app-layout>
    <div class="p-4">
        <h1 class="text-2xl font-bold mb-4">Riwayat Transaksi</h1>
        <div class="bg-white rounded-lg shadow p-4">
            @if($orders->isEmpty())
                <p class="text-gray-500">Belum ada transaksi.</p>
            @else
                <table class="w-full text-left">
                    <thead>
                        <tr>
                            <th class="border-b p-2">ID Order</th>
                            <th class="border-b p-2">Total</th>
                            <th class="border-b p-2">Metode Pembayaran</th>
                            <th class="border-b p-2">Struk</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td class="border-b p-2">{{ $order->id }}</td>
                                <td class="border-b p-2">Rp {{ number_format($order->total) }}</td>
                                <td class="border-b p-2">{{ ucfirst($order->payment_method) }}</td>
                                <td class="border-b p-2">
                                    @if($order->with_receipt)
                                        <a href="{{ route('kasir.struk', $order) }}" class="text-blue-500 hover:underline">
                                            Lihat Struk
                                        </a>
                                    @else
                                        Tidak Ada Struk
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</x-app-layout>

