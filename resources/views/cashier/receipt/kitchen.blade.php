<!-- resources/views/cashier/receipt/kitchen.blade.php -->
<div style="width: 80mm; font-family: monospace;">
    <center>
        <strong>*** ORDER DAPUR ***</strong><br>
        #{{ $order->id }} - {{ $order->customer_name }}<br>
        {{ $order->created_at->format('d/m/Y H:i') }}
    </center>
    <hr>
    @foreach($order->items as $item)
        <div>
            <strong>{{ $item->qty }}x {{ $item->menu->name }}</strong>
            @foreach($item->options as $opt)
                <br> - {{ $opt->menuOptionItem->name }}
            @endforeach
            @if($item->notes)
                <br> <small>Ket: {{ $item->notes }}</small>
            @endif
        </div>
        <br>
    @endforeach
    <hr>
    <center>SILAHKAN DIPROSES</center>
</div>

<script>
    window.onload = function() {
        window.print();
    }
</script>