<h2>Silahkan Bayar ke Kasir</h2>

<p>Order #{{ $order->id }}</p>

<img src="https://api.qrserver.com/v1/create-qr-code/?data={{ $order->id }}">

<p>Tunjukkan QR ini ke kasir</p>