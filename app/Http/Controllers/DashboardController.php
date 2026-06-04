<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Kas;
use App\Models\Shift; // <--- PENTING: Tambahkan model Shift
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user->role === 'admin';

        // 1. Ambil Parameter Filter
        $period = $request->query('period', 'daily');
        $tanggalProduk = $request->query('tanggal', Carbon::today()->toDateString());

        // 2. Tentukan Rentang Waktu & Logic Berdasarkan Periode
        $labels = [];
        $dataPenjualan = [];
        $countDay = 1;

        // Base query dipisahkan berdasarkan Role (Kasir hanya melihat datanya sendiri)
        $orderQuery = Order::query();
        if (!$isAdmin) {
            $orderQuery->where('user_id', $user->id);
        }

        if ($period == 'monthly') {
            $startOfCurrentPeriod = Carbon::now()->subMonths(11)->startOfMonth();
            $startOfPreviousPeriod = Carbon::now()->subMonths(23)->startOfMonth();
            $endOfPreviousPeriod = Carbon::now()->subMonths(12)->endOfMonth();
            $countDay = 12;

            $rawData = clone $orderQuery;
            $rawData = $rawData->select(
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
            $startOfCurrentPeriod = Carbon::now()->subYears(4)->startOfYear();
            $startOfPreviousPeriod = Carbon::now()->subYears(9)->startOfYear();
            $endOfPreviousPeriod = Carbon::now()->subYears(5)->endOfYear();
            $countDay = 5;

            $rawData = clone $orderQuery;
            $rawData = $rawData->select(
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
            $startOfCurrentPeriod = Carbon::now()->subDays(29)->startOfDay();
            $startOfPreviousPeriod = Carbon::now()->subDays(59)->startOfDay();
            $endOfPreviousPeriod = Carbon::now()->subDays(30)->endOfDay();
            $countDay = 30;

            $rawData = clone $orderQuery;
            $rawData = $rawData->select(
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
        $currentMetricsQuery = clone $orderQuery;
        $currentMetrics = $currentMetricsQuery->where('created_at', '>=', $startOfCurrentPeriod)
            ->select(
                DB::raw('SUM(total) as total_sales'),
                DB::raw('COUNT(id) as total_count')
            )->first();

        // 4. Ambil Metrik Periode SEBELUMNYA
        $previousMetricsQuery = clone $orderQuery;
        $previousMetrics = $previousMetricsQuery->whereBetween('created_at', [$startOfPreviousPeriod, $endOfPreviousPeriod])
            ->select(
                DB::raw('SUM(total) as total_sales'),
                DB::raw('COUNT(id) as total_count')
            )->first();

        $totalSemuaPenjualan = $currentMetrics->total_sales ?? 0;
        $totalTransaksi = $currentMetrics->total_count ?? 0;
        $rataRataHarian = $totalSemuaPenjualan / $countDay;

        $prevTotal = $previousMetrics->total_sales ?? 0;
        $prevCount = $previousMetrics->total_count ?? 0;

        $nominalDiffPenjualan = $totalSemuaPenjualan - $prevTotal;
        $nominalDiffTransaksi = $totalTransaksi - $prevCount;

        $diffPenjualanPersen = $this->calculatePercentage($totalSemuaPenjualan, $prevTotal);
        $diffTransaksiPersen = $this->calculatePercentage($totalTransaksi, $prevCount);

        // 5. Bagian Produk Terjual (Dengan filter Role)
        $produkTerjualQuery = OrderItem::with('menu')
            ->whereHas('order', function ($query) use ($tanggalProduk, $isAdmin, $user) {
                $query->whereDate('created_at', $tanggalProduk);
                if (!$isAdmin) {
                    $query->where('user_id', $user->id);
                }
            });

        $produkTerjual = $produkTerjualQuery->select('menu_id', DB::raw('SUM(qty) as total_qty'), DB::raw('SUM(subtotal) as total_pendapatan'))
            ->groupBy('menu_id')
            ->orderByDesc('total_qty')
            ->get();

        // 6. Data Tambahan Khusus Kasir (Status Shift & Jadwal)
        $shiftStatus = null;
        $jadwalShifts = collect();

        if (!$isAdmin) {
            // Cek sudah buka shift atau belum
            $shiftStatus = Kas::where('user_id', $user->id)
                ->whereDate('created_at', now()->toDateString())
                ->whereIn('keterangan', ['Modal Awal Shift', 'Tutup Shift'])
                ->latest()
                ->first();

            // Ambil 5 jadwal terdekat Kasir (Dimulai dari hari ini)
            $jadwalShifts = Shift::where('user_id', $user->id)
                ->whereDate('tanggal', '>=', now()->toDateString())
                ->orderBy('tanggal', 'asc')
                ->take(5)
                ->get();
        }

        return view('dashboard', compact(
            'isAdmin',
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
            'diffTransaksiPersen',
            'shiftStatus',
            'jadwalShifts' // <--- Jangan lupa masukkan ke compact
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
