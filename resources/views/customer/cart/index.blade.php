<x-layouts.customer title="Keranjang Belanja">
    <div class="p-6 max-w-lg mx-auto mb-32">
        <div class="flex items-center gap-3 mb-8">
            <div class="w-1.5 h-6 bg-[#D4E971] rounded-full"></div>
            <h2 class="text-2xl font-black text-slate-800 uppercase italic">Keranjang</h2>
        </div>

        <div id="cart-items" class="space-y-4">
            </div>

        <div id="empty-state" class="hidden py-20 text-center">
            <span class="text-5xl block mb-4">🛒</span>
            <p class="text-slate-400 font-bold uppercase text-xs tracking-widest">Keranjangmu masih kosong</p>
            <a href="{{ route('customer.menu', ['table' => $table->id]) }}" class="text-[#D4E971] font-black text-xs uppercase underline mt-4 block">Kembali ke Menu</a>
        </div>
    </div>

    {{-- Bottom Bar --}}
    <div id="bottom-bar" class="fixed bottom-0 left-0 right-0 p-6 bg-white border-t border-slate-100 shadow-[0_-10px_40px_rgba(0,0,0,0.05)]">
        <div class="max-w-lg mx-auto flex justify-between items-end mb-4">
            <div>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Total Bayar</span>
                <div id="total-display" class="text-2xl font-black text-slate-900 italic">Rp 0</div>
            </div>
        </div>
        
        {{-- 🔥 FORM KE CHECKOUT --}}
        <form id="checkout-form" method="POST" action="{{ route('customer.cart.sync') }}">
            @csrf
            <input type="hidden" name="table_id" value="{{ $table->id }}">
            <input type="hidden" name="cart_data" id="cart_data_input">
            <button type="submit" class="w-full bg-[#D4E971] text-slate-900 py-4 rounded-2xl font-black uppercase tracking-widest text-xs shadow-lg shadow-[#D4E971]/20">
                Lanjut ke Checkout
            </button>
        </form>
    </div>

    <script>
        let cart = JSON.parse(localStorage.getItem('cart') || '[]');

        function renderCart() {
            const container = document.getElementById('cart-items');
            const emptyState = document.getElementById('empty-state');
            const bottomBar = document.getElementById('bottom-bar');
            let total = 0;
            let html = '';

            if (cart.length === 0) {
                container.innerHTML = '';
                emptyState.classList.remove('hidden');
                bottomBar.classList.add('hidden');
                return;
            }

            cart.forEach((item, i) => {
                let optionTotal = 0;
                let optionNames = [];
                
                if (item.options) {
                    Object.values(item.options).forEach(opt => {
                        optionTotal += opt.price;
                        optionNames.push(opt.name);
                    });
                }

                let subtotal = item.price + optionTotal;
                total += subtotal;

                html += `
                <div class="bg-white p-5 rounded-[2rem] border border-slate-100 shadow-sm relative group">
                    <div class="flex justify-between items-start">
                        <div class="max-w-[70%]">
                            <h3 class="font-black text-slate-800 uppercase text-sm leading-tight">${item.name}</h3>
                            ${optionNames.length ? `<p class="text-[9px] font-bold text-slate-400 uppercase mt-1">Opsi: ${optionNames.join(', ')}</p>` : ''}
                            ${item.notes ? `<p class="text-[9px] text-blue-500 italic mt-1 leading-tight">"${item.notes}"</p>` : ''}
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-black text-slate-900 italic">Rp${subtotal.toLocaleString('id')}</div>
                            <button onclick="removeItem(${i})" class="text-[8px] font-black text-red-400 uppercase mt-2 tracking-widest">Hapus</button>
                        </div>
                    </div>
                </div>
                `;
            });

            container.innerHTML = html;
            document.getElementById('total-display').innerText = 'Rp ' + total.toLocaleString('id');
            // Masukkan data localStorage ke input hidden agar bisa dikirim ke PHP
            document.getElementById('cart_data_input').value = JSON.stringify(cart);
        }

        function removeItem(index) {
            if(confirm('Hapus item ini?')) {
                cart.splice(index, 1);
                localStorage.setItem('cart', JSON.stringify(cart));
                renderCart();
            }
        }

        renderCart();
    </script>
</x-layouts.customer>