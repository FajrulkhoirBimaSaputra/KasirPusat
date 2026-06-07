<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Sheets\TransaksiBulanSheet;
use App\Exports\Sheets\ProdukBulanSheet;

class LaporanBulananExport implements WithMultipleSheets
{
    use Exportable;

    protected $start;
    protected $end;

    public function __construct($start, $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    public function sheets(): array
    {
        return [
            new TransaksiBulanSheet($this->start, $this->end),
            new ProdukBulanSheet($this->start, $this->end),
        ];
    }
}
