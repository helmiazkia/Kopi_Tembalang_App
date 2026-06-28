<x-layouts.admin title="Dashboard">


    {{-- Header Bulanan --}}
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 bg-white p-8 rounded-3xl shadow-sm border border-slate-200 mb-5">
        <div>
            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400 mb-1">Kopi Tembalang Intelligence</p>
            <h2 class="text-2xl font-black text-slate-900 tracking-tight">Ringkasan Bisnis Bulanan</h2>
        </div>

        <form action="{{ route('admin.dashboard') }}" method="GET" class="flex flex-wrap gap-3 w-full lg:w-auto">
            <select name="month" class="select select-bordered bg-white border-slate-200 text-slate-600 rounded-2xl text-sm font-semibold">
                @foreach(range(1, 12) as $m)
                <option value="{{ $m }}" {{ $filterMonth == $m ? 'selected' : '' }}>
                    {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                </option>
                @endforeach
            </select>
            <select name="year" class="select select-bordered bg-white border-slate-200 text-slate-600 rounded-2xl text-sm font-semibold">
                @foreach(range(now()->year - 2, now()->year) as $y)
                <option value="{{ $y }}" {{ $filterYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn bg-slate-900 hover:bg-slate-700 text-[#D4E971] border-none rounded-2xl px-8 font-black text-xs tracking-widest transition-all duration-300">
                Terapkan
            </button>
        </form>
    </div>

    {{-- Kartu Statistik Bulanan --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

        {{-- Omzet --}}
        <div class="bg-slate-900 p-7 rounded-3xl border border-slate-800 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1 bg-[#D4E971]"></div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Omzet Bulan Ini</p>
            <h2 class="text-2xl font-black text-white tracking-tight">Rp {{ number_format($totalRevenue) }}</h2>
            <div class="mt-4 flex items-center gap-2 text-[10px] font-black text-[#D4E971] bg-[#D4E971]/10 w-fit px-3 py-1.5 rounded-xl">
                <span class="w-1.5 h-1.5 bg-[#D4E971] rounded-full animate-pulse"></span>
                Real-time
            </div>
        </div>

        {{-- Pesanan Kasir --}}
        <div class="bg-white p-7 rounded-3xl border border-slate-200 shadow-sm flex items-center gap-5">
            <div class="p-4 bg-indigo-50 text-indigo-600 rounded-2xl shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Pesanan Kasir</p>
                <h3 class="text-2xl font-black text-slate-800">
                    {{ $orderKasir ?? 0 }}
                    <span class="text-xs font-medium text-slate-400">Nota</span>
                </h3>
            </div>
        </div>

        {{-- User Mandiri --}}
        <div class="bg-white p-7 rounded-3xl border border-slate-200 shadow-sm flex items-center gap-5">
            <div class="p-4 bg-purple-50 text-purple-600 rounded-2xl shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">User Mandiri</p>
                <h3 class="text-2xl font-black text-slate-800">
                    {{ $orderMandiri ?? 0 }}
                    <span class="text-xs font-medium text-slate-400">Nota</span>
                </h3>
            </div>
        </div>

        {{-- Produk Terjual --}}
        <div class="bg-white p-7 rounded-3xl border border-slate-200 shadow-sm flex items-center gap-5">
            <div class="p-4 bg-rose-50 text-rose-600 rounded-2xl shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Produk Terjual</p>
                <h3 class="text-2xl font-black text-slate-800">
                    {{ number_format($totalItemsSold ?? 0) }}
                    <span class="text-xs font-medium text-slate-400">Item</span>
                </h3>
            </div>
        </div>
    </div>

    {{-- Row 2: Grafik --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-8">

        {{-- Jam Sibuk --}}
        <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm">
            <div class="mb-6">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Analisis Trafik 24 Jam</p>
                <h3 class="text-lg font-black text-slate-800 tracking-tight">Jam Sibuk Pemesanan</h3>
            </div>
            <div class="h-72 w-full">
                <canvas id="peakHoursChart"></canvas>
            </div>
        </div>

        {{-- Tren Mingguan --}}
        <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm">
            <div class="mb-6">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Volume Pelanggan per Hari</p>
                <h3 class="text-lg font-black text-slate-800 tracking-tight">Tren Kunjungan Mingguan</h3>
            </div>
            <div class="h-72 w-full">
                <canvas id="busyDaysChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Row 3: Rasio & Menu Terlaris --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Rasio Sumber Pesanan --}}
        <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm flex flex-col">
            <div class="mb-8">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Distribusi</p>
                <h3 class="text-lg font-black text-slate-800 tracking-tight">Rasio Sumber Pesanan</h3>
            </div>

            @php
            $valKasir = $orderKasir ?? 0;
            $valMandiri = $orderMandiri ?? 0;
            $total = ($valKasir + $valMandiri) ?: 1;
            $perKasir = ($valKasir / $total) * 100;
            $perMandiri = ($valMandiri / $total) * 100;
            @endphp

            <div class="space-y-7 flex-1">
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-black text-slate-700">Pelayanan Kasir</span>
                        <span class="text-sm font-black text-indigo-600">{{ round($perKasir) }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 h-3 rounded-full overflow-hidden">
                        <div class="bg-indigo-500 h-full rounded-full transition-all duration-1000" style="width: {{ $perKasir }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-black text-slate-700">Self-Order (Meja)</span>
                        <span class="text-sm font-black text-purple-600">{{ round($perMandiri) }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 h-3 rounded-full overflow-hidden">
                        <div class="bg-purple-500 h-full rounded-full transition-all duration-1000" style="width: {{ $perMandiri }}%"></div>
                    </div>
                </div>
            </div>

            <div class="mt-8 p-5 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                <p class="text-[11px] text-slate-400 leading-relaxed text-center font-medium">
                    Gunakan data ini untuk mengatur distribusi staf antara area meja dan kasir.
                </p>
            </div>
        </div>

        {{-- 5 Menu Terlaris --}}
        {{-- 5 Menu Terlaris --}}
        <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm lg:col-span-2">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Bulan Ini</p>
                    <h3 class="text-lg font-black text-slate-800 tracking-tight">
                        {{ $menuSort === 'top' ? '5 Menu Terlaris' : '5 Menu Paling Jarang Dipesan' }}
                    </h3>
                </div>

                <div class="flex items-center gap-3">
                    {{-- ✅ Toggle Filter --}}
                    <form action="{{ route('admin.dashboard') }}" method="GET" class="flex items-center gap-2">
                        {{-- Pertahankan filter bulan & tahun yang aktif --}}
                        <input type="hidden" name="month" value="{{ $filterMonth }}">
                        <input type="hidden" name="year" value="{{ $filterYear }}">

                        <button type="submit" name="menu_sort" value="top"
                            class="px-3 py-1.5 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all
                           {{ $menuSort === 'top' ? 'bg-slate-900 text-[#D4E971]' : 'bg-slate-100 text-slate-400 hover:bg-slate-200' }}">
                            ▲ Teratas
                        </button>
                        <button type="submit" name="menu_sort" value="bottom"
                            class="px-3 py-1.5 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all
                           {{ $menuSort === 'bottom' ? 'bg-slate-900 text-[#D4E971]' : 'bg-slate-100 text-slate-400 hover:bg-slate-200' }}">
                            ▼ Terbawah
                        </button>
                    </form>
                </div>
            </div>

            {{-- List menu (tidak berubah) --}}
            <div class="space-y-3">
                @foreach($topMenus as $menu)
                <div class="flex items-center justify-between p-5 bg-slate-50 rounded-2xl border border-slate-100 hover:bg-white hover:border-[#D4E971] hover:shadow-sm transition-all duration-300 group">
                    <div class="flex items-center gap-5">
                        <span class="text-2xl font-black text-slate-200 group-hover:text-[#D4E971] transition-colors w-8">
                            {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                        </span>
                        <div>
                            <span class="font-black text-slate-800 block text-sm tracking-tight">{{ $menu->name }}</span>
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">
                                {{ $menuSort === 'top' ? 'Produk Unggulan' : 'Perlu Perhatian' }}
                            </span>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-xl font-black text-slate-900">{{ $menu->total_qty }}</span>
                        <span class="text-[10px] block text-slate-400 font-black uppercase tracking-widest mt-0.5">Terjual</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        Chart.defaults.color = '#94a3b8';
        Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
        Chart.defaults.font.weight = '700';

        // ── Jam Sibuk (Line) ──────────────────────────────────────────
        const ctxHour = document.getElementById('peakHoursChart').getContext('2d');
        const gradBlue = ctxHour.createLinearGradient(0, 0, 0, 280);
        gradBlue.addColorStop(0, 'rgba(99, 102, 241, 0.3)');
        gradBlue.addColorStop(1, 'rgba(99, 102, 241, 0)');

        new Chart(ctxHour, {
            type: 'line',
            data: {
                labels: Array.from({
                    length: 24
                }, (_, i) => `${i}:00`),
                datasets: [{
                    label: 'Jumlah Pesanan',
                    data: @json($hourValues),
                    borderColor: '#6366f1',
                    backgroundColor: gradBlue,
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#6366f1',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 3,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
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
                        grid: {
                            color: '#f1f5f9',
                            drawBorder: false
                        },
                        beginAtZero: true,
                        suggestedMax: Math.max(...@json($hourValues)) + 2,
                        ticks: {
                            stepSize: 1,
                            font: {
                                size: 10
                            }
                        },
                    },
                },
            },
        });

        // ── Tren Mingguan (Bar) ───────────────────────────────────────
        new Chart(document.getElementById('busyDaysChart'), {
            type: 'bar',
            data: {
                labels: @json($dayLabels),
                datasets: [{
                    data: @json($busyDaysValues),
                    backgroundColor: '#D4E971',
                    hoverBackgroundColor: '#bdd139',
                    borderRadius: 10,
                    barThickness: 28,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
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
                        grid: {
                            color: '#f1f5f9',
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                size: 10
                            }
                        }
                    },
                },
            },
        });
    </script>
    @endpush

</x-layouts.admin>