<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


use Illuminate\Http\Request;

class KasirController extends Controller
{
    public function index()
    {
        $menus = Menu::all();

        $orders = Order::latest()
            ->take(3)
            ->get();

        return view('kasir.index', compact('menus','orders'));
    }

public function store(Request $request)
{
    $request->validate([
        'items' => 'required|array',
        'payment_method' => 'required|in:cash,qris',
        'with_receipt' => 'required|boolean'
    ]);

    $order = DB::transaction(function () use ($request) {

        $total = collect($request->items)
            ->sum(fn($i) => $i['harga'] * $i['qty']);

        $order = Order::create([
            'user_id' => Auth::id(),
            'total' => $total,
            'payment_method' => $request->payment_method,
            'with_receipt' => $request->with_receipt
        ]);

        foreach ($request->items as $item) {

            OrderItem::create([
                'order_id' => $order->id,
                'menu_id' => $item['menu_id'],
                'harga' => $item['harga'],
                'qty' => $item['qty'],
                'subtotal' => $item['harga'] * $item['qty']
            ]);

        }

        return $order;
    });


    // jika pilih dengan struk
    if ($request->with_receipt) {

        return redirect()
            ->route('kasir.struk', $order->id);
    }

    // jika tanpa struk
    return redirect()
        ->route('kasir.index')
        ->with('success', 'Transaksi berhasil tanpa struk');
}
public function riwayat()
{
    $orders = Order::with('items.menu','user')
        ->latest()
        ->get();

    return view('kasir.riwayat', compact('orders'));
}
public function struk($id)
{
    $order = Order::with('items.menu','user')->findOrFail($id);

    return view('kasir.struk', compact('order'));
}
}