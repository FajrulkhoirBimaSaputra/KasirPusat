<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Sheets\TransaksiBulanSheet;
use App\Exports\Sheets\ProdukBulanSheet;

class LaporanBulananExport implements WithMultipleSheets
{
    use Exportable;

    protected $tahun;

    public function __construct($tahun)
    {
        $this->tahun = $tahun;
    }

    public function sheets(): array
    {
        return [
            new TransaksiBulanSheet($this->tahun),
            new ProdukBulanSheet($this->tahun),
        ];
    }
}