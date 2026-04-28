<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <title>Checkout - Kopi Tembalang</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }
        .neo-shadow {
            box-shadow: 0 8px 0 0 #0f172a;
        }
        .neo-shadow:active {
            box-shadow: 0 0px 0 0 #0f172a;
            transform: translateY(8px);
        }
    </style>
</head>
<body class="bg-[#fafafa] pb-20">

    {{-- TOP NAVIGATION --}}
    <div class="p-6 flex items-center justify-between bg-white/80 backdrop-blur-lg sticky top-0 z-50 border-b border-slate-100">
        <a href="javascript:history.back()" class="w-10 h-10 flex items-center justify-center bg-slate-900 text-white rounded-full shadow-lg active:scale-90 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>
        <h1 class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-800">Checkout Pesanan</h1>
        <div class="w-10"></div>
    </div>

    <div class="max-w-lg mx-auto p-6">
        
        {{-- HEADER SECTION --}}
        <div class="mb-10 text-center">
            <div class="inline-block px-4 py-1 rounded-full bg-[#D4E971]/20 text-[#8ba11c] text-[10px] font-black uppercase tracking-widest mb-4">
                ☕ Order Confirmation
            </div>
            <h2 class="text-4xl font-black text-slate-900 italic leading-none uppercase tracking-tighter">
                Satu langkah lagi<br><span class="text-[#D4E971] drop-shadow-sm">Kopimu Siap.</span>
            </h2>
        </div>

        <form method="POST" action="{{ route('customer.checkout.store') }}" class="space-y-8">
            @csrf
            <input type="hidden" name="table_id" value="{{ $table->id }}">
            <input type="hidden" name="order_type" value="dine_in">

            {{-- DYNAMIC TABLE CARD --}}
            <div class="relative group">
                <div class="absolute -inset-1 bg-gradient-to-r from-[#D4E971] to-slate-900 rounded-[2.5rem] blur opacity-25 transition duration-1000"></div>
                <div class="relative bg-slate-900 rounded-[2.2rem] p-8 overflow-hidden shadow-2xl">
                    <div class="relative flex justify-between items-center">
                        <div>
                            <p class="text-[#D4E971]/60 text-[10px] font-black uppercase tracking-[0.2em] mb-1">Lokasi Antrean</p>
                            <h3 class="text-3xl font-black text-white italic uppercase tracking-tighter">Meja {{ $table->table_number }}</h3>
                        </div>
                        <div class="bg-[#D4E971] p-3 rounded-2xl rotate-12 shadow-lg">
                            <span class="text-2xl">📍</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- INPUT SECTION --}}
            <div class="bg-white rounded-[2.5rem] p-8 shadow-[0_10px_40px_rgba(0,0,0,0.03)] border border-slate-50 space-y-6">
                
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase text-slate-400 ml-4 tracking-widest">Nama Lengkap</label>
                    <input name="customer_name" placeholder="Tulis namamu..." required 
                        class="w-full px-8 py-5 rounded-2xl bg-slate-50 border-2 border-transparent focus:border-[#D4E971] focus:bg-white outline-none transition-all duration-300 font-bold text-slate-700 shadow-inner">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase text-slate-400 ml-4 tracking-widest">Email Notifikasi</label>
                    <input type="email" name="email" placeholder="kopi@tembalang.com" required 
                        class="w-full px-8 py-5 rounded-2xl bg-slate-50 border-2 border-transparent focus:border-[#D4E971] focus:bg-white outline-none transition-all duration-300 font-bold text-slate-700 shadow-inner">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase text-slate-400 ml-4 tracking-widest">Nomor WhatsApp</label>
                    <input name="phone" placeholder="0812xxxx" required 
                        class="w-full px-8 py-5 rounded-2xl bg-slate-50 border-2 border-transparent focus:border-[#D4E971] focus:bg-white outline-none transition-all duration-300 font-bold text-slate-700 shadow-inner">
                </div>

            </div>

            {{-- PAYMENT METHOD SELECTION --}}
            <div class="space-y-4">
                <label class="text-[10px] font-black uppercase text-slate-400 text-center block tracking-[0.3em]">Opsi Pembayaran</label>
                <div class="grid grid-cols-2 gap-4">
                    <label class="relative cursor-pointer group">
                        <input type="radio" name="payment_method" value="qris" class="peer hidden" checked>
                        <div class="p-6 bg-white border-2 border-slate-100 rounded-[2rem] peer-checked:border-[#D4E971] peer-checked:bg-[#D4E971]/5 transition-all duration-500 flex flex-col items-center gap-3">
                            <div class="text-3xl filter grayscale group-hover:grayscale-0 peer-checked:grayscale-0 transition-all">📱</div>
                            <span class="text-[10px] font-black uppercase text-slate-400 peer-checked:text-slate-900 tracking-tighter">Digital Pay</span>
                            <div class="absolute top-3 right-3 w-3 h-3 bg-slate-200 rounded-full peer-checked:bg-[#D4E971] peer-checked:scale-125 transition-all"></div>
                        </div>
                    </label>

                    <label class="relative cursor-pointer group">
                        <input type="radio" name="payment_method" value="cash" class="peer hidden">
                        <div class="p-6 bg-white border-2 border-slate-100 rounded-[2rem] peer-checked:border-slate-800 peer-checked:bg-slate-50 transition-all duration-500 flex flex-col items-center gap-3">
                            <div class="text-3xl filter grayscale group-hover:grayscale-0 peer-checked:grayscale-0 transition-all">💵</div>
                            <span class="text-[10px] font-black uppercase text-slate-400 peer-checked:text-slate-900 tracking-tighter">Ke Kasir</span>
                            <div class="absolute top-3 right-3 w-3 h-3 bg-slate-200 rounded-full peer-checked:bg-slate-800 peer-checked:scale-125 transition-all"></div>
                        </div>
                    </label>
                </div>
            </div>

            {{-- NEO-BRUTALISM BUTTON --}}
            <div class="pt-6">
                <button type="submit" class="w-full neo-shadow bg-[#D4E971] text-slate-900 py-6 rounded-[1.8rem] font-black uppercase tracking-[0.2em] text-xs border-2 border-slate-900 transition-all flex items-center justify-center gap-3">
                    <span>Konfirmasi & Bayar</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </button>
                <p class="text-[9px] text-center text-slate-400 mt-10 uppercase font-bold tracking-[0.2em] opacity-50">Secure Transaction Powered by Midtrans</p>
            </div>

        </form>
    </div>

</body>
</html>