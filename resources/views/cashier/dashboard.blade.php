<x-layouts.cashier title="Dashboard Kasir">

    {{-- Header & Filter --}}
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 bg-white p-8 rounded-3xl shadow-sm border border-slate-200 mb-8">
        <div class="flex items-center gap-5">
            <div class="w-14 h-14 bg-slate-900 text-[#D4E971] rounded-2xl flex items-center justify-center shrink-0">
                <span class="text-xl font-black">{{ substr(auth()->user()->name, 0, 1) }}</span>
            </div>
            <div>
                <p class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400 mb-1">
                    Kopi Tembalang • Kasir Unit #1
                </p>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">
                    Selamat
                    @php
                    $hour = \Carbon\Carbon::now()->hour;
                    if ($hour < 11) {
                        $greeting='Pagi' ;
                        } elseif ($hour < 15) {
                        $greeting='Siang' ;
                        } elseif ($hour < 18) {
                        $greeting='Sore' ;
                        } else {
                        $greeting='Malam' ;
                        }
                        echo $greeting;
                        @endphp,
                        {{ explode(' ', auth()->user()->name)[0] }}!
                        </h1>
            </div>
        </div>

        <form action="{{ route('cashier.dashboard') }}" method="GET" class="flex items-center gap-3 w-full lg:w-auto">
            <input
                type="date"
                name="date"
                value="{{ $filterDate->format('Y-m-d') }}"
                class="input input-bordered bg-white border-slate-200 text-slate-600 font-semibold rounded-2xl text-sm">
            <button type="submit" class="btn bg-slate-900 hover:bg-slate-700 text-[#D4E971] border-none rounded-2xl font-black text-xs tracking-widest px-6 transition-all duration-300">
                Terapkan
            </button>
        </form>
    </div>

    {{-- Row 1: Kartu Statistik --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">

        {{-- Uang Tunai --}}
        <div class="bg-white p-7 rounded-3xl border border-slate-200 shadow-sm">
            <div class="flex justify-between items-start mb-5">
                <div class="p-3 bg-emerald-50 text-emerald-600 rounded-2xl">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <span class="text-[10px] font-black text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-xl uppercase tracking-widest">Cash</span>
            </div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Uang Tunai</p>
            <h2 class="text-2xl font-black text-slate-900 tracking-tight">Rp {{ number_format($totalCash) }}</h2>
        </div>

        {{-- Online Payment --}}
        <div class="bg-white p-7 rounded-3xl border border-slate-200 shadow-sm">
            <div class="flex justify-between items-start mb-5">
                <div class="p-3 bg-indigo-50 text-indigo-600 rounded-2xl">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                </div>
                <span class="text-[10px] font-black text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-xl uppercase tracking-widest">QRIS</span>
            </div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Online Payment</p>
            <h2 class="text-2xl font-black text-slate-900 tracking-tight">Rp {{ number_format($totalOnline) }}</h2>
        </div>

        {{-- Total Revenue --}}
        <div class="bg-slate-900 p-7 rounded-3xl border border-slate-800 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1 bg-[#D4E971]"></div>
            <div class="flex justify-between items-start mb-5">
                <div class="p-3 bg-[#D4E971] text-black rounded-2xl">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
                <span class="text-[10px] font-black text-[#D4E971] border border-[#D4E971]/30 px-2.5 py-1 rounded-xl uppercase tracking-widest">Revenue</span>
            </div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Pendapatan Anda</p>
            <h2 class="text-2xl font-black text-[#D4E971] tracking-tight">Rp {{ number_format($totalIncome) }}</h2>
        </div>
    </div>

    {{-- Audit Setoran Unit --}}
    <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm mb-8">
        <div class="mb-6">
            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400 mb-1">Akumulasi Hari Ini</p>
            <h3 class="text-lg font-black text-slate-900 tracking-tight">Audit Setoran Unit</h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

            {{-- Input Kasir --}}
            <div class="p-6 bg-slate-50 rounded-2xl border border-slate-200">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Online Payment (Total)</p>
                <h4 class="text-xl font-black text-slate-800 tracking-tight">Rp {{ number_format($grandTotalOnline) }}</h4>
            </div>

            {{-- Self-Order --}}
            <div class="p-6 bg-indigo-50 rounded-2xl border border-indigo-100">
                <p class="text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-2">Self-Order (Pelanggan)</p>
                <h4 class="text-xl font-black text-indigo-600 tracking-tight">Rp {{ number_format($selfOrderIncome) }}</h4>
            </div>

            {{-- Total Tunai Laci --}}
            <div class="p-6 bg-emerald-50 rounded-2xl border border-emerald-100">
                <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest mb-2">Total Tunai (Laci Gabungan)</p>
                <h4 class="text-xl font-black text-emerald-700 tracking-tight">Rp {{ number_format($grandTotalCash) }}</h4>
            </div>

            {{-- Grand Total --}}
            <div class="p-6 bg-slate-900 rounded-2xl relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-[#D4E971]"></div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Grand Total Toko</p>
                <h4 class="text-xl font-black text-[#D4E971] tracking-tight">Rp {{ number_format($grandTotalIncome) }}</h4>
            </div>
        </div>
    </div>

    {{-- Row 2: Monitor & Chart --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Monitor Aktivitas --}}
        <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm flex flex-col">
            <div class="mb-6">
                <div class="flex items-center gap-2 mb-1">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Live</p>
                    <span class="w-1.5 h-1.5 bg-red-500 rounded-full animate-pulse"></span>
                </div>
                <h3 class="text-lg font-black text-slate-800 tracking-tight">Monitor Aktivitas</h3>
            </div>

            @php
            $totalOrders = ($orderPending + $orderSelesai) ?: 1;
            $perPending = $orderPending / $totalOrders * 100;
            $perSelesai = $orderSelesai / $totalOrders * 100;
            @endphp

            <div class="space-y-7 flex-1">
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-xs font-black text-amber-500 uppercase tracking-widest">Unpaid</span>
                        <span class="text-2xl font-black text-slate-800 leading-none">{{ $orderPending }}</span>
                    </div>
                    <div class="w-full bg-slate-100 h-3 rounded-full overflow-hidden">
                        <div class="bg-amber-400 h-full rounded-full transition-all duration-1000" style="width: {{ $perPending }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-xs font-black text-emerald-500 uppercase tracking-widest">Success</span>
                        <span class="text-2xl font-black text-slate-800 leading-none">{{ $orderSelesai }}</span>
                    </div>
                    <div class="w-full bg-slate-100 h-3 rounded-full overflow-hidden">
                        <div class="bg-emerald-400 h-full rounded-full transition-all duration-1000" style="width: {{ $perSelesai }}%"></div>
                    </div>
                </div>
            </div>

            <div class="mt-8 p-5 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                <p class="text-[11px] text-slate-400 leading-relaxed text-center font-medium">
                    Data mencakup transaksi akun Anda hari ini. Lakukan pengecekan laci sebelum tutup shift.
                </p>
            </div>
        </div>

        {{-- Chart Tren Pelayanan --}}
        <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm lg:col-span-2">
            <div class="mb-6">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Volume Penjualan Per Jam</p>
                <h3 class="text-lg font-black text-slate-800 tracking-tight">Tren Pelayanan Anda</h3>
            </div>
            <div class="h-72 w-full">
                <canvas id="hourlyServiceChart"></canvas>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        Chart.defaults.color = '#94a3b8';
        Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
        Chart.defaults.font.weight = '700';

        const ctx = document.getElementById('hourlyServiceChart').getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 280);
        gradient.addColorStop(0, 'rgba(212, 233, 113, 0.4)');
        gradient.addColorStop(1, 'rgba(212, 233, 113, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chartLabels),
                datasets: [{
                    label: 'Pesanan Dilayani',
                    data: @json($chartValues),
                    borderColor: '#D4E971',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 7,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#000',
                    pointBorderWidth: 2,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 14,
                        cornerRadius: 12,
                        titleFont: {
                            size: 11,
                            weight: 'normal'
                        },
                        bodyFont: {
                            size: 14,
                            weight: '800'
                        },
                        displayColors: false,
                        callbacks: {
                            label: ctx => ctx.raw + ' Pesanan',
                        },
                    },
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 10
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f1f5f9',
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                size: 10
                            }
                        },
                    },
                },
            },
        });
    </script>
    @endpush

</x-layouts.cashier>