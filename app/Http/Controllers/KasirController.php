<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Kas;
use App\Models\Ingredient;
use App\Models\IngredientHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class KasirController extends Controller
{
    /**
     * Menampilkan halaman utama Kasir (POS)
     */
    public function index()
    {
        // 1. CEK STATUS SHIFT (Wajib Buka Shift)
        $shiftStatus = Kas::where('user_id', Auth::id())
            ->whereDate('created_at', now()->toDateString())
            ->whereIn('keterangan', ['Modal Awal Shift', 'Tutup Shift'])
            ->latest()
            ->first();

        // Jika belum buka shift, tendang/redirect ke halaman Manajemen Kas
        if (!$shiftStatus || $shiftStatus->keterangan === 'Tutup Shift') {
            return redirect()->route('kasir.manajemen-kas')
                ->with('error', 'Akses ditolak! Anda harus menyetorkan Modal Awal untuk membuka shift hari ini sebelum bisa memulai transaksi.');
        }

        // 2. JIKA SHIFT SUDAH DIBUKA, LANJUTKAN MUAT HALAMAN POS
        $menus = Menu::all();

        // Mengambil daftar kategori unik dari kolom 'jenis' di tabel menus untuk tab filter
        $categories = Menu::select('jenis')->distinct()->pluck('jenis');

        // Mengambil 3 order terakhir milik kasir yang sedang login
        $orders = Order::where('user_id', Auth::id())
            ->latest()
            ->take(3)
            ->get();

        return view('kasir.index', compact('menus', 'categories', 'orders'));
    }

    /**
     * Memproses transaksi dari keranjang belanja
     */
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'payment_method' => 'required|in:cash,qris',
            'with_receipt' => 'required|boolean',
            'uang_bayar' => 'nullable|numeric|min:0',
        ]);

        $order = DB::transaction(function () use ($request) {

            $total = collect($request->items)->sum(fn($i) => $i['harga'] * $i['qty']);

            // Kalkulasi kembalian jika cash
            $uangBayar = $request->payment_method === 'cash' ? $request->uang_bayar : null;
            $uangKembali = $request->payment_method === 'cash' ? ($uangBayar - $total) : null;

            $order = Order::create([
                'user_id' => Auth::id(),
                'total' => $total,
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_method === 'qris' ? 'pending' : 'paid',
                'uang_bayar' => $uangBayar,
                'uang_kembali' => $uangKembali,
                'with_receipt' => $request->with_receipt
            ]);

            foreach ($request->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id' => $item['menu_id'],
                    'harga' => $item['harga'],
                    'qty' => $item['qty'],
                    'subtotal' => $item['harga'] * $item['qty'],
                    'catatan' => $item['catatan'] ?? null // Menyimpan catatan per item
                ]);
            }

            // --- INTEGRASI MIDTRANS ---
            if ($request->payment_method === 'qris') {
                \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
                \Midtrans\Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
                \Midtrans\Config::$isSanitized = true;
                \Midtrans\Config::$is3ds = true;

                $params = [
                    'transaction_details' => [
                        'order_id' => 'ORD-' . $order->id . '-' . time(),
                        'gross_amount' => $total,
                    ],
                    'customer_details' => [
                        'first_name' => Auth::user()->name,
                    ],
                ];

                $snapToken = \Midtrans\Snap::getSnapToken($params);
                $order->update(['snap_token' => $snapToken]);
            }

            return $order;
        });

        // Trigger Midtrans Popup jika QRIS
        if ($order->payment_method === 'qris') {
            return back()->with([
                'snap_token' => $order->snap_token,
                'order_id' => $order->id,
                'with_receipt' => $request->with_receipt
            ]);
        }

        // Trigger cetak struk otomatis (tanpa pindah tab) jika diminta
        if ($request->with_receipt) {
            return back()->with([
                'success' => 'Transaksi tunai berhasil!',
                'print_struk_url' => route('kasir.struk', $order->id)
            ]);
        }

        return back()->with('success', 'Transaksi berhasil tanpa struk');
    }

    /**
     * Menampilkan riwayat transaksi kasir
     */
    public function riwayat(Request $request)
    {
        // 1. CEK STATUS SHIFT (Wajib Buka Shift)
        $shiftStatus = Kas::where('user_id', Auth::id())
            ->whereDate('created_at', now()->toDateString())
            ->whereIn('keterangan', ['Modal Awal Shift', 'Tutup Shift'])
            ->latest()
            ->first();

        // Jika belum buka shift, tendang/redirect ke halaman Manajemen Kas
        if (!$shiftStatus || $shiftStatus->keterangan === 'Tutup Shift') {
            return redirect()->route('kasir.manajemen-kas')
                ->with('error', 'Akses ditolak! Anda harus menyetorkan Modal Awal untuk membuka shift hari ini sebelum bisa mengakses riwayat.');
        }

        // 2. JIKA SHIFT SUDAH DIBUKA, JALANKAN LOGIKA NORMAL
        $tanggal = $request->input('tanggal', \Carbon\Carbon::today()->toDateString());

        $orders = Order::with('items.menu', 'user')
            ->where('user_id', Auth::id())
            ->whereDate('created_at', $tanggal)
            ->latest()
            ->get();

        return view('kasir.riwayat', compact('orders', 'tanggal'));
    }

    /**
     * Menampilkan view untuk struk/nota
     */
    public function struk($id)
    {
        $order = Order::with('items.menu', 'user')->findOrFail($id);

        return view('kasir.struk', compact('order'));
    }

    /**
     * Menampilkan ringkasan pendapatan & shift
     */
    public function ringkasan()
    {
        // 1. Cek apakah kasir sudah buka shift hari ini
        $shiftStatus = Kas::where('user_id', Auth::id())
            ->whereDate('created_at', now()->toDateString())
            ->whereIn('keterangan', ['Modal Awal Shift', 'Tutup Shift'])
            ->latest()
            ->first();

        // Jika belum ada modal awal, paksa kembali ke halaman manajemen kas
        if (!$shiftStatus || $shiftStatus->keterangan === 'Tutup Shift') {
            return redirect()->route('kasir.manajemen-kas')
                ->with('error', 'Akses ditolak! Anda harus menyetorkan Modal Awal untuk membuka shift.');
        }

        $modalAwal = $shiftStatus->nominal;

        $waktuBukaShift = $shiftStatus->created_at;

        // 2. Ambil data order lunas hari ini
        $ordersToday = Order::where('user_id', Auth::id())
            ->whereDate('created_at', now()->toDateString())
            ->where('payment_status', 'paid')
            ->get();

        $totalTransaksiHariIni = $ordersToday->count();
        $penjualanKotor = $ordersToday->sum('total');

        $tunai = $ordersToday->where('payment_method', 'cash')->sum('total');
        $qris = $ordersToday->where('payment_method', 'qris')->sum('total');

        // 3. Ambil data Kas
        $pemasukan = Kas::where('user_id', Auth::id())
            ->whereDate('created_at', now()->toDateString())
            ->where('jenis', 'pemasukan')
            ->where('keterangan', '!=', 'Modal Awal Shift') // Jangan di-double dengan modal
            ->sum('nominal');

        $pengeluaran = Kas::where('user_id', Auth::id())
            ->whereDate('created_at', now()->toDateString())
            ->where('jenis', 'pengeluaran')
            ->sum('nominal');

        $uangDikembalikan = Kas::where('user_id', Auth::id())
            ->whereDate('created_at', now()->toDateString())
            ->where('jenis', 'refund')
            ->sum('nominal');

        // 4. Kalkulasi Akhir
        $penjualanBersih = $penjualanKotor - $uangDikembalikan;
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
            'waktuBukaShift',
            'pemasukan',
            'pengeluaran',
            'jumlahTunaiDiharapkan'
        ));
    }

    /**
     * Menampilkan form manajemen kas (Uang masuk/keluar)
     */
    public function manajemenKas()
    {
        $kasHariIni = Kas::where('user_id', Auth::id())
            ->whereDate('created_at', now()->toDateString())
            ->latest()
            ->get();

        return view('kasir.manajemen-kas', compact('kasHariIni'));
    }

    /**
     * Menyimpan data kas (Uang masuk/keluar/refund)
     */
    public function storeKas(Request $request)
    {
        // 1. Tambahkan 'refund' ke dalam aturan validasi 'in:...'
        $request->validate([
            'jenis' => 'required|in:pemasukan,pengeluaran,refund',
            'nominal' => 'required|numeric|min:1',
            'keterangan' => 'required|string|max:255'
        ]);

        // 2. Simpan data kas seperti biasa
        Kas::create([
            'user_id' => Auth::id(),
            'jenis' => $request->jenis,
            'nominal' => $request->nominal,
            'keterangan' => $request->keterangan
        ]);

        return back()->with('success', 'Data kas berhasil dicatat!');
    }

    public function tutupShift(Request $request)
    {
        // Catat "Tutup Shift" sebagai penanda di tabel Kas
        Kas::create([
            'user_id' => Auth::id(),
            'jenis' => 'pengeluaran',
            'nominal' => $request->expected_cash ?? 0,
            'keterangan' => 'Tutup Shift'
        ]);

        return redirect()->route('kasir.manajemen-kas')->with('success', 'Shift berhasil ditutup dan laporan telah dicetak! Silakan buka shift baru jika ingin melihat menu.');
    }
    /**
     * Menampilkan daftar stok bahan baku khusus akses kasir
     */
    public function stok()
    {
        $ingredients = Ingredient::all();
        return view('kasir.stok', compact('ingredients'));
    }

    /**
     * Memperbarui data stok bahan baku
     */
    /**
     * Memperbarui data stok bahan baku (Hanya boleh mengurangi)
     */
    public function updateStok(Request $request, Ingredient $ingredient)
    {
        // 1. Validasi: Nilai maksimal (max) adalah jumlah stok saat ini
        $request->validate([
            'stok' => 'required|integer|min:0|max:' . $ingredient->stok
        ], [
            'stok.max' => 'Akses ditolak! Kasir hanya diizinkan untuk mengurangi stok (melaporkan pemakaian).'
        ]);

        $oldStok = $ingredient->stok;
        $newStok = $request->stok;
        $difference = $newStok - $oldStok;

        // 2. Jika tidak ada perubahan angka (Kasir iseng tekan simpan tanpa ubah angka), abaikan saja
        if ($difference == 0) {
            return back();
        }

        // 3. Update stok master di tabel ingredients
        $ingredient->update([
            'stok' => $newStok,
            'last_updated_by' => Auth::id()
        ]);

        // 4. Catat jejak pengurangannya di tabel riwayat agar admin tahu!
        IngredientHistory::create([
            'ingredient_id' => $ingredient->id,
            'user_id' => Auth::id(),
            'old_stok' => $oldStok,
            'new_stok' => $newStok,
            'difference' => $difference, // Akan bernilai minus (contoh: -4)
        ]);

        return back()->with('success', 'Laporan pemakaian tercatat! Sisa stok ' . $ingredient->nama . ' sekarang ' . $newStok);
    }

    /**
     * Trik Cepat Update Status via Frontend (Tanpa Webhook)
     */
    public function qrisSuccess(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        // Langsung ubah status jadi lunas
        $order->update(['payment_status' => 'paid']);

        // Jika kasir minta cetak struk
        if ($request->with_receipt == 1) {
            return redirect()->route('kasir.index')->with([
                'success' => 'Pembayaran QRIS Lunas!',
                'print_struk_url' => route('kasir.struk', $order->id)
            ]);
        }

        return redirect()->route('kasir.index')->with('success', 'Pembayaran QRIS Lunas!');
    }
}
