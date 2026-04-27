<x-layouts.cashier title="Pembayaran">

<div class="max-w-md mx-auto p-6">

    <h1 class="text-xl font-bold mb-4">
        Bayar Order #{{ $order->id }}
    </h1>

    <div class="bg-white p-4 rounded shadow mb-4">

        <p>Meja: {{ $order->table->table_number ?? 'Takeaway' }}</p>

        <p class="text-gray-500 mt-2">Total</p>
        <h2 class="text-3xl font-bold text-primary">
            Rp {{ number_format($order->total_price) }}
        </h2>

    </div>

    <form method="POST" action="{{ route('cashier.payments.pay', $order->id) }}">
        @csrf

        <button class="btn btn-success w-full">
            💵 Bayar Cash
        </button>

    </form>

</div>

</x-layouts.cashier>