<x-layouts.cashier title="Manajemen Pembayaran">

    <style>
        /* Modern Industrial Transitions */
        .order-card {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .order-card:hover {
            transform: translateY(-8px) scale(1.01);
            box-shadow: 0 20px 40px -10px rgba(212, 233, 113, 0.15);
        }

        /* Scan Input Focus Effect */
        #scanner-input:focus {
            box-shadow: 0 0 0 4px rgba(212, 233, 113, 0.3);
            background-color: white;
        }

        /* Camera Scanner Custom UI */
        #reader {
            border: none !important;
            background: #f9fafb !important;
        }
        #reader__dashboard_section_csr button {
            background: #1a1a1a !important;
            color: #D4E971 !important;
            border: 2px solid #D4E971 !important;
            padding: 0.75rem 1.5rem !important;
            border-radius: 1rem !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            font-weight: 800 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.1em !important;
            font-size: 0.7rem !important;
            transition: all 0.2s !important;
        }
        #reader__dashboard_section_csr button:hover {
            background: #D4E971 !important;
            color: #1a1a1a !important;
        }

        /* Status Animation */
        .ping-green {
            animation: ping 1.5s cubic-bezier(0, 0, 0.2, 1) infinite;
        }
    </style>

    <div class="p-6 md:p-10 max-w-[1600px] mx-auto">

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            
            {{-- ================= KOLOM KIRI: SCANNER AREA (Industrial Box) ================= --}}
            <div class="lg:col-span-4 xl:col-span-3">
                
                {{-- 1. MAIN SCANNER UNIT --}}
                <div class="bg-[#1a1a1a] p-8 rounded-[2.5rem] border-4 border-black shadow-2xl relative overflow-hidden mb-8 group">
                    {{-- Decorative Scanning Line --}}
                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-[#D4E971] to-transparent animate-pulse shadow-[0_0_15px_#D4E971]"></div>
                    
                    <div class="relative flex flex-col items-center text-center">
                        <div class="w-16 h-16 bg-[#D4E971] rounded-2xl shadow-[0_0_20px_rgba(212,233,113,0.4)] mb-6 flex items-center justify-center text-black">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 7V5a2 2 0 012-2h2m10 0h2a2 2 0 012 2v2m0 10v2a2 2 0 01-2 2h-2M7 21H5a2 2 0 01-2-2v-2M12 7v10m0-10l-3 3m3-3l3 3"></path>
                            </svg>
                        </div>
                        
                        <h2 class="text-[#D4E971] font-black uppercase tracking-[0.2em] text-xs mb-2">System Ready</h2>
                        <h1 class="text-white text-2xl font-black uppercase italic mb-8 tracking-tighter">Scan <span class="text-[#D4E971] not-italic">Order.</span></h1>
                        
                        <form method="POST" action="{{ route('cashier.payments.scan') }}" id="scan-form" class="w-full">
                            @csrf
                            <div class="relative">
                                <input type="text" name="code" id="scanner-input"
                                    placeholder="WAITING..."
                                    class="w-full px-6 py-6 bg-white/5 border-2 border-white/10 rounded-2xl text-center text-2xl font-black text-[#D4E971] tracking-[0.3em] transition-all outline-none placeholder:text-white/10"
                                    autofocus autocomplete="off">
                                
                                <div class="absolute inset-y-0 right-5 flex items-center">
                                    <span class="flex h-3 w-3 relative">
                                        <span class="ping-green absolute inline-flex h-full w-full rounded-full bg-[#D4E971] opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-3 w-3 bg-[#D4E971]"></span>
                                    </span>
                                </div>
                            </div>
                        </form>
                        
                        <div class="mt-8 flex gap-2 items-center justify-center py-2 px-4 bg-white/5 rounded-full">
                            <div class="w-1.5 h-1.5 bg-[#D4E971] rounded-full"></div>
                            <p class="text-[9px] text-white/40 font-black uppercase tracking-[0.2em]">Laser Focus Mode Active</p>
                        </div>
                    </div>
                </div>

                {{-- 2. CAMERA BACKUP --}}
                <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm sticky top-10">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-2 h-2 bg-slate-300 rounded-full"></div>
                        <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Cadangan Kamera</h3>
                    </div>
                    <div class="rounded-3xl overflow-hidden bg-slate-50 border-2 border-dashed border-slate-100 p-2">
                        <div id="reader" class="w-full rounded-2xl overflow-hidden"></div>
                    </div>
                </div>
            </div>

            {{-- ================= KOLOM KANAN: ORDERS LIST ================= --}}
            <div class="lg:col-span-8 xl:col-span-9">
                
                {{-- PENDING BILLS --}}
                <div class="mb-14">
                    <div class="flex items-end justify-between mb-8 pb-4 border-b border-slate-100">
                        <div>
                            <h1 class="text-3xl font-black text-slate-800 uppercase italic tracking-tighter">Antrean <span class="text-[#D4E971] not-italic">Tagihan.</span></h1>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Selesaikan pembayaran untuk memproses pesanan</p>
                        </div>
                        <div class="bg-black text-[#D4E971] px-4 py-2 rounded-xl font-black text-xs tracking-widest">
                            {{ $pendingOrders->count() }} PENDING
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-6">
                        @forelse($pendingOrders as $order)
                        <div class="order-card bg-white rounded-[2rem] p-7 border border-slate-100 shadow-sm relative overflow-hidden flex flex-col">
                            <div class="flex justify-between items-start mb-8">
                                <div class="px-3 py-1 bg-slate-100 rounded-lg">
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">#{{ $order->id }}</span>
                                </div>
                                <div class="flex items-center gap-2 px-3 py-1 bg-amber-50 rounded-lg border border-amber-100">
                                    <span class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse"></span>
                                    <span class="text-[9px] font-black text-amber-700 uppercase">UNPAID</span>
                                </div>
                            </div>

                            <div class="mb-8">
                                <h2 class="text-xl font-black text-slate-800 tracking-tight leading-tight uppercase">{{ $order->customer_name ?? 'Walk In Guest' }}</h2>
                                <div class="flex items-center gap-2 mt-2">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $order->order_type }}</span>
                                    @if($order->order_type == 'dine_in')
                                        <span class="w-1 h-1 bg-slate-200 rounded-full"></span>
                                        <span class="text-[10px] font-black text-[#D4E971] uppercase tracking-widest">Meja {{ $order->table->table_number ?? '?' }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-auto pt-6 border-t border-slate-50 flex items-center justify-between">
                                <div>
                                    <p class="text-[9px] font-black text-slate-300 uppercase tracking-[0.2em] mb-1">Total Billing</p>
                                    <h3 class="text-2xl font-black text-slate-900 italic">
                                        <span class="text-xs font-normal not-italic opacity-40">Rp</span>{{ number_format($order->total_price) }}
                                    </h3>
                                </div>
                                
                                <form method="POST" action="{{ route('cashier.payments.scan') }}">
                                    @csrf
                                    <input type="hidden" name="code" value="{{ $order->id }}">
                                    <button class="bg-black hover:bg-[#D4E971] text-[#D4E971] hover:text-black w-14 h-14 rounded-2xl transition-all duration-300 shadow-xl flex items-center justify-center group">
                                        <svg class="w-6 h-6 transition-transform group-hover:rotate-12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @empty
                        <div class="col-span-full py-20 text-center bg-slate-50/50 rounded-[3rem] border-4 border-dashed border-slate-100 flex flex-col items-center">
                            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center text-slate-300 mb-4">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                            </div>
                            <p class="text-slate-400 text-[10px] font-black uppercase tracking-[0.3em] italic">Antrean Tagihan Bersih</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                {{-- RECENT HISTORY --}}
                <div class="bg-slate-900 rounded-[3rem] p-10 relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-12 opacity-5">
                        <svg class="w-64 h-64 text-[#D4E971]" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                    </div>
                    
                    <h1 class="text-lg font-black text-white uppercase tracking-[0.2em] mb-8 flex items-center gap-4">
                        <span class="w-8 h-[2px] bg-[#D4E971]"></span> 
                        Riwayat Transaksi Hari Ini
                    </h1>
                    
                    <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-4">
                        @forelse($paidOrders as $order)
                        <div class="bg-white/5 border border-white/10 p-5 rounded-2xl flex items-center justify-between group hover:bg-white/10 transition-all duration-300">
                            <div>
                                <h2 class="font-black text-white text-xs uppercase tracking-tight">{{ $order->customer_name ?? 'Walk In' }}</h2>
                                <p class="text-[9px] font-bold text-white/30 mt-1 uppercase tracking-widest">{{ $order->updated_at->format('H:i') }} • SUCCESS</p>
                            </div>
                            <div class="text-right">
                                <h3 class="text-sm font-black text-[#D4E971] italic">Rp{{ number_format($order->total_price) }}</h3>
                            </div>
                        </div>
                        @empty
                        <p class="col-span-full text-white/20 text-[10px] font-black text-center uppercase tracking-widest py-4 italic">Belum ada riwayat transaksi</p>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- SCRIPTS --}}
    <script src="https://unpkg.com/html5-qrcode"></script>

    <script>
        const scannerInput = document.getElementById('scanner-input');
        const scanForm = document.getElementById('scan-form');

        // Industrial Auto-submit
        scannerInput.addEventListener('input', function() {
            if (this.value.length > 0) {
                this.classList.add('opacity-50');
                setTimeout(() => { scanForm.submit(); }, 150);
            }
        });

        // Anti-focus Loss
        document.addEventListener('click', () => scannerInput.focus());
        scannerInput.addEventListener('blur', () => {
            setTimeout(() => { scannerInput.focus(); }, 10);
        });

        // Camera Logic
        function onScanSuccess(decodedText) {
            html5QrcodeScanner.clear();
            fetch("{{ route('cashier.payments.scan') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ code: decodedText })
            }).then(() => {
                window.location.reload();
            });
        }

        let html5QrcodeScanner = new Html5QrcodeScanner("reader", { 
            fps: 15, 
            qrbox: {width: 250, height: 250},
            aspectRatio: 1.0,
            showTorchButtonIfSupported: true
        });
        html5QrcodeScanner.render(onScanSuccess);
    </script>

</x-layouts.cashier>