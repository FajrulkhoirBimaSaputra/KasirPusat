<?php

namespace App\Exports\Sheets;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TransaksiBulanSheet implements FromCollection, WithHeadings, WithTitle
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

            $order = Order::whereMonth('created_at', $m)
                ->whereYear('created_at', $this->tahun)
                ->where('payment_status', 'paid')
                ->select(
                    DB::raw('COUNT(id) as transaksi'),
                    DB::raw('SUM(CASE WHEN payment_method = "cash" THEN total ELSE 0 END) as total_cash'),
                    DB::raw('SUM(CASE WHEN payment_method = "qris" THEN total ELSE 0 END) as total_qris'),
                    DB::raw('SUM(total) as pendapatan')
                )->first();

            // Hanya masukkan bulan yang ada transaksinya
            if ($order && $order->transaksi > 0) {
                $data->push([
                    'Bulan' => $namaBulan,
                    'Jml Transaksi' => $order->transaksi,
                    'Tunai (Rp)' => $order->total_cash ?? 0,
                    'QRIS (Rp)' => $order->total_qris ?? 0,
                    'Rata-rata/Trx (Rp)' => round($order->pendapatan / $order->transaksi),
                    'Total Pendapatan (Rp)' => $order->pendapatan ?? 0,
                ]);
            }
        }
        return $data;
    }

    public function headings(): array
    {
        return ['Bulan', 'Jumlah Transaksi', 'Tunai (Rp)', 'QRIS (Rp)', 'Rata-rata/Trx (Rp)', 'Total Pendapatan (Rp)'];
    }

    public function title(): string
    {
        return 'Rekap Transaksi';
    }
}