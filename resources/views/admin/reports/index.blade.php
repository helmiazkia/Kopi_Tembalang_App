<x-layouts.admin title="Audit & Laporan">

    {{-- Header & Filter --}}
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 bg-white p-8 rounded-3xl shadow-sm border border-slate-200 mb-8">
        <div>
            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400 mb-1">Kopi Tembalang Intelligence</p>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Audit & Laporan Transaksi</h1>
        </div>

        <div class="flex flex-wrap gap-3 w-full lg:w-auto">
            {{-- Filter Rentang Tanggal --}}
            <form action="{{ route('admin.reports.index') }}" method="GET" class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
                <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 px-4 py-2 rounded-2xl">
                    <span class="text-[10px] font-black text-slate-400 uppercase">Dari</span>
                    <input
                        type="date"
                        name="start_date"
                        value="{{ $startDate }}"
                        class="bg-transparent border-none text-sm font-semibold text-slate-700 focus:ring-0 focus:outline-none">
                </div>
                <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 px-4 py-2 rounded-2xl">
                    <span class="text-[10px] font-black text-slate-400 uppercase">Sampai</span>
                    <input
                        type="date"
                        name="end_date"
                        value="{{ $endDate }}"
                        class="bg-transparent border-none text-sm font-semibold text-slate-700 focus:ring-0 focus:outline-none">
                </div>
                <button type="submit" class="btn bg-slate-900 hover:bg-slate-700 text-[#D4E971] border-none rounded-2xl px-6 font-black text-xs tracking-widest transition-all duration-300">
                    Filter
                </button>
            </form>

            {{-- Export --}}
            <a
                href="{{ route('admin.reports.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
                class="btn bg-emerald-600 hover:bg-emerald-700 text-white border-none rounded-2xl px-6 font-black text-xs tracking-widest shadow-sm shadow-emerald-600/20 transition-all duration-300">
                Export Excel
            </a>
        </div>
    </div>

    {{-- Kartu Audit --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

        {{-- Setoran Tunai --}}
        <div class="bg-white p-7 rounded-3xl border border-slate-200 shadow-sm border-l-4 border-l-emerald-500 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-2 mb-4">
                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Setoran Tunai</p>
            </div>
            <h4 class="text-2xl font-black text-slate-900 tracking-tight">Rp {{ number_format($summary['total_cash']) }}</h4>
            <p class="text-[10px] text-emerald-600 font-bold mt-3 uppercase tracking-widest">Cocokkan dengan laci</p>
        </div>

        {{-- Online Payment --}}
        <div class="bg-white p-7 rounded-3xl border border-slate-200 shadow-sm border-l-4 border-l-indigo-500 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-2 mb-4">
                <span class="w-1.5 h-1.5 bg-indigo-500 rounded-full"></span>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Online Payment</p>
            </div>
            <h4 class="text-2xl font-black text-slate-900 tracking-tight">Rp {{ number_format($summary['total_online']) }}</h4>
            <p class="text-[10px] text-indigo-600 font-bold mt-3 uppercase tracking-widest">Cek mutasi rekening</p>
        </div>

        {{-- Nota Berhasil --}}
        <div class="bg-white p-7 rounded-3xl border border-slate-200 shadow-sm border-l-4 border-l-slate-800 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-2 mb-4">
                <span class="w-1.5 h-1.5 bg-slate-800 rounded-full"></span>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Nota Berhasil</p>
            </div>
            <h4 class="text-2xl font-black text-slate-900 tracking-tight">
                {{ $summary['count_orders'] }}
                <span class="text-xs font-medium text-slate-400">Nota</span>
            </h4>
            <p class="text-[10px] text-slate-500 font-bold mt-3 uppercase tracking-widest">Total pesanan selesai</p>
        </div>

        {{-- Total Omzet --}}
        <div class="bg-slate-900 p-7 rounded-3xl border border-slate-800 shadow-sm border-l-4 border-l-[#D4E971] hover:shadow-md transition-shadow relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-0.5 bg-[#D4E971]"></div>
            <div class="flex items-center gap-2 mb-4">
                <span class="w-1.5 h-1.5 bg-[#D4E971] rounded-full animate-pulse"></span>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Omzet</p>
            </div>
            <h4 class="text-2xl font-black text-[#D4E971] tracking-tight">Rp {{ number_format($summary['total_omzet']) }}</h4>
            <p class="text-[10px] text-slate-500 font-bold mt-3 uppercase tracking-widest">Cash + Online</p>
        </div>
    </div>

    {{-- Tabel Monitoring --}}
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">

        {{-- Table Header --}}
        <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Monitoring Pesanan</p>
                <h3 class="text-sm font-black text-slate-800 tracking-tight">
                    {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}
                    @if($startDate != $endDate)
                    — {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                    @endif
                </h3>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                    {{ $orders->count() }} Transaksi
                </span>
                <span class="bg-slate-900 text-[#D4E971] font-black text-[10px] uppercase tracking-widest py-2 px-4 rounded-xl">
                    Live Feed
                </span>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="table table-zebra w-full">
                <thead class="bg-slate-50">
                    <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">
                        <th class="py-5 pl-8 text-center w-12">No</th>
                        <th>Order ID</th>
                        <th>Pelanggan / Meja</th>
                        <th>Metode Bayar</th>
                        <th>Status</th>
                        <th>Kasir</th>
                        <th class="text-right pr-8">Nominal</th>
                        <th class="text-center">Waktu</th>
                    </tr>
                </thead>
                <tbody class="text-slate-600">
                    @forelse($orders as $index => $order)
                    <tr class="hover:bg-slate-50 transition-colors border-b border-slate-50">

                        <td class="py-5 pl-8 text-center text-[10px] font-bold text-slate-300">
                            {{ $index + 1 }}
                        </td>

                        <td>
                            <span class="px-3 py-1 bg-slate-100 rounded-lg text-[10px] font-black text-slate-500 uppercase">
                                #{{ $order->id }}
                            </span>
                        </td>

                        <td>
                            <span class="font-black text-slate-800 text-sm block uppercase tracking-tight">
                                {{ $order->customer_name }}
                            </span>
                            <span class="text-[9px] uppercase tracking-wider text-slate-400">
                                {{ $order->table?->table_number ?? 'Take Away' }}
                            </span>
                        </td>

                        <td>
                            @if($order->payment)
                            <span class="px-3 py-1 rounded-lg text-[9px] font-black uppercase
                                        {{ $order->payment->method === 'cash' ? 'bg-emerald-100 text-emerald-700' : 'bg-indigo-100 text-indigo-700' }}">
                                {{ $order->payment->method }}
                            </span>
                            @else
                            <span class="text-[9px] font-black text-rose-400 uppercase">Belum Bayar</span>
                            @endif
                        </td>

                        <td>
                            @php
                            $statusColor = match($order->status) {
                            'paid' => 'bg-emerald-100 text-emerald-700',
                            'preparing' => 'bg-amber-100 text-amber-700',
                            'done' => 'bg-blue-100 text-blue-700',
                            'cancelled' => 'bg-rose-600 text-white',
                            default => 'bg-slate-100 text-slate-500',
                            };
                            @endphp
                            <span class="px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest {{ $statusColor }}">
                                {{ $order->status }}
                            </span>
                        </td>

                        <td class="text-[10px] font-bold text-slate-400 uppercase tracking-tight">
                            {{ $order->cashier?->name ?? 'Self-Order' }}
                        </td>

                        <td class="text-right pr-8 font-black text-slate-900">
                            Rp {{ number_format($order->total_price) }}
                        </td>

                        <td class="text-center">
                            <span class="font-black text-slate-800 text-xs block">
                                {{ $order->created_at->format('d/m/Y') }}
                            </span>
                            <span class="text-[9px] text-slate-400">
                                {{ $order->created_at->format('H:i:s') }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-24 text-center">
                            <p class="font-black text-slate-300 uppercase tracking-[0.3em] text-xs">
                                Tidak ada data pada rentang tanggal ini
                            </p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

</x-layouts.admin>