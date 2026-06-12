<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Shift</title>
    <style>
        /* Styling khusus Printer Thermal Kasir (Lebar 80mm atau 58mm) */
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 10px;
        }
        .ticket {
            width: 100%;
            max-width: 300px; /* Ukuran maksimal struk */
            margin: 0 auto;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .divider { border-top: 1px dashed #000; margin: 8px 0; }
        .flex { display: flex; justify-content: space-between; }
        
        @media print {
            @page { margin: 0; }
            body { margin: 0.5cm; }
        }
    </style>
</head>
<body>
    <div class="ticket">
        <h2 class="text-center font-bold" style="margin-bottom: 5px; font-size:16px;">LAPORAN AKHIR SHIFT</h2>
        <div class="text-center" style="margin-bottom: 15px;">
            Sistem POS Pusat<br>
            {{ now()->format('d M Y - H:i') }}
        </div>

        <div class="divider"></div>

        <div>Kasir   : {{ Auth::user()->name }}</div>
        <div>Dibuka  : {{ $waktuBukaShift->format('H:i') }} WIB</div>
        <div>Ditutup : {{ now()->format('H:i') }} WIB</div>

        <div class="divider"></div>

        <div class="font-bold" style="margin-bottom: 5px;">A. RINGKASAN PENJUALAN</div>
        <div class="flex"><span>Total Struk</span> <span>{{ $totalTransaksiHariIni }} Trx</span></div>
        <div class="flex"><span>Penjualan Kotor</span> <span>Rp {{ number_format($penjualanKotor, 0, ',', '.') }}</span></div>
        <div class="flex"><span>Refund/Retur</span> <span>- Rp {{ number_format($uangDikembalikan, 0, ',', '.') }}</span></div>
        <div class="flex font-bold"><span>Penjualan Bersih</span> <span>Rp {{ number_format($penjualanBersih, 0, ',', '.') }}</span></div>
        
        <br>
        <div class="flex"><span>-> Pembayaran Tunai</span> <span>Rp {{ number_format($tunai, 0, ',', '.') }}</span></div>
        <div class="flex"><span>-> Pembayaran QRIS</span> <span>Rp {{ number_format($qris, 0, ',', '.') }}</span></div>

        <div class="divider"></div>

        <div class="font-bold" style="margin-bottom: 5px;">B. KONTROL LACI UANG (FISIK)</div>
        <div class="flex"><span>Modal Awal</span> <span>Rp {{ number_format($modalAwal, 0, ',', '.') }}</span></div>
        <div class="flex"><span>Uang Tunai Masuk</span> <span>+ Rp {{ number_format($tunai, 0, ',', '.') }}</span></div>
        <div class="flex"><span>Kas Tambahan (In)</span> <span>+ Rp {{ number_format($pemasukan, 0, ',', '.') }}</span></div>
        <div class="flex"><span>Refund (Keluar)</span> <span>- Rp {{ number_format($uangDikembalikan, 0, ',', '.') }}</span></div>
        <div class="flex"><span>Pengeluaran Kas</span> <span>- Rp {{ number_format($pengeluaran, 0, ',', '.') }}</span></div>

        <div class="divider" style="border-top: 2px solid #000;"></div>

        <div class="flex font-bold" style="font-size: 14px;">
            <span>TOTAL DI LACI</span> 
            <span>Rp {{ number_format($jumlahTunaiDiharapkan, 0, ',', '.') }}</span>
        </div>

        <div class="divider"></div>

        <div class="text-center" style="margin-top: 15px; font-size: 10px;">
            Laporan ini dicetak secara otomatis oleh sistem.<br>
            Harap setorkan uang fisik sesuai dengan total yang tertera.
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
        
        // Menutup tab setelah jendela print di-close (cancel/print)
        window.onafterprint = function() {
            window.close();
        }
    </script>
</body>
</html>