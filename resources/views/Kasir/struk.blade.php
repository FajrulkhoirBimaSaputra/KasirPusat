<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">

    <title>Struk</title>

    <style>
        body {
            font-family: monospace;
            width: 230px;
            margin: auto;
        }

        .center {
            text-align: center;
        }

        .line {
            border-top: 1px dashed black;
            margin: 6px 0;
        }

        table {
            width: 100%;
            font-size: 12px;
        }

        td {
            vertical-align: top;
        }

        .right {
            text-align: right;
        }

        button {
            margin-top: 10px;
            width: 100%;
        }

        @media print {
            button {
                display: none;
            }
        }
    </style>

</head>

<body>

    <div class="center">

        <b>KEDAI BIMZ</b><br>
        Jl. Contoh No 123<br>
        Palangkaraya<br>
        Telp: 08123456789

    </div>

    <div class="line"></div>

    <table>
        <tr>
            <td>No</td>
            <td>:</td>
            <td>{{ $order->id }}</td>
        </tr>

        <tr>
            <td>Kasir</td>
            <td>:</td>
            <td>{{ $order->user->name }}</td>
        </tr>

        <tr>
            <td>Tgl</td>
            <td>:</td>
            <td>{{ $order->created_at->format('d-m-Y H:i') }}</td>
        </tr>
    </table>

    <div class="line"></div>

    <table>

        @foreach($order->items as $item)

            <tr>
                <td colspan="3">
                    {{ $item->menu->nama }}
                </td>
            </tr>

            <tr>
                <td>
                    {{ $item->qty }} x {{ number_format($item->harga) }}
                </td>

                <td></td>

                <td class="right">
                    {{ number_format($item->subtotal) }}
                </td>
            </tr>

        @endforeach

    </table>

    <div class="line"></div>

    <table>

        <tr>
            <td>Total</td>
            <td class="right">
                Rp {{ number_format($order->total) }}
            </td>
        </tr>

        <tr>
            <td>Bayar</td>
            <td class="right">
                {{ strtoupper($order->payment_method) }}
            </td>
        </tr>

    </table>

    <div class="line"></div>

    <div class="center">

        Terima Kasih<br>
        Selamat Menikmati

    </div>

    <button onclick="window.print()">Print</button>

    <script>

        window.onload = function () {

            window.print();

        }

    </script>

</body>

</html>