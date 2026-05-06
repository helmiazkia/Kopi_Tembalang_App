{{-- customer/payment/success.blade.php --}}
<x-layouts.customer :title="'Pesanan Berhasil - #' . $order->id">
    <div class="max-w-md mx-auto p-8 text-center min-h-screen flex flex-col justify-center bg-white">
        
        <!-- Ikon Sukses dengan Efek Luminous -->
        <div class="relative w-24 h-24 mx-auto mb-10">
            <div class="absolute inset-0 bg-[#D4E971] rounded-full animate-ping opacity-25"></div>
            <div class="relative w-24 h-24 bg-[#D4E971] rounded-full flex items-center justify-center shadow-[0_0_30px_rgba(212,233,113,0.5)]">
                <svg class="w-10 h-10 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
        </div>
        
        <h1 class="text-4xl font-black italic uppercase tracking-tighter text-slate-900 leading-none">
            Pesanan<br><span class="text-[#D4E971]">Diterima!</span>
        </h1>
        <p class="text-[10px] font-black text-slate-400 mt-4 uppercase tracking-[0.3em] leading-relaxed">
            Terima kasih atas pesananmu,<br>mohon tunggu sebentar.
        </p>

        <!-- Struk Digital Modern -->
        <div class="mt-10 bg-slate-50 rounded-[3rem] border border-slate-100 relative overflow-hidden text-left shadow-2xl shadow-slate-200/50">
            <!-- Dekorasi Notch Struk -->
            <div class="absolute -top-3 left-1/2 -translate-x-1/2 w-12 h-6 bg-white rounded-full"></div>
            
            <div class="p-8 pt-10">
                <div class="flex justify-between items-end mb-8 border-b border-dashed pb-6 border-slate-200">
                    <div>
                        <span class="text-[8px] font-black uppercase text-slate-400 block tracking-[0.2em] mb-1">Nomor Meja</span>
                        <span class="text-lg font-black text-slate-900 italic uppercase">Meja {{ $order->table->table_number }}</span>
                    </div>
                    <div class="text-right">
                        <span class="text-[8px] font-black uppercase text-slate-400 block tracking-[0.2em] mb-1">ID Pesanan</span>
                        <span class="text-xs font-black text-slate-900">#{{ $order->id }}</span>
                    </div>
                </div>

                <!-- Item Pesanan dengan Tipografi Rapi -->
                <div class="space-y-6">
                    @foreach($order->items as $item)
                    <div class="flex justify-between items-start">
                        <div class="flex-1 pr-6">
                            <h4 class="text-[11px] font-black uppercase text-slate-800 leading-tight tracking-tight">
                                {{ $item->menu->name }}
                            </h4>
                            @if($item->notes)
                                <div class="flex items-center gap-1.5 mt-1.5">
                                    <span class="text-[10px]">💬</span>
                                    <p class="text-[9px] text-blue-500 font-bold italic leading-none">"{{ $item->notes }}"</p>
                                </div>
                            @endif
                            <p class="text-[10px] font-bold text-slate-400 mt-2 tracking-wide">
                                {{ $item->qty }}x <span class="opacity-50">@</span> Rp{{ number_format($item->price, 0, ',', '.') }}
                            </p>
                        </div>
                        <span class="text-xs font-black text-slate-900 italic tracking-tighter">
                            Rp{{ number_format($item->subtotal, 0, ',', '.') }}
                        </span>
                    </div>
                    @endforeach
                </div>

                <!-- Total dengan High Contrast -->
                <div class="mt-10 pt-6 border-t-2 border-dashed border-slate-200">
                    <div class="flex justify-between items-center bg-slate-900 p-5 rounded-2xl shadow-xl">
                        <span class="text-[9px] font-black uppercase text-[#D4E971]/60 tracking-[0.2em]">Total Bayar</span>
                        <strong class="text-xl font-black italic text-white tracking-tighter">
                            Rp{{ number_format($order->total_price, 0, ',', '.') }}
                        </strong>
                    </div>
                </div>
            </div>
            
            <!-- Dekorasi Notch Bawah -->
            <div class="absolute -bottom-3 left-1/2 -translate-x-1/2 w-12 h-6 bg-white rounded-full"></div>
        </div>

        <!-- Status & Navigation -->
        <div class="mt-12 space-y-6">
            <div class="inline-flex items-center gap-4 px-6 py-3 bg-white border border-slate-100 rounded-full shadow-sm">
                <span class="text-[9px] font-black uppercase text-slate-400 tracking-widest">Status:</span>
                <div class="flex items-center gap-2">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#D4E971] opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-[#D4E971]"></span>
                    </span>
                    <span class="font-black uppercase text-[10px] tracking-widest text-slate-900">
                        {{ strtoupper($order->status) }}
                    </span>
                </div>
            </div>

            <div class="pt-4 pb-8">
                <a href="{{ route('customer.menu', $order->table_id) }}" 
                   class="group inline-flex items-center justify-center gap-3 text-[11px] font-black uppercase tracking-[0.2em] text-slate-400 hover:text-slate-900 transition-all duration-300">
                    <div class="w-8 h-8 rounded-full border border-slate-100 flex items-center justify-center group-hover:bg-slate-900 group-hover:text-[#D4E971] transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                    </div>
                    Pesan Lagi
                </a>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        /**
         * PENTING: Kosongkan keranjang di LocalStorage
         * agar saat user klik "Pesan Lagi", tidak ada residu data lama.
         */
        document.addEventListener('DOMContentLoaded', function() {
            localStorage.removeItem('cart');
            console.log('🛒 LocalStorage: Cart has been cleared.');
        });
    </script>
    @endpush
</x-layouts.customer>