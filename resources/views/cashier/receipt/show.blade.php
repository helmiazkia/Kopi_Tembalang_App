<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Struk #{{ $order->id }} - Kopi Tembalang</title>
    {{-- Mencegah user melakukan zoom di HP kasir --}}
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        /* Konfigurasi Ukuran Kertas Thermal (biasanya 58mm atau 80mm) */
        @page {
            margin: 0;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            width: 280px;
            /* Standar printer thermal 58mm */
            margin: 0 auto;
            padding: 10px;
            color: #000;
            font-size: 14px;
            line-height: 1.2;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .line {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }

        .flex {
            display: flex;
            justify-content: space-between;
        }

        .small {
            font-size: 12px;
        }

        .bold {
            font-weight: bold;
        }

        /* Hilangkan margin/padding saat print agar tidak kepotong */
        @media print {
            body {
                width: 100%;
                margin: 0;
                padding: 5px;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body onload="printStruk()">

    {{-- HEADER --}}
    <div class="center">
        <h3 style="margin-bottom: 5px;">KOPI TEMBALANG</h3>
        <p class="small">Jl. Tembalang Raya No. XX, Semarang<br>
            Telp: 0812-XXXX-XXXX</p>
    </div>

    <div class="line"></div>

    {{-- INFO ORDER --}}
    <div class="small">
        <div class="flex">
            <span>No. Order</span>
            <span>#{{ $order->id }}</span>
        </div>
        <div class="flex">
            <span>Tanggal</span>
            <span>{{ $order->created_at->format('d/m/Y H:i') }}</span>
        </div>
        <div class="flex">
            <span>Kasir</span>
            <span>{{ auth()->user()->name }}</span>
        </div>
        <div class="flex">
            <span>Tipe</span>
            <span class="bold">{{ strtoupper($order->order_type) }}</span>
        </div>
        @if($order->table)
        <div class="flex">
            <span>Meja</span>
            <span class="bold">{{ $order->table->table_number }}</span>
        </div>
        @endif
    </div>

    <div class="line"></div>

    {{-- ITEMS --}}
    @foreach($order->items as $item)
    <div style="margin-bottom: 8px;">
        <div class="bold uppercase">{{ $item->menu->name }}</div>

        @foreach($item->options as $opt)
        <div class="small" style="padding-left: 10px;">
            + {{ $opt->menuOptionItem->name ?? '' }}
            @if($opt->price > 0)
            ({{ number_format($opt->price) }})
            @endif
        </div>
        @endforeach

        <div class="flex small">
            <span>{{ $item->qty }} x {{ number_format($item->price) }}</span>
            <span>{{ number_format($item->subtotal) }}</span>
        </div>
    </div>
    @endforeach

    <div class="line"></div>

    {{-- TOTAL --}}
    <div class="flex" style="font-size: 16px;">
        <span class="bold">TOTAL</span>
        <span class="bold">Rp {{ number_format($order->total_price) }}</span>
    </div>

    {{-- Tambahkan Info Metode Pembayaran --}}
    <div class="flex small" style="margin-top: 4px;">
        <span>Metode Bayar</span>
        <span>{{ strtoupper($order->payment->method ?? 'Cash') }}</span>
    </div>

    <div class="line"></div>

    {{-- FOOTER --}}
    <div class="center small">
        <p>Follow us on Instagram:<br><b>@kopitembalang</b></p>
        <p>Terima kasih atas kunjungannya ☕</p>
        <p>-- LUNAS --</p>
    </div>

    {{-- Tombol bantuan jika auto-print gagal --}}
    <div class="no-print center" style="margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px; cursor: pointer;">Cetak Ulang</button>
        <a href="/cashier/orders" style="display: block; margin-top: 10px; color: blue;">Kembali ke Kasir</a>
    </div>

    <script>
        function printStruk() {
            // Beri jeda sedikit agar CSS render sempurna sebelum print
            setTimeout(() => {
                window.print();
            }, 500);

            // Otomatis balik ke kasir SETELAH dialog print ditutup
            window.onafterprint = function() {
                window.location.href = "/cashier/orders";
            };

            // Backup jika onafterprint tidak jalan di browser tertentu
            setTimeout(() => {
                // Hanya redirect jika tidak sedang dalam dialog print
                // (biasanya 5 detik sudah cukup untuk user berinteraksi)
                // window.location.href = "/cashier/orders"; 
            }, 10000);
        }
    </script>

</body>

</html>