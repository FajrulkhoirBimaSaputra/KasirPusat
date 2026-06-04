<?php

namespace App\Exports\Sheets;

use App\Models\OrderItem;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProdukBulanSheet implements FromCollection, WithHeadings, WithTitle
{
    protected $tahun;

    public function __construct($tahun)
    {
        $this->tahun = $tahun;
    }

    public function collection()
    {
        $data = collect();
        for ($m = 1; $m <= 12; $m++) {
            $date = Carbon::create($this->tahun, $m, 1);
            $namaBulan = $date->translatedFormat('F');

            $items = OrderItem::with('menu')
                ->whereHas('order', function ($q) use ($m) {
                    $q->whereMonth('created_at', $m)
                      ->whereYear('created_at', $this->tahun)
                      ->where('payment_status', 'paid');
                })
                ->select('menu_id', DB::raw('SUM(qty) as total_qty'), DB::raw('SUM(subtotal) as total_pendapatan'))
                ->groupBy('menu_id')
                ->orderByDesc('total_qty')
                ->get();

            foreach ($items as $item) {
                $data->push([
                    'Bulan' => $namaBulan,
                    'Nama Produk' => $item->menu->nama ?? 'Produk Terhapus',
                    'Terjual (Porsi)' => $item->total_qty,
                    'Total Pendapatan (Rp)' => $item->total_pendapatan,
                ]);
            }
        }
        return $data;
    }

    public function headings(): array
    {
        return ['Bulan', 'Nama Produk', 'Terjual (Porsi)', 'Total Pendapatan (Rp)'];
    }

    public function title(): string
    {
        return 'Rincian Produk';
    }
}