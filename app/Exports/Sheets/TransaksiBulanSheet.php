<?php

namespace App\Exports\Sheets;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TransaksiBulanSheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithStyles, WithColumnFormatting
{
    protected $start;
    protected $end;

    public function __construct($start, $end)
    {
        $this->start = $start . ' 00:00:00';
        $this->end = $end . ' 23:59:59';
    }

    public function collection()
    {
        $data = collect();

        // Ambil data transaksi harian di dalam range tanggal
        $orders = Order::whereBetween('created_at', [$this->start, $this->end])
            ->where('payment_status', 'paid')
            ->select(
                DB::raw('DATE(created_at) as tanggal'),
                DB::raw('COUNT(id) as transaksi'),
                DB::raw('SUM(CASE WHEN payment_method = "cash" THEN total ELSE 0 END) as total_cash'),
                DB::raw('SUM(CASE WHEN payment_method = "qris" THEN total ELSE 0 END) as total_qris'),
                DB::raw('SUM(total) as pendapatan')
            )
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc')
            ->get();

        $totalTrx = 0;
        $totalCash = 0;
        $totalQris = 0;
        $totalPendapatan = 0;

        foreach ($orders as $order) {
            $rataRata = $order->transaksi > 0 ? round($order->pendapatan / $order->transaksi) : 0;

            $data->push([
                'Tanggal' => Carbon::parse($order->tanggal)->translatedFormat('d M Y'),
                'Jml Transaksi' => $order->transaksi,
                'Tunai (Rp)' => $order->total_cash ?? 0,
                'QRIS (Rp)' => $order->total_qris ?? 0,
                'Rata-rata/Trx (Rp)' => $rataRata,
                'Total Pendapatan (Rp)' => $order->pendapatan ?? 0,
            ]);

            // Hitung untuk baris total akhir
            $totalTrx += $order->transaksi;
            $totalCash += $order->total_cash ?? 0;
            $totalQris += $order->total_qris ?? 0;
            $totalPendapatan += $order->pendapatan ?? 0;
        }

        // TAMBAHKAN BARIS TOTAL KESELURUHAN DI AKHIR TABEL
        if ($data->count() > 0) {
            $rataRataTotal = $totalTrx > 0 ? round($totalPendapatan / $totalTrx) : 0;
            $data->push([
                'Tanggal' => 'TOTAL KESELURUHAN',
                'Jml Transaksi' => $totalTrx,
                'Tunai (Rp)' => $totalCash,
                'QRIS (Rp)' => $totalQris,
                'Rata-rata/Trx (Rp)' => $rataRataTotal,
                'Total Pendapatan (Rp)' => $totalPendapatan,
            ]);
        }

        return $data;
    }

    public function headings(): array
    {
        return ['Tanggal', 'Jumlah Transaksi', 'Tunai (Rp)', 'QRIS (Rp)', 'Rata-rata/Trx (Rp)', 'Total Pendapatan (Rp)'];
    }

    public function title(): string
    {
        return 'Rekap Transaksi';
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'C' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'D' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $style = [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF107C41']],
            ]
        ];

        if ($lastRow > 1) {
            $style[$lastRow] = [
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFD1E7DD']],
            ];
        }
        return $style;
    }
}
