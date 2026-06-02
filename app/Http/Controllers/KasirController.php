<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Kas;
use App\Models\Ingredient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class KasirController extends Controller
{
    public function index()
    {
        $menus = Menu::all();

        // Filter 3 transaksi terakhir khusus untuk kasir yang login
        $orders = Order::where('user_id', Auth::id())
            ->latest()
            ->take(3)
            ->get();

        return view('kasir.index', compact('menus', 'orders'));
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

        if ($request->with_receipt) {
            return redirect()->route('kasir.struk', $order->id);
        }

        return redirect()
            ->route('kasir.index')
            ->with('success', 'Transaksi berhasil tanpa struk');
    }

    public function riwayat(Request $request) // [PERBAIKAN]: Tambahkan Request
    {
        // [PERBAIKAN]: Pindahkan variabel tanggal ke atas dan fungsikan filter tanggalnya
        $tanggal = $request->input('tanggal', Carbon::today()->toDateString());

        $orders = Order::with('items.menu', 'user')
            ->where('user_id', Auth::id())
            ->whereDate('created_at', $tanggal) // Filter berdasarkan tanggal yang dipilih
            ->latest()
            ->get();

        return view('kasir.riwayat', compact('orders', 'tanggal'));
    }

    public function struk($id)
    {
        $order = Order::with('items.menu', 'user')->findOrFail($id);

        return view('kasir.struk', compact('order'));
    }

    public function ringkasan()
    {
        $ordersToday = Order::where('user_id', Auth::id())
            ->whereDate('created_at', now()->toDateString())
            ->get();

        $totalTransaksiHariIni = $ordersToday->count();

        $penjualanKotor = $ordersToday->sum('total');
        $penjualanBersih = $penjualanKotor;

        $tunai = $ordersToday->where('payment_method', 'cash')->sum('total');
        $qris = $ordersToday->where('payment_method', 'qris')->sum('total');

        // [PERBAIKAN]: Menghitung pemasukan dan pengeluaran dinamis dari tabel Kas
        $pemasukan = Kas::where('user_id', Auth::id())
            ->whereDate('created_at', now()->toDateString())
            ->where('jenis', 'pemasukan')->sum('nominal');

        $pengeluaran = Kas::where('user_id', Auth::id())
            ->whereDate('created_at', now()->toDateString())
            ->where('jenis', 'pengeluaran')->sum('nominal');

        $modalAwal = 0;
        $uangDikembalikan = 0;

        // Rumus Uang di Laci
        $jumlahTunaiDiharapkan = $modalAwal + $tunai + $pemasukan - $pengeluaran - $uangDikembalikan;

        return view('kasir.ringkasan', compact(
            'ordersToday',
            'totalTransaksiHariIni',
            'penjualanKotor',
            'penjualanBersih',
            'tunai',
            'qris',
            'modalAwal',
            'uangDikembalikan',
            'pemasukan',
            'pengeluaran',
            'jumlahTunaiDiharapkan'
        ));
    }

    public function manajemenKas()
    {
        $kasHariIni = Kas::where('user_id', Auth::id())
            ->whereDate('created_at', now()->toDateString())
            ->latest()
            ->get();

        return view('kasir.manajemen-kas', compact('kasHariIni'));
    }

    public function storeKas(Request $request)
    {
        $request->validate([
            'jenis' => 'required|in:pemasukan,pengeluaran',
            'nominal' => 'required|numeric|min:1',
            'keterangan' => 'required|string|max:255'
        ]);

        Kas::create([
            'user_id' => Auth::id(),
            'jenis' => $request->jenis,
            'nominal' => $request->nominal,
            'keterangan' => $request->keterangan
        ]);

        return back()->with('success', 'Data kas berhasil dicatat!');
    }

    public function stok()
    {
        $ingredients = Ingredient::all();
        return view('kasir.stok', compact('ingredients'));
    }

    public function updateStok(Request $request, Ingredient $ingredient)
    {
        // 1. Validasi input
        $request->validate([
            'stok' => 'required|numeric|min:0'
        ]);

        // 2. Update stok sekaligus catat user yang sedang login
        $ingredient->update([
            'stok' => $request->stok,
            'last_updated_by' => Auth::id() // Menyimpan ID Kasir yang melakukan update
        ]);

        // 3. Berikan feedback yang informatif
        return back()->with('success', 'Stok ' . $ingredient->nama . ' berhasil diperbarui menjadi ' . $request->stok);
    }
}