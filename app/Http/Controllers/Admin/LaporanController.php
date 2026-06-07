<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\OrderItem;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanBulananExport;

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
            // 2. Ambil data produk dan kelompokkan per HARI (Tanggal)
            $items = OrderItem::with('menu')
                ->whereHas('order', function ($q) use ($m, $tahunTerpilih) {
                    $q->whereMonth('created_at', $m)
                        ->whereYear('created_at', $tahunTerpilih)
                        ->where('payment_status', 'paid');
                })
                ->select(
                    DB::raw('DATE(created_at) as tanggal'),
                    'menu_id',
                    DB::raw('SUM(qty) as total_qty'),
                    DB::raw('SUM(subtotal) as total_pendapatan')
                )
                ->groupBy('tanggal', 'menu_id')
                ->orderByDesc('tanggal') // Urutkan dari tanggal terbaru ke terlama
                ->orderByDesc('total_qty') // Lalu urutkan berdasarkan yang paling laris
                ->get();

            // Format data agar menjadi array bersarang: ['01 Januari 2026' => [ item1, item2 ]]
            $formattedItems = [];
            foreach ($items as $item) {
                $tglIndo = Carbon::parse($item->tanggal)->translatedFormat('d F Y');

                if (!isset($formattedItems[$tglIndo])) {
                    $formattedItems[$tglIndo] = [];
                }

                $formattedItems[$tglIndo][] = [
                    'nama' => $item->menu->nama ?? 'Produk Terhapus',
                    'qty' => $item->total_qty,
                    'total' => $item->total_pendapatan
                ];
            }

            $produkPerBulan[$namaBulan] = $formattedItems;
        }

        return view('admin.laporan.bulanan', compact(
            'laporanBulanan',
            'totalTahunan',
            'tahunTerpilih',
            'daftarTahun',
            'produkPerBulan'
        ));
    }

    public function exportExcel(Request $request)
    {
        // Validasi input range tanggal
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        $start = $request->query('tanggal_mulai');
        $end = $request->query('tanggal_selesai');

        // Penamaan file dinamis berdasarkan range tanggal
        $namaFile = "Laporan_Penjualan_{$start}_to_{$end}.xlsx";

        return Excel::download(new LaporanBulananExport($start, $end), $namaFile);
    }
}
