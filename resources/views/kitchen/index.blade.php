<x-layouts.kitchen title="Kitchen Display System">
    <div class="min-h-screen bg-slate-900 p-4 md:p-8">

        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row justify-between items-center mb-10 gap-4">
            <div>
                <h1 class="text-3xl font-black text-white uppercase italic tracking-tighter">
                    Kitchen <span class="text-[#D4E971] not-italic">Orders.</span>
                </h1>
                <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.3em]">Kopi Tembalang System</p>
            </div>

            <div class="flex items-center gap-6">
                <div class="text-right">
                    {{-- JAM REAL-TIME --}}
                    <div id="clock" class="text-[#D4E971] font-mono text-3xl font-black tracking-tighter">00:00:00</div>
                    <p class="text-slate-500 text-[9px] font-bold uppercase tracking-widest">{{ now()->format('d F Y') }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="bg-red-500/10 hover:bg-red-500 text-red-500 hover:text-white p-3 rounded-xl transition-all group">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>

        {{-- GRID PESANAN --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" id="orders-container">
            @forelse($orders as $order)
            <div class="bg-white rounded-[2.5rem] shadow-2xl flex flex-col overflow-hidden border-4 border-transparent hover:border-[#D4E971] transition-all duration-300">

                {{-- CARD HEADER --}}
                <div class="p-5 {{ $order->order_type == 'takeaway' ? 'bg-orange-500 text-white' : 'bg-[#D4E971] text-slate-900' }} flex justify-between items-center">
                    <div>
                        <span class="text-[9px] font-black uppercase tracking-[0.2em] opacity-70">
                            {{ $order->order_type }}
                        </span>
                        <h2 class="text-2xl font-black uppercase leading-tight">
                            {{ $order->table ? 'Meja ' . $order->table->table_number : 'Take Away' }}
                        </h2>
                    </div>
                    <div class="bg-black/10 px-3 py-2 rounded-xl text-center">
                        <span class="text-[10px] font-black block leading-none">MASUK</span>
                        <span class="text-xs font-black">{{ $order->created_at->format('H:i') }}</span>
                    </div>
                </div>

                {{-- CARD BODY (ORDER ITEMS) --}}
                <div class="p-6 flex-1">
                    <div class="mb-4 flex items-center justify-between border-b border-slate-100 pb-2">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Menu Pesanan</span>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Qty</span>
                    </div>

                    <ul class="space-y-5">
                        @foreach($order->items as $item)
                        <li class="border-b border-slate-50 pb-4 last:border-0">
                            <div class="flex justify-between items-start">
                                <div class="max-w-[85%]">
                                    <p class="font-black text-base text-slate-800 uppercase leading-tight">{{ $item->menu->name }}</p>

                                    {{-- OPSI / TOPPING --}}
                                    {{-- OPSI / TOPPING --}}
                                    @if($item->options->count() > 0)
                                    @php
                                    $groupedOptions = $item->options->groupBy(function ($opt) {
                                    return $opt->optionItem->option->name ?? 'Opsi';
                                    });
                                    @endphp

                                    <div class="mt-2 space-y-2">
                                        @foreach($groupedOptions as $groupName => $optsInGroup)
                                        <div>
                                            <span class="text-[8px] font-black uppercase text-slate-400 tracking-widest block mb-1">{{ $groupName }}</span>

                                            <div class="flex flex-wrap gap-1">
                                                @foreach($optsInGroup->groupBy('menu_option_item_id') as $itemOptions)
                                                @php
                                                $optItem = $itemOptions->first()->optionItem;
                                                $qtyOpt = $itemOptions->count();
                                                @endphp
                                                <span class="text-[9px] font-bold bg-slate-100 text-slate-600 px-2 py-0.5 rounded-md border border-slate-200">
                                                    + {{ $optItem->name }}{{ $qtyOpt > 1 ? ' x' . $qtyOpt : '' }}
                                                </span>
                                                @endforeach
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    @endif

                                    {{-- CATATAN PER ITEM --}}
                                    @if($item->notes)
                                    <div class="mt-2 flex items-start gap-2 bg-blue-50 p-2 rounded-lg border border-blue-100">
                                        <span class="text-xs">💬</span>
                                        <p class="text-[10px] italic text-blue-700 font-bold leading-tight">"{{ $item->notes }}"</p>
                                    </div>
                                    @endif
                                </div>
                                <span class="bg-slate-900 text-[#D4E971] w-7 h-7 flex items-center justify-center rounded-lg font-black text-xs shadow-md">
                                    {{ $item->qty }}
                                </span>
                            </div>
                        </li>
                        @endforeach
                    </ul>

                    {{-- CATATAN PESANAN GLOBAL --}}
                    @if($order->notes)
                    <div class="mt-6 pt-4 border-t-2 border-dashed border-slate-100">
                        <p class="text-[9px] font-black text-slate-400 uppercase mb-1 tracking-widest text-center">Global Notes</p>
                        <p class="text-xs font-black text-red-500 italic text-center">{{ $order->notes }}</p>
                    </div>
                    @endif
                </div>

                {{-- CARD FOOTER (ACTION) --}}
                <div class="p-4 bg-slate-50 border-t border-slate-100">
                    <form action="{{ route('kitchen.ready', $order->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-slate-900 hover:bg-[#D4E971] text-[#D4E971] hover:text-slate-900 py-4 rounded-2xl font-black uppercase tracking-[0.2em] text-[11px] transition-all flex items-center justify-center gap-3 active:scale-95">
                            Selesai Masak
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="col-span-full flex flex-col items-center justify-center py-40">
                <div class="text-7xl mb-6 grayscale opacity-20">☕</div>
                <p class="text-white/20 font-black uppercase tracking-[0.5em] italic">No Orders Yet</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- AUDIO NOTIF --}}
    <audio id="notif-sound" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3"></audio>

    <script>
        // 1. UPDATE JAM REAL-TIME
        function updateClock() {
            const now = new Date();
            const time = now.getHours().toString().padStart(2, '0') + ":" +
                now.getMinutes().toString().padStart(2, '0') + ":" +
                now.getSeconds().toString().padStart(2, '0');
            const clockEl = document.getElementById('clock');
            if (clockEl) clockEl.innerText = time;
        }
        setInterval(updateClock, 1000);
        updateClock();

        // 2. AUTO REFRESH (Polling)
        let currentOrderCount = {{ $orders->count() }};

        function checkNewOrders() {
            fetch(window.location.href)
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newContainer = doc.getElementById('orders-container');
                    const newCount = doc.querySelectorAll('.order-card-class-selector-if-exist').length;
                    // Catatan: Karena kita me-refresh UI secara halus, kita cek jumlah card

                    // Cara termudah: reload jika jumlah berubah untuk memicu bunyi notif
                    // Atau update innerHTML saja:
                    document.getElementById('orders-container').innerHTML = newContainer.innerHTML;

                    // Cek jika jumlah pesanan bertambah (dibandingkan variabel JS)
                    // (Logika reload lebih aman untuk memastikan notifikasi bunyi)
                })
                .catch(err => console.error("Error refreshing orders:", err));
        }

        // Set refresh setiap 15 detik
        setInterval(() => {
            window.location.reload();
        }, 15000);
    </script>
</x-layouts.kitchen>