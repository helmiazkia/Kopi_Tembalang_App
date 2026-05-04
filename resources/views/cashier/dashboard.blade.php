<x-layouts.cashier title="Dashboard Kasir">
    <div class="container mx-auto p-10">
        <h1 class="text-3xl font-semibold mb-4">Dashboard Kasir</h1>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-5">
            <div class="card bg-base-100 card-sm shadow-xs p-2">
                <div class="card-body">
                    <h2 class="card-title text-md">Total Order</h2>
                    <p class="font-bold text-4xl">{{ $totalOrders }}</p>
                </div>
            </div>
            <div class="card bg-base-100 card-sm shadow-xs p-2">
                <div class="card-body">
                    <h2 class="card-title text-md">Total Income</h2>
                    <p class="font-bold text-4xl">Rp {{ number_format($totalIncome) }}</p>
                </div>
            </div>
            <div class="card bg-base-100 card-sm shadow-xs p-2">
                <div class="card-body">
                    <h2 class="card-title text-md">Cash</h2>
                    <p class="font-bold text-4xl">Rp {{ number_format($totalCash) }}</p>
                </div>
            </div>
            <div class="card bg-base-100 card-sm shadow-xs p-2">
                <div class="card-body">
                    <h2 class="card-title text-md">QRIS</h2>
                    <p class="font-bold text-4xl">Rp {{ number_format($totalQris) }}</p>
                </div>
            </div>
        </div>
    </div>

</x-layouts.cashier>