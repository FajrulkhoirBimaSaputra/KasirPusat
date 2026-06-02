<?php

namespace App\Http\Controllers\Admin; // Tambahkan \Admin di sini

use App\Http\Controllers\Controller; // Wajib diimport karena berada di sub-folder
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
        $daftarTahun = range(Carbon::now()->year, Carbon::now()->year - 5);

        $laporanBulanan = [];
        $totalTahunan = 0;
        $produkPerBulan = []; // Untuk menyimpan data produk per bulan

        for ($m = 1; $m <= 12; $m++) {
            $date = Carbon::create($tahunTerpilih, $m, 1);
            $namaBulan = $date->translatedFormat('F');

            $data = Order::whereMonth('created_at', $m)
                ->whereYear('created_at', $tahunTerpilih)
                ->select(
                    DB::raw('SUM(total) as pendapatan'),
                    DB::raw('COUNT(id) as transaksi')
                )->first();

            $pendapatan = $data->pendapatan ?? 0;
            $totalTahunan += $pendapatan;

            $laporanBulanan[] = [
                'bulan' => $namaBulan,
                'pendapatan' => $pendapatan,
                'transaksi' => $data->transaksi ?? 0
            ];

            // Ambil top produk untuk bulan ini saja
            $produkPerBulan[$namaBulan] = OrderItem::with('menu')
                ->whereHas('order', function ($q) use ($m, $tahunTerpilih) {
                    $q->whereMonth('created_at', $m)->whereYear('created_at', $tahunTerpilih);
                })
                ->select('menu_id', DB::raw('SUM(qty) as total_qty'), DB::raw('SUM(subtotal) as total_pendapatan'))
                ->groupBy('menu_id')
                ->orderByDesc('total_qty')
                ->take(5)
                ->get()
                ->map(function ($item) {
                    return [
                        'nama' => $item->menu->nama ?? 'Terhapus',
                        'qty' => $item->total_qty,
                        'total' => number_format($item->total_pendapatan, 0, ',', '.')
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