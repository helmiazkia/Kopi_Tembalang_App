<x-layouts.customer :title="'Pesanan Berhasil - #' . $order->id">
    <div class="max-w-md mx-auto p-6 min-h-screen flex flex-col bg-white">

        <div class="relative w-20 h-20 mx-auto mt-8 mb-6">
            <div class="absolute inset-0 bg-[#D4E971] rounded-full animate-ping opacity-20"></div>
            <div class="relative w-20 h-20 bg-[#D4E971] rounded-full flex items-center justify-center shadow-lg">
                <svg class="w-8 h-8 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
        </div>

        <div class="text-center mb-8">
            <h1 class="text-3xl font-black italic uppercase tracking-tighter text-slate-900 leading-none">
                Order<br><span class="text-[#D4E971]">Confirmed!</span>
            </h1>
        </div>

        <div class="bg-slate-900 rounded-[2rem] p-6 mb-8 shadow-xl">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-[10px] font-black uppercase text-[#D4E971] tracking-[0.2em]">Kitchen Progress</h3>
                <span id="status-label" class="px-3 py-1 bg-white/10 rounded-full text-[9px] font-black text-white uppercase tracking-widest">
                    {{ $order->status == 'pending' ? 'Antrean' : ($order->status == 'paid' ? 'Dimasak' : 'Siap') }}
                </span>
            </div>

            <div class="relative flex justify-between items-center">
                <div class="absolute top-1/2 left-0 w-full h-1 bg-white/10 -translate-y-1/2"></div>
                <div id="progress-line" class="absolute top-1/2 left-0 h-1 bg-[#D4E971] -translate-y-1/2 transition-all duration-1000"
                    style="width: {{ $order->status == 'done' ? '100%' : ($order->status == 'paid' ? '50%' : '10%') }}"></div>

                <div class="relative flex flex-col items-center gap-2">
                    <div id="step-1" class="w-4 h-4 rounded-full border-4 {{ $order->status != 'pending' ? 'bg-[#D4E971] border-slate-900' : 'bg-slate-800 border-slate-700' }}"></div>
                    <span class="text-[8px] font-black text-white/40 uppercase">Antrean</span>
                </div>
                <div class="relative flex flex-col items-center gap-2">
                    <div id="step-2" class="w-4 h-4 rounded-full border-4 {{ in_array($order->status, ['paid', 'done']) ? 'bg-[#D4E971] border-slate-900' : 'bg-slate-800 border-slate-700' }}"></div>
                    <span class="text-[8px] font-black text-white/40 uppercase">Dimasak</span>
                </div>
                <div class="relative flex flex-col items-center gap-2">
                    <div id="step-3" class="w-4 h-4 rounded-full border-4 {{ $order->status == 'done' ? 'bg-[#D4E971] border-slate-900' : 'bg-slate-800 border-slate-700' }}"></div>
                    <span class="text-[8px] font-black text-white/40 uppercase">Siap</span>
                </div>
            </div>
        </div>

        <div class="bg-slate-50 rounded-[2.5rem] border border-slate-100 p-6 flex-1 shadow-sm">
            <div class="flex justify-between items-end mb-6 border-b border-dashed pb-4 border-slate-200">
                <div>
                    <span class="text-[8px] font-black uppercase text-slate-400 block tracking-widest mb-1">Meja</span>
                    <span class="text-base font-black text-slate-900 italic uppercase">{{ $order->table->table_number }}</span>
                </div>
                <div class="text-right">
                    <span class="text-[8px] font-black uppercase text-slate-400 block tracking-widest mb-1">Receipt</span>
                    <span class="text-[10px] font-black text-slate-900">#{{ $order->id }}</span>
                </div>
            </div>

            <div class="space-y-4">
                @foreach($order->items as $item)
                <div class="border-b border-slate-100 pb-4 last:border-0">
                    <div class="flex justify-between items-start">
                        <div class="flex-1 pr-3">
                            <h4 class="text-[10px] font-black uppercase text-slate-800 leading-none">{{ $item->menu->name }}</h4>
                            <p class="text-[9px] font-bold text-slate-400 mt-1 italic">{{ $item->qty }}x</p>
                        </div>
                        <span class="text-[10px] font-black text-slate-900 whitespace-nowrap">
                            Rp{{ number_format($item->price * $item->qty, 0, ',', '.') }}
                        </span>
                    </div>

                    @if($item->options->count() > 0)
                    <div class="mt-2 pl-1 space-y-1">
                        @foreach($item->options->groupBy('menu_option_item_id') as $itemOptions)
                        @php
                        $optItem = $itemOptions->first()->optionItem;
                        $qtyOpt = $itemOptions->count();
                        $lineTotal = ($optItem->price ?? 0) * $qtyOpt;
                        @endphp
                        <div class="flex justify-between items-center">
                            <span class="text-[9px] font-bold text-slate-400">
                                + {{ $optItem->name ?? '-' }}{{ $qtyOpt > 1 ? ' x' . $qtyOpt : '' }}
                            </span>
                            <span class="text-[9px] font-bold text-slate-400 whitespace-nowrap">
                                {{ $lineTotal > 0 ? 'Rp' . number_format($lineTotal, 0, ',', '.') : 'Gratis' }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    @if($item->notes)
                    <p class="text-[8px] italic text-blue-500 mt-1.5 pl-1">"{{ $item->notes }}"</p>
                    @endif

                    <div class="flex justify-between items-center mt-2 pt-2 border-t border-dashed border-slate-200">
                        <span class="text-[8px] font-black uppercase text-slate-400">Subtotal</span>
                        <span class="text-[9px] font-black text-slate-700">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-8 pt-4 border-t-2 border-dashed border-slate-200">
                <div class="flex justify-between items-center">
                    <span class="text-[9px] font-black uppercase text-slate-400">Total</span>
                    <strong class="text-lg font-black italic text-slate-900">Rp{{ number_format($order->total_price, 0, ',', '.') }}</strong>
                </div>
            </div>
        </div>

        <div class="mt-8 mb-4 text-center">
            <a href="{{ route('customer.menu', $order->table_id) }}"
                class="inline-flex items-center gap-3 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 hover:text-slate-900 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Pesan Menu Lain
            </a>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Bersihkan Cart
            localStorage.removeItem('cart');

            // 2. Real-time Status Polling (Kitchen Progress)
            const orderId = "{{ $order->id }}";
            const statusLabel = document.getElementById('status-label');
            const progressLine = document.getElementById('progress-line');
            const step2 = document.getElementById('step-2');
            const step3 = document.getElementById('step-3');

            const checkKitchenStatus = setInterval(() => {
                fetch(`/payment/check/${orderId}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'paid') {
                            statusLabel.innerText = 'Dimasak';
                            progressLine.style.width = '50%';
                            step2.classList.replace('bg-slate-800', 'bg-[#D4E971]');
                            step2.classList.replace('border-slate-700', 'border-slate-900');
                        } else if (data.status === 'done') {
                            statusLabel.innerText = 'Siap Diantar';
                            statusLabel.classList.replace('bg-white/10', 'bg-[#D4E971]');
                            statusLabel.classList.replace('text-white', 'text-black');
                            progressLine.style.width = '100%';
                            step3.classList.replace('bg-slate-800', 'bg-[#D4E971]');
                            step3.classList.replace('border-slate-700', 'border-slate-900');

                            // Jika sudah done, berhenti polling setelah beberapa saat
                            // clearInterval(checkKitchenStatus); 
                        }
                    });
            }, 5000); // Cek setiap 5 detik
        });
    </script>
    @endpush
</x-layouts.customer>