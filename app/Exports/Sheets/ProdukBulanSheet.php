<?php

namespace App\Exports\Sheets;

use App\Models\OrderItem;
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

class ProdukBulanSheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithStyles, WithColumnFormatting
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

        $items = OrderItem::with('menu')
            ->whereHas('order', function ($q) {
                $q->whereBetween('created_at', [$this->start, $this->end])
                    ->where('payment_status', 'paid');
            })
            ->select('menu_id', DB::raw('SUM(qty) as total_qty'), DB::raw('SUM(subtotal) as total_pendapatan'))
            ->groupBy('menu_id')
            ->orderByDesc('total_qty')
            ->get();

        $totalQty = 0;
        $totalPendapatan = 0;

        foreach ($items as $item) {
            $data->push([
                'Nama Produk' => $item->menu->nama ?? 'Produk Terhapus',
                'Terjual (Porsi)' => $item->total_qty,
                'Pendapatan Kotor (Rp)' => $item->total_pendapatan,
            ]);

            $totalQty += $item->total_qty;
            $totalPendapatan += $item->total_pendapatan;
        }

        // BARIS TOTAL KESELURUHAN DI AKHIR TABEL PRODUK
        if ($data->count() > 0) {
            $data->push([
                'Nama Produk' => 'TOTAL KESELURUHAN',
                'Terjual (Porsi)' => $totalQty,
                'Pendapatan Kotor (Rp)' => $totalPendapatan,
            ]);
        }

        return $data;
    }

    public function headings(): array
    {
        return ['Nama Produk', 'Terjual (Porsi)', 'Pendapatan Kotor (Rp)'];
    }

    public function title(): string
    {
        return 'Rincian Produk';
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'C' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
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
