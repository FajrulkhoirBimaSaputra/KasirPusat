<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Kas;
use App\Models\Menu;
use App\Models\Ingredient;
use App\Models\IngredientHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();

        // 1 & 3. Histori Uang (Kas) & Laporan Keuangan (POS)
        $uangMasukKas = Kas::where('jenis', 'pemasukan')->whereBetween('created_at', [$startDate, $endDate])->sum('nominal');
        $uangKeluarKas = Kas::where('jenis', 'pengeluaran')->whereBetween('created_at', [$startDate, $endDate])->sum('nominal');

        $totalPendapatanPOS = Order::where('payment_status', 'paid')->whereBetween('created_at', [$startDate, $endDate])->sum('total');
        $totalTransaksi = Order::where('payment_status', 'paid')->whereBetween('created_at', [$startDate, $endDate])->count();
        $totalPendapatanKotor = $totalPendapatanPOS + $uangMasukKas;

        // 2. Item Paling Laris
        $itemLaris = DB::table('menus')
            ->join('order_items', 'menus.id', '=', 'order_items.menu_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.payment_status', 'paid')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->select('menus.id', 'menus.nama', DB::raw('SUM(order_items.qty) as total_terjual'))
            ->groupBy('menus.id', 'menus.nama')
            ->orderByDesc('total_terjual')
            ->take(5)
            ->get();

        // Item Tidak Laris
        $itemTidakLaris = DB::table('menus')
            ->leftJoin('order_items', 'menus.id', '=', 'order_items.menu_id')
            ->leftJoin('orders', function ($join) use ($startDate, $endDate) {
                $join->on('order_items.order_id', '=', 'orders.id')
                    ->where('orders.payment_status', 'paid')
                    ->whereBetween('orders.created_at', [$startDate, $endDate]);
            })
            ->select('menus.id', 'menus.nama', DB::raw('COALESCE(SUM(order_items.qty), 0) as total_terjual'))
            ->groupBy('menus.id', 'menus.nama')
            ->orderBy('total_terjual', 'asc')
            ->take(5)
            ->get();

        // 4. Stok Overview & Log Histori Mutasi
        $stokKritis = Ingredient::where('stok', '<=', 10)->get();

        $histories = IngredientHistory::with(['ingredient', 'user'])
            ->latest()
            ->take(10)
            ->get();

        return view('owner.dashboard', compact(
            'totalPendapatanPOS',
            'uangMasukKas',
            'uangKeluarKas',
            'totalPendapatanKotor',
            'totalTransaksi',
            'itemLaris',
            'itemTidakLaris',
            'stokKritis',
            'histories',
            'startDate',
            'endDate'
        ));
    }
}
