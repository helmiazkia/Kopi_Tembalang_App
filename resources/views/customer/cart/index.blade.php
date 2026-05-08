<x-layouts.customer :title="'Keranjang Pesanan'">
    <div class="flex flex-col min-h-screen relative bg-white">

        <header class="sticky top-0 z-30 bg-white/90 backdrop-blur-md px-6 py-5 flex items-center justify-between border-b border-slate-100">
            <div class="flex items-center gap-2">
                <div class="w-1.5 h-6 bg-[#D4E971] rounded-full shadow-[0_0_10px_rgba(212,233,113,0.8)]"></div>
                <h1 class="text-xl font-black uppercase italic tracking-tight text-slate-800">Keranjang</h1>
            </div>
            <button onclick="clearCart()" class="text-[10px] font-bold text-red-500 uppercase bg-red-50 px-3 py-2 rounded-xl active:scale-95 transition-all">
                Kosongkan
            </button>
        </header>

        <main id="cart-items" class="flex-1 p-5 space-y-4 pb-56">
            </main>

        <div id="empty-state" class="hidden flex-1 flex flex-col items-center justify-center p-10 text-center min-h-[60vh]">
            <div class="relative mb-6">
                <div class="absolute inset-0 bg-[#D4E971] blur-3xl opacity-20 rounded-full"></div>
                <span class="text-7xl relative">☕</span>
            </div>
            <h2 class="font-black uppercase italic text-slate-800 text-lg">Keranjang Kosong!</h2>
            <p class="text-slate-400 text-[10px] mt-2 mb-8 uppercase tracking-[0.2em] font-bold leading-relaxed">Pilih menu favoritmu sekarang</p>
            <a href="{{ route('customer.menu', ['table' => $table->id ?? 0]) }}" class="w-full bg-slate-900 text-[#D4E971] py-4 rounded-2xl flex items-center justify-center font-black text-xs uppercase tracking-widest shadow-xl">
                Lihat Menu
            </a>
        </div>

        <footer id="bottom-bar" class="fixed bottom-0 w-full max-w-md bg-white border-t border-slate-100 p-6 shadow-[0_-20px_50px_rgba(0,0,0,0.1)] z-40 rounded-t-[2.5rem] left-1/2 -translate-x-1/2">
            <div class="space-y-2 mb-6 px-1">
                <div class="flex justify-between items-center text-[11px] font-bold uppercase tracking-widest text-slate-400">
                    <span>Harga Dasar</span>
                    <span id="subtotal-display">Rp 0</span>
                </div>
                <div class="flex justify-between items-center text-[11px] font-bold uppercase tracking-widest text-slate-400">
                    <span>Ekstra Topping</span>
                    <span id="options-display">Rp 0</span>
                </div>
                <div class="pt-2 border-t border-dashed border-slate-200 flex justify-between items-center">
                    <div>
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Total Bayar</p>
                        <p id="total-display" class="text-2xl font-black text-slate-900 italic tracking-tighter">Rp 0</p>
                    </div>
                    <div class="bg-[#D4E971] text-slate-900 px-4 py-2 rounded-2xl shadow-sm">
                        <span id="item-count" class="text-[11px] font-black uppercase">0 Pesanan</span>
                    </div>
                </div>
            </div>

            <form id="checkout-form" method="POST" action="{{ route('customer.cart.sync') }}">
                @csrf
                <input type="hidden" name="table_id" value="{{ $table->id ?? '' }}">
                <input type="hidden" name="cart_data" id="cart_data_input">
                <button type="submit" class="w-full bg-slate-900 text-[#D4E971] py-5 rounded-[1.8rem] font-black uppercase tracking-[0.15em] text-xs shadow-2xl active:scale-[0.97] transition-all">
                    Konfirmasi Pesanan
                </button>
            </form>
        </footer>
    </div>

    @push('scripts')
    <script>
        let cart = JSON.parse(localStorage.getItem('cart') || '[]');

        function renderCart() {
            const container = document.getElementById('cart-items');
            const emptyState = document.getElementById('empty-state');
            const bottomBar = document.getElementById('bottom-bar');
            const countDisplay = document.getElementById('item-count');

            let totalMenuBase = 0;
            let totalOptionsAll = 0;
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

                totalMenuBase += item.price;

                // Logika menampilkan Opsi dengan Quantity (e.g. 2x Meses)
                if (item.options && Object.keys(item.options).length > 0) {
                    optionHtml = `<div class="flex flex-wrap gap-1 mt-2">`;
                    Object.values(item.options).forEach(opt => {
                        const subTotalOpt = opt.price * opt.qty;
                        itemOptionTotal += subTotalOpt;
                        optionHtml += `
                            <span class="text-[8px] font-bold bg-slate-100 text-slate-600 px-2 py-1 rounded-md uppercase tracking-tighter border border-slate-200">
                                ${opt.qty}x ${opt.name} (+Rp${subTotalOpt.toLocaleString('id')})
                            </span>`;
                    });
                    optionHtml += `</div>`;
                }

                totalOptionsAll += itemOptionTotal;
                let itemFinalSubtotal = item.price + itemOptionTotal;

                let imgUrl = item.image ? `/images/menu/${item.image}` : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(item.name) + '&background=D4E971&color=000';

                html += `
                <div class="bg-white p-4 rounded-[2rem] border border-slate-100 shadow-sm flex items-start gap-4">
                    <div class="w-16 h-16 rounded-2xl overflow-hidden flex-shrink-0 bg-slate-50 border border-slate-100">
                        <img src="${imgUrl}" class="w-full h-full object-cover">
                    </div>

                    <div class="flex-1">
                        <div class="flex justify-between items-start">
                            <h3 class="font-black text-slate-800 uppercase text-xs leading-tight">${item.name}</h3>
                            <button onclick="removeItem(${i})" class="text-red-400 p-1 hover:text-red-600 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                        
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Rp${item.price.toLocaleString('id')}</p>

                        ${optionHtml}

                        ${item.notes ? `
                            <div class="mt-2 text-[9px] text-blue-500 italic font-bold bg-blue-50 p-1.5 rounded-lg border border-blue-100">
                                " ${item.notes} "
                            </div>
                        ` : ''}

                        <div class="mt-3 pt-2 border-t border-slate-50 flex justify-between items-center">
                            <span class="text-[8px] font-black text-slate-300 uppercase">Subtotal Item</span>
                            <span class="text-sm font-black text-slate-900 italic">Rp${itemFinalSubtotal.toLocaleString('id')}</span>
                        </div>
                    </div>
                </div>`;
            });

            container.innerHTML = html;

            // Update Summary
            document.getElementById('subtotal-display').innerText = 'Rp ' + totalMenuBase.toLocaleString('id');
            document.getElementById('options-display').innerText = '+ Rp ' + totalOptionsAll.toLocaleString('id');
            document.getElementById('total-display').innerText = 'Rp ' + (totalMenuBase + totalOptionsAll).toLocaleString('id');

            // Sync Hidden Data
            document.getElementById('cart_data_input').value = JSON.stringify(cart);
        }

        function removeItem(index) {
            if (confirm('Hapus menu ini?')) {
                cart.splice(index, 1);
                updateStorage();
            }
        }

        function clearCart() {
            if (confirm('Kosongkan keranjang?')) {
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