<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Struk #{{ $order->id }}</title>
    <style>
        @page { 
            size: 58mm auto; 
            margin: 0; 
        }

        body {
            width: 58mm;
            margin: 0;
            padding: 8px; /* Padding ditambah agar lebih presisi */
            font-family: "Courier New", Courier, monospace;
            font-size: 12px; /* Font utama diperbesar */
            line-height: 1.3;
            color: #000;
        }

        .center { text-align: center; }
        .bold { font-weight: bold; }
        .flex { display: flex; justify-content: space-between; }
        
        .line {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }

        .item-block { margin-bottom: 8px; }
        .item-total { display: flex; justify-content: space-between; font-weight: bold; font-size: 13px; }
        
        .opt-group { margin-left: 10px; font-size: 11px; } /* Opsi juga diperbesar */
        .opt-row { display: flex; justify-content: space-between; }
        
        h3 { font-size: 16px; margin: 0 0 4px 0; } /* Judul toko lebih besar */
        .tiny { font-size: 10px; }

        @media print {
            .no-print { display: none !important; }
            body { width: 100%; }
        }
    </style>
</head>

<body>
    <div class="center">
        <h3>KOPI TEMBALANG</h3>
        <div class="tiny">
            Jl. Banjarsari Raya No.53, Tembalang<br>
            Semarang
        </div>
    </div>
    <div class="line"></div>

    <div>
        <div class="flex"><span>No:</span><span class="bold">#{{ $order->id }}</span></div>
        <div class="flex"><span>Tgl:</span><span>{{ $order->created_at->format('d/m/y H:i') }}</span></div>
        <div class="flex"><span>Kasir:</span><span>{{ auth()->user()->name }}</span></div>
        <div class="flex"><span>Tipe:</span><span class="bold">{{ strtoupper($order->order_type) }}</span></div>
    </div>
    <div class="line"></div>

    @foreach($order->items as $item)
    <div class="item-block">
        <div class="bold">{{ $item->menu->name }}</div>
        
        @foreach($item->options as $opt)
        <div class="opt-group">
            <div class="opt-row">
                <span>+ {{ $opt->optionItem->name }}</span>
                <span>{{ number_format($opt->optionItem->price ?? 0) }}</span>
            </div>
        </div>
        @endforeach

        <div class="item-total">
            <span>{{ $item->qty }} x {{ number_format($item->price) }}</span>
            <span>{{ number_format($item->subtotal) }}</span>
        </div>
    </div>
    @endforeach

    <div class="line"></div>

    <div class="flex" style="font-size: 14px;">
        <span class="bold">TOTAL</span>
        <span class="bold">Rp {{ number_format($order->total_price) }}</span>
    </div>

    <div class="line"></div>

    <div class="center tiny">
        <div>Terima Kasih ☕</div>
        <div class="bold">===== LUNAS =====</div>
    </div>

    <div class="no-print center" style="margin-top: 20px;">
        <button onclick="window.print()" style="padding: 8px 16px; cursor: pointer;">Cetak Ulang</button>
    </div>

    <script>
        window.onload = function() {
            window.print();
            window.onafterprint = function() {
                window.location.href = "/cashier/orders";
            };
        }
    </script>
</body>
</html>