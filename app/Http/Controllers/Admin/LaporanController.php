<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\OrderItem;

class LaporanController extends Controller
{
    public function bulanan(Request $request)
    {
        $tahunTerpilih = $request->query('tahun', Carbon::now()->year);
        // Buat daftar tahun dinamis (dari tahun pertama ada order sampai sekarang)
        $tahunTerkecil = Order::min(DB::raw('YEAR(created_at)')) ?? Carbon::now()->year;
        $daftarTahun = range(Carbon::now()->year, $tahunTerkecil);

        $laporanBulanan = [];
        $totalTahunan = 0;
        $produkPerBulan = [];

        for ($m = 1; $m <= 12; $m++) {
            $date = Carbon::create($tahunTerpilih, $m, 1);
            $namaBulan = $date->translatedFormat('F');

            // 1. Ambil data agregat pesanan per bulan
            $data = Order::whereMonth('created_at', $m)
                ->whereYear('created_at', $tahunTerpilih)
                ->where('payment_status', 'paid') // Hanya hitung yang lunas
                ->select(
                    DB::raw('SUM(total) as pendapatan'),
                    DB::raw('COUNT(id) as transaksi'),
                    DB::raw('SUM(CASE WHEN payment_method = "cash" THEN total ELSE 0 END) as total_cash'),
                    DB::raw('SUM(CASE WHEN payment_method = "qris" THEN total ELSE 0 END) as total_qris')
                )->first();

            $pendapatan = $data->pendapatan ?? 0;
            $transaksi = $data->transaksi ?? 0;
            $totalTahunan += $pendapatan;

            // Hitung rata-rata per transaksi (Basket Size)
            $rataRata = $transaksi > 0 ? ($pendapatan / $transaksi) : 0;

            $laporanBulanan[] = [
                'bulan' => $namaBulan,
                'pendapatan' => $pendapatan,
                'transaksi' => $transaksi,
                'total_cash' => $data->total_cash ?? 0,
                'total_qris' => $data->total_qris ?? 0,
                'rata_rata' => $rataRata
            ];

            // 2. Ambil data SEMUA produk (tanpa limit 5) agar lebih transparan
            $produkPerBulan[$namaBulan] = OrderItem::with('menu')
                ->whereHas('order', function ($q) use ($m, $tahunTerpilih) {
                    $q->whereMonth('created_at', $m)
                        ->whereYear('created_at', $tahunTerpilih)
                        ->where('payment_status', 'paid');
                })
                ->select('menu_id', DB::raw('SUM(qty) as total_qty'), DB::raw('SUM(subtotal) as total_pendapatan'))
                ->groupBy('menu_id')
                ->orderByDesc('total_qty')
                ->get()
                ->map(function ($item) {
                    return [
                        'nama' => $item->menu->nama ?? 'Produk Terhapus',
                        'qty' => $item->total_qty,
                        'total' => $item->total_pendapatan // Kirim angka mentah, diformat di Alpine JS
                    ];
                });
        }

        return view('admin.laporan.bulanan', compact(
            'laporanBulanan',
            'totalTahunan',
            'tahunTerpilih',
            'daftarTahun',
            'produkPerBulan'
        ));
    }
}
