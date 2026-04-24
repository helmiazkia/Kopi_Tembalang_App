<x-layouts.cashier title="Pembayaran">

<div class="max-w-xl mx-auto p-6">

    {{-- HEADER --}}
    <h1 class="text-2xl font-bold mb-4">
        💳 Pembayaran Order #{{ $order->id }}
    </h1>

    {{-- INFO ORDER --}}
    <div class="bg-white rounded-xl shadow p-5 mb-5">

        <p class="text-sm text-gray-500">Meja</p>
        <p class="font-semibold mb-2">
            {{ $order->table->table_number ?? 'Takeaway' }}
        </p>

        <p class="text-sm text-gray-500">Total</p>
        <h2 class="text-3xl font-bold text-primary">
            Rp {{ number_format($order->total_price) }}
        </h2>

    </div>

    {{-- PILIH METODE --}}
    <div class="bg-white rounded-xl shadow p-5 mb-5">

        <p class="font-semibold mb-3">Pilih Metode Pembayaran</p>

        <div class="flex gap-3">

            <button type="button"
                onclick="selectMethod('cash')"
                id="btn-cash"
                class="btn flex-1">
                💵 Cash
            </button>

            <button type="button"
                onclick="selectMethod('qris')"
                id="btn-qris"
                class="btn flex-1">
                📱 QRIS
            </button>

        </div>

    </div>

    {{-- QRIS --}}
    <div id="qris-box" class="hidden bg-white p-5 rounded-xl shadow text-center mb-5">

        <p class="mb-3 font-semibold">Scan QRIS</p>

        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=ORDER-{{ $order->id }}">

        <p class="text-xs text-gray-400 mt-2">
            Minta customer scan QR ini
        </p>

    </div>

    {{-- FORM SUBMIT --}}
    <form method="POST" action="{{ route('cashier.orders.pay', $order->id) }}">
        @csrf

        <input type="hidden" name="method" id="method">

        <button id="pay-btn"
            class="btn btn-success w-full mt-3"
            disabled>
            Bayar Sekarang
        </button>
    </form>

</div>

{{-- SCRIPT --}}
<script>

let selectedMethod = null

function selectMethod(method){

    selectedMethod = method

    document.getElementById('method').value = method

    // reset style
    document.getElementById('btn-cash').classList.remove('btn-primary')
    document.getElementById('btn-qris').classList.remove('btn-primary')

    // aktifkan
    document.getElementById('btn-'+method).classList.add('btn-primary')

    // tampilkan QRIS kalau dipilih
    if(method === 'qris'){
        document.getElementById('qris-box').classList.remove('hidden')
    } else {
        document.getElementById('qris-box').classList.add('hidden')
    }

    // enable button
    document.getElementById('pay-btn').disabled = false
}

</script>

</x-layouts.cashier>