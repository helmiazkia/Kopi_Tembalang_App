<x-layouts.admin title="Struk">

    <div class="max-w-md mx-auto bg-white p-6 shadow mt-10">

        <h2 class="text-center font-bold text-lg mb-4">
            COFFEE SHOP
        </h2>

        <p>Order #{{ $order->id }}</p>
        <p>Meja : {{ $order->table->table_number }}</p>
        <p>Tanggal : {{ $order->created_at->format('d M Y H:i') }}</p>

        <hr class="my-3">

        @foreach($order->items as $item)

        <div class="mb-2">

            <div class="flex justify-between">
                <span>{{ $item->qty }}x {{ $item->menu->name }}</span>
                <span>Rp {{ number_format($item->subtotal) }}</span>
            </div>

            {{-- OPTION --}}
            @if($item->options->count())
            <div class="ml-4 text-sm text-gray-500">

                @foreach($item->options as $opt)

                <div>
                    {{ optional($opt->optionItem->menuOption)->name }} :
                    {{ optional($opt->optionItem)->name }}
                </div>

                @endforeach

            </div>
            @endif

        </div>

        @endforeach

        <hr class="my-3">

        <div class="flex justify-between font-bold">
            <span>Total</span>
            <span>Rp {{ number_format($order->total_price) }}</span>
        </div>

        <p class="mt-3">
            Payment : {{ strtoupper($order->payment->method ?? '-') }}
        </p>

        @if($order->payment->channel)
        <p>
            Channel : {{ $order->payment->channel }}
        </p>
        @endif

        <hr class="my-3">

        <p class="text-center text-sm">
            Terima kasih 🙏
        </p>

        <div class="mt-4 text-center">
            <button onclick="window.print()" class="btn btn-primary">
                Print
            </button>
        </div>

    </div>

</x-layouts.admin>