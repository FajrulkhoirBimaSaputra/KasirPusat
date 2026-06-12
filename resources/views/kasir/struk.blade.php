<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Transaksi #{{ $order->id }}</title>

    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            width: 240px;
            margin: auto;
            color: #000;
            background: #fff;
            padding: 10px 0;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .font-bold {
            font-weight: bold;
        }

        .line {
            border-top: 1px dashed black;
            margin: 8px 0;
        }

        table {
            width: 100%;
            font-size: 12px;
            border-collapse: collapse;
        }

        td {
            vertical-align: top;
            padding: 2px 0;
        }

        button {
            margin-top: 15px;
            width: 100%;
            padding: 10px;
            font-weight: bold;
            cursor: pointer;
        }

        @media print {
            button {
                display: none;
            }

            @page {
                margin: 0;
            }
        }
    </style>
</head>

<body>

    {{-- HEADER TOKO --}}
    <div class="center">
        <span class="font-bold" style="font-size: 14px;">TOKO KOPI PUSAT</span><br>
        <span style="font-size: 11px;">Nilasari<br>
            Gonilan, Kartasura, Sukoharjo<br>
            Jawa Tengah 57169</span>
    </div>

    <div class="line"></div>

    {{-- INFO TRANSAKSI --}}
    <table>
        <tr>
            <td style="width: 55px;">No. Struk</td>
            <td style="width: 10px;">:</td>
            <td>#{{ $order->id }}</td>
        </tr>
        <tr>
            <td>Kasir</td>
            <td>:</td>
            <td>{{ $order->user->name }}</td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td>:</td>
            <td>{{ $order->created_at->format('d-m-Y H:i') }} WIB</td>
        </tr>
    </table>

    <div class="line"></div>

    {{-- DAFTAR ITEM BELANJA --}}
    <table>
        @foreach ($order->items as $item)
            <tr>
                <td colspan="2" class="font-bold" style="padding-top: 4px;">
                    {{ $item->menu->nama }}
                </td>
            </tr>
            <tr>
                <td style="color: #444;">
                    {{ $item->qty }} x {{ number_format($item->harga, 0, ',', '.') }}
                </td>
                <td class="right" style="font-weight: 500;">
                    {{ number_format($item->subtotal, 0, ',', '.') }}
                </td>
            </tr>
        @endforeach
    </table>

    <div class="line"></div>

    {{-- TOTAL & PEMBAYARAN --}}
    <table>
        <tr>
            <td class="font-bold">TOTAL AKHIR</td>
            <td class="right font-bold" style="font-size: 13px;">
                Rp {{ number_format($order->total, 0, ',', '.') }}
            </td>
        </tr>
        <tr>
            <td>Metode Bayar</td>
            <td class="right font-bold uppercase">
                {{ $order->payment_method }}
            </td>
        </tr>

        {{-- STRUKTUR TAMBAHAN: Munculkan nominal tunai & kembalian jika pembayaran Cash --}}
        @if ($order->payment_method === 'cash' || $order->payment_method === 'tunai')
            <tr>
                <td>Diterima</td>
                <td class="right">
                    Rp {{ number_format($order->cash_received ?? $order->total, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td>Kembalian</td>
                <td class="right">
                    Rp {{ number_format($order->change ?? 0, 0, ',', '.') }}
                </td>
            </tr>
        @endif
    </table>

    <div class="line"></div>

    {{-- FOOTER STRUK --}}
    <div class="center" style="font-size: 11px; margin-top: 10px;">
        Terima Kasih<br>
        Silakan datang kembali
    </div>

    <button onclick="window.print()">Print Manual</button>

    {{-- SCRIPT AUTO PRINT & AUTO CLOSE TAB --}}
    <script>
        window.onload = function() {
            window.print();
        }

        // Otomatis menutup tab/jendela kecil POS setelah printer merespons (di-print atau di-cancel)
        window.onafterprint = function() {
            window.close();
        }
    </script>

</body>

</html>
