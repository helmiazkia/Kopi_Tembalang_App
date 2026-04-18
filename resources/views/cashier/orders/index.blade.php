<x-layouts.admin title="Kasir">

    <div class="container p-10">

        <a href="{{ route('cashier.orders.create') }}" class="btn btn-primary mb-4">
            + Input Order
        </a>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            @foreach($orders as $order)

            <div class="card bg-white shadow">

                <div class="card-body">

                    <h2>Order #{{ $order->id }}</h2>
                    <p>Meja: {{ $order->table->table_number }}</p>

                    <ul>
                        @foreach($order->items as $item)
                        <li>{{ $item->qty }}x {{ $item->menu->name }}</li>
                        @endforeach
                    </ul>

                    <p>Total: Rp {{ number_format($order->total_price) }}</p>

                    @if($order->payment && $order->payment->status == 'pending')

                    <form method="POST" action="{{ route('cashier.orders.payCash',$order->id) }}">
                        @csrf
                        <button class="btn btn-success w-full">
                            Scan Cash (Bayar)
                        </button>
                    </form>

                    @else

                    <span class="badge badge-success">
                        {{ strtoupper($order->payment->method ?? '-') }}
                    </span>

                    @endif

                </div>

            </div>

            @endforeach

        </div>

    </div>

</x-layouts.admin>