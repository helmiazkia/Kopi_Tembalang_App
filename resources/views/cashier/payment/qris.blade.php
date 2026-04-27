<!DOCTYPE html>
<html>

<head>
    <title>Pembayaran QRIS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('midtrans.clientKey') }}"></script>

    <style>
        body {
            font-family: sans-serif;
            background: #f5f5f5;
            padding: 20px;
            text-align: center;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 15px;
            max-width: 400px;
            margin: auto;
        }

        .price {
            font-size: 22px;
            color: #6d28d9;
            margin: 10px 0;
        }

        .timer {
            color: red;
            font-weight: bold;
        }

        .btn {
            margin-top: 15px;
            padding: 12px;
            width: 100%;
            border: none;
            border-radius: 10px;
            background: #6d28d9;
            color: white;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="card">

        <h2>Scan QR untuk bayar</h2>

        <div class="price">
            Rp {{ number_format($payment->amount) }}
        </div>

        <p>Order #{{ $order->id }}</p>

        <div class="timer" id="timer"></div>

        <button class="btn" onclick="payNow()">
            Tampilkan QR
        </button>

        @if(config('app.env') === 'local')
        <button class="btn" onclick="testComplete()" style="background: #10b981; margin-top: 10px;">
            ✅ Mark as Completed (Test)
        </button>
        @endif

    </div>

    <script>
        // 🔥 SNAP TOKEN DARI PAYMENT
        function payNow() {
            snap.pay('{{ $payment->snap_token }}', {

                onSuccess: function(result) {
                    console.log('success')
                },

                onPending: function(result) {
                    console.log('pending') // 🔥 ini normal untuk QRIS
                },

                onError: function(result) {
                    alert("Gagal")
                }
            });
        }

        // 🔥 AUTO OPEN QR (TANPA KLIK)
        window.onload = function() {
            payNow()
        }

        // 🔥 TEST COMPLETE PAYMENT
        function testComplete() {
            if (confirm('Mark payment as completed? (Development only)')) {
                fetch('/test-callback/{{ $order->id }}')
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            window.location.href = "/cashier/receipt/{{ $order->id }}";
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        alert('Network error: ' + error);
                    });
            }
        }


        // ⏳ COUNTDOWN (AMBIL DARI DB)
        let expiredAt = new Date("{{ $payment->expired_at }}").getTime();

        setInterval(() => {

            let now = new Date().getTime();
            let distance = expiredAt - now;

            if (distance <= 0) {
                alert("QR Expired");
                window.location.href = "/cashier/orders";
                return;
            }

            let min = Math.floor(distance / 1000 / 60);
            let sec = Math.floor((distance / 1000) % 60);

            document.getElementById('timer').innerText =
                `Expired dalam ${min}:${sec < 10 ? '0'+sec : sec}`;

        }, 1000);


        // 🔥 AUTO CEK STATUS
        setInterval(() => {

            fetch("/api/check-payment/{{ $order->id }}")
                .then(res => res.json())
                .then(res => {

                    if (res.status === 'paid') {
                        window.location.href = "/cashier/receipt/{{ $order->id }}"
                    }

                })

        }, 3000);
    </script>

</body>

</html>