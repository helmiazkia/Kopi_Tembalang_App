{{-- customer/payment/success.blade.php --}}
<x-layouts.customer>
    <div class="max-w-md mx-auto p-6 text-center">
        <div class="w-20 h-20 bg-[#D4E971] rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"></path></svg>
        </div>
        
        <h1 class="text-2xl font-black italic uppercase">Pesanan Diterima!</h1>
        <p class="text-xs text-slate-400 mt-2">Mohon tunggu, pesanan anda sedang kami proses.</p>

        <div class="mt-8 p-6 bg-white rounded-[2rem] border border-slate-100 shadow-xl text-left">
            <div class="flex justify-between mb-4 border-b pb-4 border-dashed">
                <span class="text-[10px] font-black uppercase text-slate-400">Order ID</span>
                <span class="text-xs font-black">#{{ $order->id }}</span>
            </div>

            <div class="space-y-4">
                @foreach($order->items as $item)
                <div class="flex justify-between items-center">
                    <div>
                        <h4 class="text-xs font-black uppercase">{{ $item->menu->name }}</h4>
                        <p class="text-[10px] text-slate-400">{{ $item->qty }}x @ Rp{{ number_format($item->price) }}</p>
                    </div>
                    <span class="text-xs font-black">Rp{{ number_format($item->subtotal) }}</span>
                </div>
                @endforeach
            </div>

            <div class="mt-6 pt-4 border-t-2 border-dashed border-slate-100 flex justify-between items-end">
                <span class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Total Bayar</span>
                <strong class="text-xl font-black italic">Rp{{ number_format($order->total_price) }}</strong>
            </div>
        </div>

        <div class="mt-10">
            <span class="text-[10px] font-black uppercase text-slate-300 block mb-2">Status Pesanan</span>
            <div class="px-6 py-3 bg-slate-900 text-[#D4E971] rounded-full inline-block font-black uppercase text-[10px] tracking-widest">
                {{ strtoupper($order->status) }}
            </div>
        </div>
    </div>
</x-layouts.customer>