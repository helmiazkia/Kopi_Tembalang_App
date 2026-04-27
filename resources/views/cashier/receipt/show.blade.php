<!DOCTYPE html>
<html>
<head>
    <title>Struk</title>

    <style>
        body {
            font-family: monospace;
            width: 260px;
            margin: auto;
        }

        .center { text-align: center; }
        .line { border-top: 1px dashed #000; margin: 6px 0; }
        .flex { display: flex; justify-content: space-between; }
        .small { font-size: 12px; }
    </style>
</head>

<body onload="printStruk()">

<div class="center">
    <h3>KOPI TEMBALANG</h3>
    <p class="small">Semarang</p>
</div>

<div class="line"></div>

<p>Order : #{{ $order->id }}</p>
<p>{{ now()->format('d/m/Y H:i') }}</p>
<p>Kasir : {{ auth()->user()->name }}</p>

<div class="line"></div>

@foreach($order->items as $item)
    <div>
        <b>{{ $item->menu->name }}</b>
    </div>

    @foreach($item->options as $opt)
        <div class="small">
            + {{ $opt->optionItem->name ?? '' }}
        </div>
    @endforeach

    <div class="flex">
        <span>1 x {{ number_format($item->price) }}</span>
        <span>{{ number_format($item->subtotal) }}</span>
    </div>
@endforeach

<div class="line"></div>

<div class="flex">
    <b>Total</b>
    <b>Rp {{ number_format($order->total_price) }}</b>
</div>

<div class="line"></div>

<p class="center small">Terima kasih ☕</p>

<script>
function printStruk() {
    console.log('Starting print process...');

    // 🔥 AUTO PRINT
    try {
        window.print();
        console.log('Print command executed');
    } catch (error) {
        console.error('Print error:', error);
        alert('Print error: ' + error);
    }

    // 🔥 OPTIONAL: kembali ke kasir setelah print
    setTimeout(() => {
        console.log('Redirecting to cashier orders...');
        window.location.href = "/cashier/orders";
    }, 1000);
}
</script>

</body>
</html>