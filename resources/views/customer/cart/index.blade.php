<x-layouts.customer :title="'Keranjang Pesanan'">
    <div class="flex flex-col min-h-screen relative bg-white">

        <!-- HEADER -->
        <header class="sticky top-0 z-30 bg-white/90 backdrop-blur-md px-6 py-5 flex items-center justify-between border-b border-slate-100">
            <div class="flex items-center gap-2">
                <div class="w-1.5 h-6 bg-[#D4E971] rounded-full shadow-[0_0_10px_rgba(212,233,113,0.8)]"></div>
                <h1 class="text-xl font-black uppercase italic tracking-tight text-slate-800">Keranjang</h1>
            </div>
            <button onclick="clearCart()" class="text-[10px] font-bold text-red-500 uppercase bg-red-50 px-3 py-2 rounded-xl active:scale-95 transition-all">
                Kosongkan
            </button>
        </header>

        <!-- MAIN CONTENT -->
        <main id="cart-items" class="flex-1 p-5 space-y-4 pb-56">
            <!-- Items injected by JS -->
        </main>

        <!-- EMPTY STATE -->
        <div id="empty-state" class="hidden flex-1 flex flex-col items-center justify-center p-10 text-center min-h-[60vh]">
            <div class="relative mb-6">
                <div class="absolute inset-0 bg-[#D4E971] blur-3xl opacity-20 rounded-full"></div>
                <span class="text-7xl relative">🍔</span>
            </div>
            <h2 class="font-black uppercase italic text-slate-800 text-lg">Perutmu Masih Kosong!</h2>
            <p class="text-slate-400 text-[10px] mt-2 mb-8 uppercase tracking-[0.2em] font-bold leading-relaxed">Pilih menu favoritmu sekarang dan nikmati kelezatannya</p>
            <a href="{{ route('customer.menu', ['table' => $table->id ?? 0]) }}" class="w-full bg-slate-900 text-[#D4E971] py-4 rounded-2xl flex items-center justify-center font-black text-xs uppercase tracking-widest shadow-xl active:scale-95 transition-all">
                Lihat Daftar Menu
            </a>
        </div>

        <!-- BOTTOM SUMMARY & CHECKOUT -->
        <footer id="bottom-bar" class="fixed bottom-0 w-full max-w-md bg-white border-t border-slate-100 p-6 shadow-[0_-20px_50px_rgba(0,0,0,0.1)] safe-bottom z-40 rounded-t-[2.5rem]">
            <!-- Ringkasan Harga Detail -->
            <div class="space-y-2 mb-6 px-1">
                <div class="flex justify-between items-center text-[11px] font-bold uppercase tracking-widest text-slate-400">
                    <span>Subtotal Menu</span>
                    <span id="subtotal-display">Rp 0</span>
                </div>
                <div class="flex justify-between items-center text-[11px] font-bold uppercase tracking-widest text-slate-400">
                    <span>Tambahan/Opsi</span>
                    <span id="options-display">Rp 0</span>
                </div>
                <div class="pt-2 border-t border-dashed border-slate-200 flex justify-between items-center">
                    <div>
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none">Total Pembayaran</p>
                        <p id="total-display" class="text-2xl font-black text-slate-900 italic tracking-tighter">Rp 0</p>
                    </div>
                    <div class="bg-[#D4E971] text-slate-900 px-4 py-2 rounded-2xl shadow-sm">
                        <span id="item-count" class="text-[11px] font-black uppercase">0 Items</span>
                    </div>
                </div>
            </div>

            <form id="checkout-form" method="POST" action="{{ route('customer.cart.sync') }}">
                @csrf
                <input type="hidden" name="table_id" value="{{ $table->id ?? '' }}">
                <input type="hidden" name="cart_data" id="cart_data_input">
                <button type="submit" class="w-full bg-slate-900 text-[#D4E971] py-5 rounded-[1.8rem] font-black uppercase tracking-[0.15em] text-xs shadow-2xl active:scale-[0.97] transition-all flex items-center justify-center gap-3 group">
                    Konfirmasi Pesanan
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </button>
            </form>
        </footer>

    </div>

    @push('scripts')
    <script>
        // Data cart diasumsikan memiliki struktur: { id, name, price, image, notes, options: { name, price } }
        let cart = JSON.parse(localStorage.getItem('cart') || '[]');

        function renderCart() {
            const container = document.getElementById('cart-items');
            const emptyState = document.getElementById('empty-state');
            const bottomBar = document.getElementById('bottom-bar');
            const countDisplay = document.getElementById('item-count');

            let totalMenu = 0;
            let totalOptions = 0;
            let html = '';

            if (cart.length === 0) {
                container.innerHTML = '';
                emptyState.classList.remove('hidden');
                bottomBar.classList.add('hidden');
                return;
            }

            emptyState.classList.add('hidden');
            bottomBar.classList.remove('hidden');
            countDisplay.innerText = `${cart.length} Pesanan`;

            cart.forEach((item, i) => {
                let itemOptionTotal = 0;
                let optionHtml = '';

                totalMenu += item.price;

                if (item.options && Object.keys(item.options).length > 0) {
                    optionHtml = `<div class="flex flex-wrap gap-1 mt-2">`;
                    Object.values(item.options).forEach(opt => {
                        itemOptionTotal += opt.price;
                        optionHtml += `<span class="text-[8px] font-bold bg-slate-100 text-slate-500 px-2 py-0.5 rounded-md uppercase tracking-tighter"> + ${opt.name} (Rp${opt.price.toLocaleString('id')})</span>`;
                    });
                    optionHtml += `</div>`;
                }

                totalOptions += itemOptionTotal;
                let itemSubtotal = item.price + itemOptionTotal;

                // Logika Image Path: Menyesuaikan agar folder images/menu tetap terbaca
                let imgUrl = item.image;
                if (item.image && !item.image.startsWith('http')) {
                    imgUrl = `/images/menu/${item.image}`;
                } else if (!item.image) {
                    imgUrl = 'https://via.placeholder.com/150?text=Food';
                }

                html += `
                <div class="bg-white p-3 rounded-[2.2rem] border border-slate-100 shadow-[0_10px_20px_rgba(0,0,0,0.02)] flex items-start gap-4 active:scale-[0.98] transition-all">
                    <!-- Image Menu -->
                    <div class="w-20 h-20 rounded-[1.5rem] overflow-hidden flex-shrink-0 shadow-inner bg-slate-50">
                        <img src="${imgUrl}" class="w-full h-full object-cover" onerror="this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(item.name)}&background=D4E971&color=000'">
                    </div>

                    <!-- Detail Menu -->
                    <div class="flex-1">
                        <div class="flex justify-between items-start">
                            <h3 class="font-black text-slate-800 uppercase text-[11px] leading-tight tracking-tight">${item.name}</h3>
                            <button onclick="removeItem(${i})" class="bg-red-50 p-1.5 rounded-lg text-red-500 active:bg-red-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        
                        <p class="text-[10px] font-bold text-slate-400 italic mt-0.5">Rp${item.price.toLocaleString('id')}</p>

                        ${optionHtml}

                        ${item.notes ? `
                            <div class="mt-2 flex items-start gap-1 bg-blue-50/50 p-1.5 rounded-lg">
                                <span class="text-[8px]">💬</span>
                                <p class="text-[9px] text-blue-600 font-medium italic leading-tight">"${item.notes}"</p>
                            </div>
                        ` : ''}

                        <div class="mt-3 pt-2 border-t border-slate-50 flex justify-between items-center">
                            <span class="text-[9px] font-black text-slate-300 uppercase tracking-widest text-right block text-[8px]">Subtotal</span>
                            <span class="text-[12px] font-black text-slate-900 italic leading-none">Rp${itemSubtotal.toLocaleString('id')}</span>
                        </div>
                    </div>
                </div>
                `;
            });

            container.innerHTML = html;

            // Update Summary Area
            document.getElementById('subtotal-display').innerText = 'Rp ' + totalMenu.toLocaleString('id');
            document.getElementById('options-display').innerText = '+ Rp ' + totalOptions.toLocaleString('id');
            document.getElementById('total-display').innerText = 'Rp ' + (totalMenu + totalOptions).toLocaleString('id');

            // Masukkan ke input hidden untuk dikirim ke Controller
            document.getElementById('cart_data_input').value = JSON.stringify(cart);
        }
        

        function removeItem(index) {
            if (confirm('Hapus menu ini?')) {
                cart.splice(index, 1);
                updateStorage();
            }
        }

        function clearCart() {
            if (confirm('Hapus semua pesanan?')) {
                cart = [];
                updateStorage();
            }
        }

        function updateStorage() {
            localStorage.setItem('cart', JSON.stringify(cart));
            renderCart();
        }

        document.addEventListener('DOMContentLoaded', renderCart);
    </script>
    @endpush
</x-layouts.customer>