<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil Parameter Filter
        $period = $request->query('period', 'daily');
        $tanggalProduk = $request->query('tanggal', Carbon::today()->toDateString());

        // 2. Tentukan Rentang Waktu & Logic Berdasarkan Periode
        $labels = [];
        $dataPenjualan = [];
        $countDay = 1;

        if ($period == 'monthly') {
            // --- LOGIKA BULANAN (12 Bulan Terakhir) ---
            $startOfCurrentPeriod = Carbon::now()->subMonths(11)->startOfMonth();
            $startOfPreviousPeriod = Carbon::now()->subMonths(23)->startOfMonth();
            $endOfPreviousPeriod = Carbon::now()->subMonths(12)->endOfMonth();
            $countDay = 12;

            $rawData = Order::select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as date"),
                DB::raw('SUM(total) as total_sales')
            )
                ->where('created_at', '>=', $startOfCurrentPeriod)
                ->groupBy('date')
                ->pluck('total_sales', 'date');

            for ($i = 11; $i >= 0; $i--) {
                $m = Carbon::now()->subMonths($i);
                $key = $m->format('Y-m');
                $labels[] = $m->translatedFormat('M Y');
                $dataPenjualan[] = $rawData[$key] ?? 0;
            }

        } elseif ($period == 'yearly') {
            // --- LOGIKA TAHUNAN (5 Tahun Terakhir) ---
            $startOfCurrentPeriod = Carbon::now()->subYears(4)->startOfYear();
            $startOfPreviousPeriod = Carbon::now()->subYears(9)->startOfYear(); // Contoh 5 thn sebelumnya
            $endOfPreviousPeriod = Carbon::now()->subYears(5)->endOfYear();
            $countDay = 5;

            $rawData = Order::select(
                DB::raw("YEAR(created_at) as date"),
                DB::raw('SUM(total) as total_sales')
            )
                ->where('created_at', '>=', $startOfCurrentPeriod)
                ->groupBy('date')
                ->pluck('total_sales', 'date');

            for ($i = 4; $i >= 0; $i--) {
                $y = Carbon::now()->subYears($i)->format('Y');
                $labels[] = $y;
                $dataPenjualan[] = $rawData[$y] ?? 0;
            }

        } else {
            // --- LOGIKA HARIAN (30 Hari Terakhir - Default) ---
            $startOfCurrentPeriod = Carbon::now()->subDays(29)->startOfDay();
            $startOfPreviousPeriod = Carbon::now()->subDays(59)->startOfDay();
            $endOfPreviousPeriod = Carbon::now()->subDays(30)->endOfDay();
            $countDay = 30;

            $rawData = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as total_sales')
            )
                ->where('created_at', '>=', $startOfCurrentPeriod)
                ->groupBy('date')
                ->pluck('total_sales', 'date');

            for ($i = 29; $i >= 0; $i--) {
                $d = Carbon::now()->subDays($i)->format('Y-m-d');
                $labels[] = Carbon::parse($d)->translatedFormat('d M');
                $dataPenjualan[] = $rawData[$d] ?? 0;
            }
        }

        // 3. Ambil Metrik Utama Periode SEKARANG
        $currentMetrics = Order::where('created_at', '>=', $startOfCurrentPeriod)
            ->select(
                DB::raw('SUM(total) as total_sales'),
                DB::raw('COUNT(id) as total_count')
            )->first();

        // 4. Ambil Metrik Periode SEBELUMNYA untuk Perbandingan
        $previousMetrics = Order::whereBetween('created_at', [$startOfPreviousPeriod, $endOfPreviousPeriod])
            ->select(
                DB::raw('SUM(total) as total_sales'),
                DB::raw('COUNT(id) as total_count')
            )->first();

        // Hitung variabel final
        $totalSemuaPenjualan = $currentMetrics->total_sales ?? 0;
        $totalTransaksi = $currentMetrics->total_count ?? 0;
        $rataRataHarian = $totalSemuaPenjualan / $countDay;

        $prevTotal = $previousMetrics->total_sales ?? 0;
        $prevCount = $previousMetrics->total_count ?? 0;

        $nominalDiffPenjualan = $totalSemuaPenjualan - $prevTotal;
        $nominalDiffTransaksi = $totalTransaksi - $prevCount;

        $diffPenjualanPersen = $this->calculatePercentage($totalSemuaPenjualan, $prevTotal);
        $diffTransaksiPersen = $this->calculatePercentage($totalTransaksi, $prevCount);

        // 5. Bagian Produk Terjual (Filter Tanggal Spesifik)
        $produkTerjual = OrderItem::with('menu')
            ->whereHas('order', function ($query) use ($tanggalProduk) {
                $query->whereDate('created_at', $tanggalProduk);
            })
            ->select('menu_id', DB::raw('SUM(qty) as total_qty'), DB::raw('SUM(subtotal) as total_pendapatan'))
            ->groupBy('menu_id')
            ->orderByDesc('total_qty')
            ->get();

        return view('dashboard', compact(
            'labels',
            'dataPenjualan',
            'totalSemuaPenjualan',
            'rataRataHarian',
            'totalTransaksi',
            'tanggalProduk',
            'produkTerjual',
            'nominalDiffPenjualan',
            'nominalDiffTransaksi',
            'diffPenjualanPersen',
            'diffTransaksiPersen'
        ));
    }

    private function calculatePercentage($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        return (($current - $previous) / $previous) * 100;
    }
}