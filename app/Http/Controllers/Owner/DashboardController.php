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
use Illuminate\Pagination\LengthAwarePaginator;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();

        // 1. DATA KARTU KEUANGAN (Detail Pemasukan & Pengeluaran)
        // Kecualikan "Modal Awal Shift" agar tidak dihitung sebagai pemasukan ekstra
        $uangMasukKas = Kas::where('jenis', 'pemasukan')
            ->where('keterangan', '!=', 'Modal Awal Shift')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('nominal');

        // Kecualikan "Tutup Shift" agar penarikan uang akhir hari tidak dianggap sebagai kerugian/pengeluaran toko
        $uangKeluarKas = Kas::where('jenis', 'pengeluaran')
            ->where('keterangan', '!=', 'Tutup Shift')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('nominal');

        $totalPendapatanPOS = Order::where('payment_status', 'paid')->whereBetween('created_at', [$startDate, $endDate])->sum('total');

        $totalPemasukan = $totalPendapatanPOS + $uangMasukKas;
        $totalPengeluaran = $uangKeluarKas;

        // 2. DATA TABEL 1: RIWAYAT KEUANGAN HARIAN (Gabungan POS & Kas)
        $ordersByDate = Order::where('payment_status', 'paid')->whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as pos_total'))
            ->groupBy('date')->get()->keyBy('date');

        // Sama, kecualikan juga di query per hari
        $kasMasukByDate = Kas::where('jenis', 'pemasukan')
            ->where('keterangan', '!=', 'Modal Awal Shift')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(nominal) as kas_masuk'))
            ->groupBy('date')->get()->keyBy('date');

        $kasKeluarByDate = Kas::where('jenis', 'pengeluaran')
            ->where('keterangan', '!=', 'Tutup Shift')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(nominal) as kas_keluar'))
            ->groupBy('date')->get()->keyBy('date');

        $allDates = collect(array_keys($ordersByDate->toArray()))
            ->merge(array_keys($kasMasukByDate->toArray()))
            ->merge(array_keys($kasKeluarByDate->toArray()))
            ->unique()->sortDesc();

        $riwayatHarian = [];
        foreach ($allDates as $date) {
            $pos = $ordersByDate[$date]->pos_total ?? 0;
            $k_masuk = $kasMasukByDate[$date]->kas_masuk ?? 0;
            $k_keluar = $kasKeluarByDate[$date]->kas_keluar ?? 0;

            $riwayatHarian[] = [
                'tanggal' => $date,
                'pemasukan_pos' => $pos,
                'pemasukan_kas' => $k_masuk,
                'total_pemasukan' => $pos + $k_masuk,
                'total_pengeluaran' => $k_keluar,
                'laba_bersih' => ($pos + $k_masuk) - $k_keluar
            ];
        }

        // Paginate Manual untuk Array Riwayat Harian
        $riwayatPage = $request->input('riwayat_page', 1);
        $perPage = 10;
        $offset = ($riwayatPage - 1) * $perPage;
        $riwayatHarianPaginated = new LengthAwarePaginator(
            array_slice($riwayatHarian, $offset, $perPage),
            count($riwayatHarian),
            $perPage,
            $riwayatPage,
            ['path' => $request->url(), 'query' => $request->query(), 'pageName' => 'riwayat_page']
        );

        // 3. ANALITIK MENU (Top 5 & Bottom 5)
        $itemLaris = DB::table('menus')->join('order_items', 'menus.id', '=', 'order_items.menu_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.payment_status', 'paid')->whereBetween('orders.created_at', [$startDate, $endDate])
            ->select('menus.id', 'menus.nama', DB::raw('SUM(order_items.qty) as total_terjual'))
            ->groupBy('menus.id', 'menus.nama')->orderByDesc('total_terjual')->take(5)->get();

        $itemTidakLaris = DB::table('menus')->leftJoin('order_items', 'menus.id', '=', 'order_items.menu_id')
            ->leftJoin('orders', function ($join) use ($startDate, $endDate) {
                $join->on('order_items.order_id', '=', 'orders.id')
                    ->where('orders.payment_status', 'paid')->whereBetween('orders.created_at', [$startDate, $endDate]);
            })
            ->select('menus.id', 'menus.nama', DB::raw('COALESCE(SUM(order_items.qty), 0) as total_terjual'))
            ->groupBy('menus.id', 'menus.nama')->orderBy('total_terjual', 'asc')->take(5)->get();

        // 4. DATA TABEL 2: STOK MAKANAN (Hanya Jenis = Makanan)
        $stokMakanan = Ingredient::whereIn('nama', function ($query) {
            $query->select('nama')->from('menus')->where('jenis', 'makanan')->orWhere('jenis', 'Makanan');
        })
            ->orderBy('stok', 'asc')
            ->paginate(10, ['*'], 'stok_page')
            ->appends($request->query());

        // 5. DATA TABEL 3: LOG MUTASI STOK (Hanya Jenis = Makanan)
        // Ambil daftar ID Bahan Baku yang terikat dengan menu berjenis "Makanan"
        $makananIngredientIds = \App\Models\Ingredient::whereIn('nama', function ($q) {
            $q->select('nama')->from('menus')->where('jenis', 'makanan')->orWhere('jenis', 'Makanan');
        })->pluck('id')->toArray();

        // Ambil seluruh riwayat, urutkan dari terlama ke terbaru agar data stok awal/akhir akurat
        $rawHistories = \App\Models\IngredientHistory::with(['ingredient', 'user'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Pisahkan menjadi 2 kelompok data
        $makananHistories = $rawHistories->whereIn('ingredient_id', $makananIngredientIds);
        $nonMakananHistories = $rawHistories->whereNotIn('ingredient_id', $makananIngredientIds);

        // 1. Group Khusus Makanan (Dihitung Per Hari & Per User)
        $groupedMakanan = $makananHistories->groupBy(function ($item) {
            return $item->ingredient_id . '-' . $item->user_id . '-' . \Carbon\Carbon::parse($item->created_at)->format('Y-m-d');
        })->map(function ($group) {
            $firstItem = $group->first(); // Transaksi pagi/pertama
            $lastItem = $group->last();   // Transaksi malam/terakhir

            return (object) [
                'ingredient' => $firstItem->ingredient,
                'user'       => $firstItem->user,
                'old_stok'   => $firstItem->old_stok,
                'new_stok'   => $lastItem->new_stok,
                'difference' => $group->sum('difference'),
                'tanggal'    => \Carbon\Carbon::parse($firstItem->created_at)->format('Y-m-d'),
                'created_at' => \Carbon\Carbon::parse($lastItem->created_at), // Waktu aktivitas terakhir
            ];
        });

        // 2. Non-Makanan Tetap Detail (Ditampilkan untuk setiap transaksi)
        $mappedNonMakanan = $nonMakananHistories->map(function ($item) {
            return (object) [
                'ingredient' => $item->ingredient,
                'user'       => $item->user,
                'old_stok'   => $item->old_stok,
                'new_stok'   => $item->new_stok,
                'difference' => $item->difference,
                'tanggal'    => \Carbon\Carbon::parse($item->created_at)->format('Y-m-d'),
                'created_at' => \Carbon\Carbon::parse($item->created_at),
            ];
        });

        // 3. Gabungkan keduanya, lalu urutkan kembali dari aktivitas yang paling baru
        $finalHistories = $groupedMakanan->concat($mappedNonMakanan)->sortByDesc('created_at')->values();

        // 4. Pagination Manual (Pastikan class LengthAwarePaginator diimpor jika belum)
        $mutasiPage = $request->input('mutasi_page', 1);
        $perPage = 10;
        $offset = ($mutasiPage - 1) * $perPage;
        $histories = new \Illuminate\Pagination\LengthAwarePaginator(
            $finalHistories->slice($offset, $perPage)->all(),
            $finalHistories->count(),
            $perPage,
            $mutasiPage,
            ['path' => $request->url(), 'query' => $request->query(), 'pageName' => 'mutasi_page']
        );

        return view('owner.dashboard', compact(
            'totalPemasukan',
            'totalPengeluaran',
            'totalPendapatanPOS',
            'uangMasukKas',
            'uangKeluarKas',
            'riwayatHarianPaginated',
            'itemLaris',
            'itemTidakLaris',
            'stokMakanan',
            'histories',
            'startDate',
            'endDate'
        ));
    }
}
