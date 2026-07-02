<x-layouts.customer :title="'Checkout Pesanan - Kopi Tembalang'">
    <div class="flex flex-col min-h-screen bg-[#fafafa]">

        <main class="p-6 space-y-8">

            <!-- HEADER SECTION -->
            <div class="relative flex items-center justify-center">
                <div class="absolute left-0">
                    <a href="javascript:history.back()" class="w-10 h-10 flex items-center justify-center bg-white text-slate-900 rounded-full shadow-sm border border-slate-100 active:scale-90 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                </div>
                <div class="inline-block px-3 py-1 rounded-full bg-[#D4E971]/20 text-[#8ba11c] text-[9px] font-black uppercase tracking-widest">
                    ✨ Konfirmasi Pesanan
                </div>
            </div>

            <div class="text-center">
                <h2 class="text-2xl font-black text-slate-900 italic leading-tight uppercase tracking-tighter">
                    Lengkapi data,<br><span class="text-[#D4E971]">Pesanan Segera Diproses.</span>
                </h2>
            </div>

            <!-- FORM -->
            <form id="mainOrderForm" method="POST" action="{{ route('customer.checkout.store', ['table' => $table->id]) }}" class="space-y-6">
                @csrf
                <input type="hidden" name="table_id" value="{{ $table->id }}">

                <!-- INFO MEJA -->
                <div class="relative group">
                    <div class="absolute -inset-0.5 bg-gradient-to-r from-[#D4E971] to-slate-800 rounded-3xl blur opacity-20"></div>
                    <div class="relative bg-slate-900 rounded-2xl p-6 overflow-hidden flex justify-between items-center shadow-xl">
                        <div>
                            <p class="text-[#D4E971]/60 text-[9px] font-black uppercase tracking-widest mb-1">Meja Kamu</p>
                            <h3 class="text-xl font-black text-white italic uppercase tracking-tight">Meja {{ $table->table_number }}</h3>
                        </div>
                        <div class="bg-[#D4E971] w-10 h-10 rounded-xl rotate-6 flex items-center justify-center shadow-lg text-lg">📍</div>
                    </div>
                </div>

                <!-- FORM INPUT -->
                <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-slate-100 space-y-5">
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black uppercase text-slate-400 ml-2 tracking-widest">Nama Lengkap</label>
                        <input id="input_name" name="customer_name" placeholder="Siapa namamu?" required
                            class="w-full px-5 py-4 rounded-xl bg-slate-50 border-2 border-transparent focus:border-[#D4E971] focus:bg-white outline-none transition-all duration-300 font-bold text-sm text-slate-700">
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black uppercase text-slate-400 ml-2 tracking-widest">WhatsApp</label>
                        <input id="input_phone" name="phone" type="tel" placeholder="0812xxxx" required
                            class="w-full px-5 py-4 rounded-xl bg-slate-50 border-2 border-transparent focus:border-[#D4E971] focus:bg-white outline-none transition-all duration-300 font-bold text-sm text-slate-700">
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black uppercase text-slate-400 ml-2 tracking-widest">Email (Opsional)</label>
                        <input name="email" type="email" placeholder="kopi@tembalang.com"
                            class="w-full px-5 py-4 rounded-xl bg-slate-50 border-2 border-transparent focus:border-[#D4E971] focus:bg-white outline-none transition-all duration-300 font-bold text-sm text-slate-700">
                    </div>
                </div>

                <!-- METODE PEMBAYARAN -->
                <div class="space-y-3">
                    <p class="text-[9px] font-black uppercase text-slate-400 text-center tracking-[0.2em]">Pilih Cara Bayar</p>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="relative cursor-pointer">
                            <input type="radio" name="payment_method" value="qris" class="peer hidden" checked>
                            <div class="p-4 bg-white border-2 border-slate-100 rounded-2xl peer-checked:border-[#D4E971] peer-checked:bg-[#D4E971]/5 transition-all flex flex-col items-center gap-2">
                                <span class="text-2xl">📱</span>
                                <span class="text-[9px] font-black uppercase text-slate-500 peer-checked:text-slate-900 tracking-tighter">Bayar Online</span>
                            </div>
                        </label>
                        <label class="relative cursor-pointer">
                            <input type="radio" name="payment_method" value="cash" class="peer hidden">
                            <div class="p-4 bg-white border-2 border-slate-100 rounded-2xl peer-checked:border-slate-800 peer-checked:bg-slate-50 transition-all flex flex-col items-center gap-2">
                                <span class="text-2xl">💵</span>
                                <span class="text-[9px] font-black uppercase text-slate-500 peer-checked:text-slate-900 tracking-tighter">Tunai di Kasir</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- TRIGGER BUTTON (Bukan submit langsung) -->
                <div class="pt-4 pb-10">
                    <button type="button" onclick="openConfirmModal()" class="w-full shadow-[0_6px_0_0_#0f172a] active:shadow-none active:translate-y-[6px] bg-[#D4E971] text-slate-900 py-5 rounded-2xl font-black uppercase tracking-[0.15em] text-[11px] border-2 border-slate-900 transition-all flex items-center justify-center gap-3">
                        <span>Pesan & Bayar Sekarang</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </main>
    </div>

    <!-- MODAL KONFIRMASI -->
    <div id="confirmModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center px-6">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeConfirmModal()"></div>
        <div class="relative bg-white w-full max-w-xs rounded-[2.5rem] p-8 shadow-2xl scale-95 transition-transform duration-300" id="modalContent">
            <div class="text-center space-y-4">
                <div class="w-16 h-16 bg-[#D4E971] rounded-full flex items-center justify-center mx-auto text-2xl shadow-lg shadow-[#D4E971]/30">☕</div>
                <h3 class="text-lg font-black uppercase italic tracking-tighter">Cek Sekali Lagi!</h3>
                <div class="bg-slate-50 rounded-2xl p-4 text-left space-y-2">
                    <div>
                        <p class="text-[8px] font-black uppercase text-slate-400">Nama Pemesan</p>
                        <p id="display_name" class="text-sm font-bold text-slate-800">Nama</p>
                    </div>
                    <div>
                        <p class="text-[8px] font-black uppercase text-slate-400">Metode Bayar</p>
                        <p id="display_method" class="text-sm font-bold text-slate-800">Metode</p>
                    </div>
                </div>
                <p class="text-[10px] text-slate-400 font-medium leading-relaxed">Pastikan pesanan dan data sudah sesuai sebelum diproses.</p>
                <div class="grid grid-cols-1 gap-2 pt-2">
                    <button type="button" onclick="submitFinalOrder()" class="w-full bg-slate-900 text-[#D4E971] py-4 rounded-xl font-black uppercase text-[10px] tracking-widest active:scale-95 transition-all">Sesuai, Pesan!</button>
                    <button type="button" onclick="closeConfirmModal()" class="w-full bg-slate-100 text-slate-400 py-3 rounded-xl font-black uppercase text-[9px] tracking-widest active:scale-95 transition-all">Perbaiki Data</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function openConfirmModal() {
            const name = document.getElementById('input_name').value;
            const phone = document.getElementById('input_phone').value;
            const method = document.querySelector('input[name="payment_method"]:checked').value;

            // Validasi sederhana sebelum buka modal
            if (!name || !phone) {
                alert('Tolong isi nama dan nomor WhatsApp dulu ya! 🙏');
                return;
            }

            document.getElementById('display_name').innerText = name;
            document.getElementById('display_method').innerText = method === 'qris' ? 'Pembayaran Online' : 'Tunai di Kasir';

            const modal = document.getElementById('confirmModal');
            modal.classList.remove('hidden');
            setTimeout(() => {
                document.getElementById('modalContent').classList.remove('scale-95');
                document.getElementById('modalContent').classList.add('scale-100');
            }, 10);
        }

        function closeConfirmModal() {
            const modal = document.getElementById('confirmModal');
            document.getElementById('modalContent').classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 200);
        }

        function submitFinalOrder() {
            const btn = event.target;
            btn.disabled = true;
            btn.innerText = "Memproses...";
            document.getElementById('mainOrderForm').submit();
        }
    </script>
    @endpush
</x-layouts.customer>