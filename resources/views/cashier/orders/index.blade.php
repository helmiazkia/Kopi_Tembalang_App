<x-layouts.cashier title="Kasir POS">
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.clientKey') }}"></script>

    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .menu-card {
            transition: all 0.3s ease;
            border: 1px solid #f1f5f9;
            background: #ffffff;
        }

        .menu-card:hover:not(.out-of-stock) {
            transform: translateY(-4px);
            border-color: #D4E971;
            box-shadow: 0 15px 30px -10px rgba(0, 0, 0, 0.05);
        }

        .menu-card.out-of-stock {
            opacity: 0.5;
            filter: grayscale(1);
            cursor: not-allowed;
        }

        .out-of-stock-label {
            position: absolute;
            top: 12px;
            right: 12px;
            background: #ef4444;
            color: white;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 9px;
            font-weight: 800;
            z-index: 10;
        }

        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(8px);
            z-index: 999;
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.show {
            display: flex;
        }

        .opt-btn {
            transition: all 0.2s;
            border-width: 2px;
        }

        .opt-btn.active {
            background: #D4E971 !important;
            border-color: #D4E971 !important;
            color: #1a1a1a !important;
            transform: scale(1.05);
        }

        .cart-item {
            background: #f8fafc;
            border: 1px solid #f1f5f9;
            transition: all 0.2s ease;
        }

        .cart-item:hover {
            border-color: #D4E971;
        }
    </style>

    <div class="max-w-[1600px] mx-auto p-4 md:p-6">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            {{-- ================= AREA MENU (KIRI) ================= --}}
            <div class="lg:col-span-8">
                {{-- Toolbar --}}
                <div class="flex flex-col md:flex-row gap-4 items-center justify-between mb-6">
                    <div class="w-full md:w-2/5">
                        <form action="{{ route('cashier.orders.index') }}" method="GET" id="search-form" class="relative group">
                            @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
                            <input type="text" name="search" id="search-input" value="{{ request('search') }}"
                                class="w-full pl-12 pr-4 py-3.5 bg-white border border-slate-200 rounded-2xl text-sm font-semibold focus:ring-4 focus:ring-[#D4E971]/20 focus:border-[#D4E971] outline-none transition-all shadow-sm"
                                placeholder="Cari menu kopi...">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-[#D4E971]">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="8" />
                                    <path d="m21 21-4.3-4.3" />
                                </svg>
                            </div>
                        </form>
                    </div>

                    <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-1">
                        <a href="{{ route('cashier.orders.index') }}" class="px-5 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition-all {{ !request('category') ? 'bg-slate-900 text-white shadow-lg' : 'bg-white text-slate-400 border border-slate-200' }}">Semua</a>
                        @foreach($categories as $cat)
                        <a href="{{ route('cashier.orders.index', ['category' => $cat->id]) }}" class="px-5 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition-all whitespace-nowrap {{ request('category') == $cat->id ? 'bg-slate-900 text-white shadow-lg' : 'bg-white text-slate-400 border border-slate-200' }}">{{ $cat->name }}</a>
                        @endforeach
                    </div>
                </div>

                {{-- Filter --}}
                <div class="flex flex-wrap items-center gap-4 mb-8 bg-slate-50 p-4 rounded-[2rem] border border-slate-100">
                    <div class="flex items-center gap-3">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Filter:</span>
                        <select onchange="applyExtraFilter('stock', this.value)" class="text-xs font-bold border-none bg-white rounded-lg py-1.5 pl-3 pr-8 shadow-sm">
                            <option value="">Status Stok</option>
                            <option value="available" {{ request('stock') == 'available' ? 'selected' : '' }}>Tersedia</option>
                            <option value="empty" {{ request('stock') == 'empty' ? 'selected' : '' }}>Habis</option>
                        </select>
                        <select onchange="applyExtraFilter('sort', this.value)" class="text-xs font-bold border-none bg-white rounded-lg py-1.5 pl-3 pr-8 shadow-sm">
                            <option value="">Urutan Harga</option>
                            <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Termurah</option>
                            <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Termahal</option>
                        </select>
                    </div>
                </div>

                {{-- Grid --}}
                <div class="grid grid-cols-2 xl:grid-cols-4 gap-4 overflow-y-auto pr-2 no-scrollbar" style="max-height: 60vh;">
                    @forelse($menus as $menu)
                    <div class="menu-card relative rounded-[2rem] overflow-hidden {{ !$menu->is_available ? 'out-of-stock' : 'cursor-pointer group' }}"
                        @if($menu->is_available) onclick="openModal(this)" data-id="{{ $menu->id }}" data-name="{{ $menu->name }}" data-price="{{ $menu->price }}" data-image="{{ $menu->image ? asset('images/menu/'.$menu->image) : 'https://via.placeholder.com/300x300' }}" data-options='@json($menu->options)' @endif>
                        @if(!$menu->is_available) <div class="out-of-stock-label">HABIS</div> @endif
                        <div class="h-40 overflow-hidden bg-slate-100">
                            <img src="{{ $menu->image ? asset('images/menu/'.$menu->image) : 'https://via.placeholder.com/300x300' }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        </div>
                        <div class="p-4">
                            <h3 class="text-xs font-extrabold text-slate-700 uppercase leading-tight truncate mb-1">{{ $menu->name }}</h3>
                            <p class="text-base font-black text-slate-900 italic"><span class="text-[10px] font-normal not-italic opacity-40">Rp</span>{{ number_format($menu->price, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full py-20 text-center opacity-20 font-black uppercase tracking-widest text-xs italic">Menu tidak ditemukan</div>
                    @endforelse
                </div>
            </div>

            {{-- ================= KERANJANG (KANAN) ================= --}}
            <div class="lg:col-span-4">
                <form id="pos-form" action="{{ route('cashier.orders.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="payment_method" id="payment_method" required>
                    <div class="bg-white rounded-[2.5rem] p-6 border border-slate-200 shadow-xl shadow-slate-100 sticky top-6 flex flex-col h-full max-h-[90vh]">
                        <div class="flex justify-between items-center mb-6 pb-4 border-b border-slate-100 shrink-0">
                            <div class="flex items-center gap-2">
                                <div class="w-1.5 h-5 bg-[#D4E971] rounded-full"></div>
                                <h2 class="font-black text-lg uppercase italic text-slate-800">Keranjang</h2>
                            </div>
                            <span id="cart-badge" class="px-3 py-1 bg-slate-900 text-[#D4E971] rounded-lg font-black text-[10px]">0 ITEM</span>
                        </div>

                        <div id="order-type-wrapper" class="space-y-3 mb-6 shrink-0">
                            <input type="text" name="customer_name" placeholder="NAMA PELANGGAN" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-3 text-xs font-bold focus:border-[#D4E971] outline-none">
                            <input type="email" name="email" placeholder="EMAIL (NOTIFIKASI MIDTRANS)" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-3 text-xs font-bold focus:border-[#D4E971] outline-none">
                            <input type="text" name="phone" placeholder="NOMOR HP (OPSIONAL)" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-3 text-xs font-bold focus:border-[#D4E971] outline-none">

                            <div id="order-type-wrapper" class="grid grid-cols-2 gap-2 transition-all duration-300">

                                <select name="order_type" id="order_type" onchange="toggleTable()"
                                    class="w-full col-span-1 text-center bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-[10px] font-black uppercase outline-none focus:border-[#D4E971]">
                                    <option value="dine_in">Dine In</option>
                                    <option value="takeaway">Takeaway</option>
                                </select>

                                <div id="table-wrapper" class="col-span-1">
                                    <select name="table_id" id="table_id_input"
                                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-[10px] font-black uppercase outline-none focus:border-[#D4E971]">
                                        <option value="">MEJA</option>
                                        @foreach($tables as $table)
                                        <option value="{{ $table->id }}">#{{ $table->table_number }}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                            <textarea name="notes" rows="2" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-2 text-xs font-bold focus:border-[#D4E971] outline-none" placeholder="Catatan global..."></textarea>
                        </div>

                        <div id="cart-list" class="space-y-2 overflow-y-auto grow no-scrollbar min-h-[100px]">
                            <div class="py-10 text-center opacity-30 text-[9px] font-black uppercase tracking-[0.2em]">Keranjang Kosong</div>
                        </div>

                        {{-- Hidden inputs untuk item menu akan di-render di sini oleh JS --}}
                        <div id="hidden-inputs"></div>

                        <div class="mt-auto pt-6 border-t-2 border-dashed border-slate-100 shrink-0">
                            <div class="flex justify-between items-end mb-6">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Bayar</span>
                                <h2 id="total-display" class="text-3xl font-black text-slate-900 italic">Rp 0</h2>
                            </div>
                            <button type="button" id="submit-btn" onclick="openPaymentModal()" class="w-full bg-[#D4E971] hover:bg-slate-900 hover:text-white text-slate-900 py-4.5 rounded-2xl text-xs font-black uppercase shadow-lg transition-all py-4">PROSES PESANAN</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL PAYMENT --}}
    <div id="payment-modal" class="modal-overlay">
        <div class="bg-white w-full max-w-sm rounded-[2.5rem] p-8 shadow-2xl text-center border border-slate-100">
            <h2 class="text-xl font-black text-slate-800 uppercase italic">Konfirmasi Bayar</h2>
            <p class="text-[10px] font-bold text-slate-400 mt-2 uppercase tracking-widest mb-8">Pilih metode pembayaran</p>

            <div id="payment-step-1" class="grid grid-cols-1 gap-3">
                <button onclick="showPaymentMethods()" class="w-full py-4 rounded-2xl bg-slate-900 text-[#D4E971] font-black uppercase text-xs tracking-widest shadow-lg">METODE PEMBAYARAN</button>
                <button onclick="confirmCancelPayment()" class="w-full py-4 rounded-2xl bg-slate-100 text-slate-400 font-black uppercase text-xs">BATALKAN</button>
            </div>

            <div id="payment-step-2" class="hidden grid grid-cols-1 gap-3">
                <button onclick="submitPayment('cash')" class="flex items-center justify-between px-6 py-5 rounded-2xl border-2 border-slate-100 hover:border-[#D4E971] transition-all bg-white shadow-sm">
                    <span class="font-black uppercase text-xs text-slate-600">💵 TUNAI / CASH</span>
                </button>
                <button onclick="submitPayment('qris')" class="flex items-center justify-between px-6 py-5 rounded-2xl border-2 border-slate-100 hover:border-blue-400 transition-all bg-white shadow-sm">
                    <span class="font-black uppercase text-xs text-slate-600">📱 QRIS / SNAP</span>
                </button>
                <button onclick="confirmCancelPayment()" class="mt-4 text-[10px] font-black text-slate-300 uppercase tracking-widest hover:text-red-500">KEMBALI</button>
            </div>
        </div>
    </div>

    {{-- LOADING OVERLAY --}}
    <div id="success-modal" class="modal-overlay">
        <div class="bg-white rounded-[2.5rem] p-8 text-center w-80 shadow-2xl">
            <div class="animate-spin w-12 h-12 border-4 border-[#D4E971] border-t-slate-900 rounded-full mx-auto mb-6"></div>
            <h2 class="text-xl font-black text-slate-800 uppercase italic">Processing...</h2>
            <p class="text-xs font-bold text-slate-400 mt-2 uppercase">Sedang memproses pesanan</p>
        </div>
    </div>

    {{-- MODAL DETAIL MENU (OPTIONS) --}}
    <div id="modal-overlay" class="modal-overlay" onclick="closeModal()">
        <div class="bg-white w-full max-w-md rounded-[2.5rem] overflow-hidden shadow-2xl mx-4" onclick="event.stopPropagation()">
            <div class="relative h-48"><img id="modal-img" class="w-full h-full object-cover"></div>
            <div class="p-6">
                <h3 id="modal-name" class="text-xl font-black text-slate-800 uppercase italic"></h3>
                <p id="modal-base-price" class="text-xs font-bold text-slate-400 mt-2 uppercase"></p>
                <div id="modal-options" class="space-y-5 my-6"></div>
                <textarea id="modal-note" rows="2" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-3 text-xs font-bold focus:border-[#D4E971] outline-none" placeholder="CATATAN ITEM..."></textarea>
            </div>
            <div class="flex justify-between items-center p-6 bg-slate-50 border-t border-slate-100">
                <div class="flex flex-col"><span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Subtotal</span><strong id="modal-total" class="text-xl font-black italic">Rp 0</strong></div>
                <button type="button" onclick="confirmAdd()" class="bg-slate-900 text-[#D4E971] px-8 py-4 rounded-xl text-[10px] font-black uppercase shadow-lg">TAMBAHKAN</button>
            </div>
        </div>
    </div>

    <script>
        let cart = [];
        let currentItem = null;
        let currentSelected = {};

        function applyExtraFilter(key, value) {
            let url = new URL(window.location.href);
            value ? url.searchParams.set(key, value) : url.searchParams.delete(key);
            window.location.href = url.toString();
        }

        function toggleTable() {
            const wrapper = document.getElementById('table-wrapper');
            const grid = document.getElementById('order-type-wrapper');
            const select = document.getElementById('order_type');
            const type = select.value;

            if (type === 'takeaway') {
                wrapper.classList.add('hidden');

                // grid jadi 1 kolom
                grid.classList.remove('grid-cols-2');
                grid.classList.add('grid-cols-1');

                // select FULL WIDTH
                select.classList.remove('col-span-1');
                select.classList.add('col-span-2');

            } else {
                wrapper.classList.remove('hidden');

                // balik ke 2 kolom
                grid.classList.remove('grid-cols-1');
                grid.classList.add('grid-cols-2');

                // normal lagi
                select.classList.remove('col-span-2');
                select.classList.add('col-span-1');
            }
        }

        function openPaymentModal() {
            if (cart.length === 0) return alert('Pilih menu dulu!');
            if (document.getElementById('order_type').value === 'dine_in' && !document.getElementById('table_id_input').value) return alert('Pilih nomor meja!');
            document.getElementById('payment-modal').classList.add('show');
        }

        function showPaymentMethods() {
            document.getElementById('payment-step-1').classList.add('hidden');
            document.getElementById('payment-step-2').classList.remove('hidden');
        }

        function confirmCancelPayment() {
            if (confirm("Batalkan proses pembayaran? Pesanan belum disimpan.")) {
                document.getElementById('payment-modal').classList.remove('show');
                document.getElementById('payment-step-1').classList.remove('hidden');
                document.getElementById('payment-step-2').classList.add('hidden');
            }
        }

        // ================= CORE SUBMIT =================
        function submitPayment(method) {
            document.getElementById('payment_method').value = method;
            const form = document.getElementById('pos-form');
            const formData = new FormData(form);

            // Debugging: Lihat data di Console Browser
            console.log("Data dikirim:", Object.fromEntries(formData));

            const btn = document.getElementById('submit-btn');
            btn.disabled = true;
            document.getElementById('payment-modal').classList.remove('show');
            document.getElementById('success-modal').classList.add('show');

            fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(res => {
                    if (res.type === 'cash') {
                        window.location.href = '/cashier/receipt/' + res.order_id;
                        return;
                    }

                    // QRIS Snap
                    snap.pay(res.snap_token, {
                        onSuccess: (result) => {
                            window.location.href = '/cashier/receipt/' + res.order_id;
                        },
                        onPending: (result) => {
                            alert('Pending! Pesanan masuk antrean.');
                            window.location.href = '/cashier/orders';
                        },
                        onClose: () => {
                            alert('Pembayaran ditutup. Pesanan tersimpan di antrean.');
                            window.location.href = '/cashier/orders';
                        }
                    });
                })
                .catch(err => {
                    alert('Gagal memproses pesanan.');
                    document.getElementById('success-modal').classList.remove('show');
                    btn.disabled = false;
                });
        }

        // ================= MODAL & CART FUNCTIONS =================
        function openModal(card) {
            currentItem = {
                id: card.dataset.id,
                name: card.dataset.name,
                price: parseInt(card.dataset.price),
                image: card.dataset.image,
                options: JSON.parse(card.dataset.options)
            };
            currentSelected = {};
            document.getElementById('modal-img').src = currentItem.image;
            document.getElementById('modal-name').innerText = currentItem.name;
            document.getElementById('modal-base-price').innerText = 'BASE: Rp ' + currentItem.price.toLocaleString('id');
            document.getElementById('modal-note').value = '';
            let html = '';
            currentItem.options.forEach(opt => {
                html += `<div><p class="text-[10px] font-black text-slate-300 uppercase mb-2">${opt.name}</p><div class="flex flex-wrap gap-2">`;
                opt.items.forEach(i => {
                    html += `<button type="button" class="opt-btn border-2 border-slate-100 px-3 py-1.5 text-[10px] rounded-lg font-black uppercase" onclick="selectOpt(${opt.id}, ${i.id}, ${i.price}, '${i.name}', this)">${i.name} (+${i.price.toLocaleString('id')})</button>`;
                });
                html += `</div></div>`;
            });
            document.getElementById('modal-options').innerHTML = html;
            updateModalTotal();
            document.getElementById('modal-overlay').classList.add('show');
        }

        function closeModal() {
            document.getElementById('modal-overlay').classList.remove('show');
        }

        function selectOpt(optId, itemId, price, name, btn) {
            btn.parentElement.querySelectorAll('.opt-btn').forEach(b => b.classList.remove('active'));
            currentSelected[optId] = {
                id: itemId,
                price,
                name
            };
            btn.classList.add('active');
            updateModalTotal();
        }

        function updateModalTotal() {
            let extra = Object.values(currentSelected).reduce((s, o) => s + o.price, 0);
            document.getElementById('modal-total').innerText = 'Rp ' + (currentItem.price + extra).toLocaleString('id');
        }

        function confirmAdd() {
            cart.push({
                ...currentItem,
                selectedOptions: {
                    ...currentSelected
                },
                notes: document.getElementById('modal-note').value.trim()
            });
            closeModal();
            renderCart();
        }

        function renderCart() {
            const list = document.getElementById('cart-list');
            const hiddenInputs = document.getElementById('hidden-inputs');
            let total = 0;
            let html = '';
            hiddenInputs.innerHTML = '';
            if (cart.length === 0) {
                list.innerHTML = '<div class="py-10 text-center opacity-30 text-[9px] font-black uppercase tracking-[0.2em]">Keranjang Kosong</div>';
                document.getElementById('total-display').innerText = 'Rp 0';
                document.getElementById('cart-badge').innerText = '0 ITEM';
                return;
            }
            cart.forEach((item, i) => {
                let extra = Object.values(item.selectedOptions).reduce((s, o) => s + o.price, 0);
                let sub = item.price + extra;
                total += sub;
                const opts = Object.values(item.selectedOptions).map(o => o.name).join(', ');
                html += `<div class="cart-item p-4 rounded-2xl mb-2 group relative">
                    <div class="flex justify-between items-start">
                        <div class="max-w-[70%]">
                            <span class="text-[11px] font-black uppercase text-slate-800">${item.name}</span>
                            ${opts ? `<p class="text-[9px] font-bold text-slate-400 uppercase mt-1">Opsi: ${opts}</p>` : ''}
                            ${item.notes ? `<p class="text-[9px] text-blue-500 italic mt-0.5">"${item.notes}"</p>` : ''}
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-black text-slate-900 italic">Rp${sub.toLocaleString('id')}</span>
                            <button type="button" onclick="removeItem(${i})" class="block text-[8px] font-black text-red-400 uppercase mt-2 opacity-0 group-hover:opacity-100">Hapus</button>
                        </div>
                    </div>
                </div>`;

                // Render Hidden Inputs
                const addInp = (name, val) => {
                    let inp = document.createElement('input');
                    inp.type = 'hidden';
                    inp.name = name;
                    inp.value = val;
                    hiddenInputs.appendChild(inp);
                };
                addInp(`items[${i}][menu_id]`, item.id);
                addInp(`items[${i}][notes]`, item.notes);
                Object.entries(item.selectedOptions).forEach(([optId, opt]) => {
                    addInp(`items[${i}][options][${optId}]`, opt.id);
                });
            });
            list.innerHTML = html;
            document.getElementById('total-display').innerText = 'Rp ' + total.toLocaleString('id');
            document.getElementById('cart-badge').innerText = cart.length + ' ITEM';
        }

        function removeItem(i) {
            cart.splice(i, 1);
            renderCart();
        }

        window.onload = () => {
            toggleTable();
        }
    </script>
</x-layouts.cashier>