<x-layouts.cashier title="Kasir POS">

{{-- Midtrans Snap --}}
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.clientKey') }}"></script>

<style>
    /* ── Scrollbar ── */
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

    /* ── Menu Card ── */
    .menu-card {
        border: 1.5px solid #f1f5f9;
        background: #fff;
        transition: transform 0.25s ease, border-color 0.25s ease, box-shadow 0.25s ease;
    }
    .menu-card:hover:not(.out-of-stock) {
        transform: translateY(-5px);
        border-color: #D4E971;
        box-shadow: 0 18px 36px -12px rgba(0,0,0,0.08);
    }
    .menu-card.out-of-stock {
        opacity: 0.45;
        filter: grayscale(1);
        cursor: not-allowed;
    }
    .menu-card .thumb img { transition: transform 0.6s ease; }
    .menu-card:hover:not(.out-of-stock) .thumb img { transform: scale(1.08); }

    /* ── OOS Badge ── */
    .oos-badge {
        position: absolute;
        top: 10px; right: 10px;
        background: #ef4444;
        color: #fff;
        padding: 3px 9px;
        border-radius: 8px;
        font-size: 8px;
        font-weight: 800;
        letter-spacing: 0.08em;
        z-index: 10;
    }

    /* ── Modal ── */
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15,23,42,0.45);
        backdrop-filter: blur(10px);
        z-index: 999;
        align-items: center;
        justify-content: center;
    }
    .modal-overlay.show { display: flex; }

    /* ── Option Buttons ── */
    .opt-choice-btn {
        transition: border-color 0.2s, background-color 0.2s, box-shadow 0.2s;
        border-width: 2px;
    }
    .opt-choice-btn.active {
        border-color: #D4E971 !important;
        background-color: rgba(212,233,113,0.12) !important;
        box-shadow: 0 4px 14px -4px rgba(212,233,113,0.5);
    }

    /* ── Modal Options Scroll ── */
    #modal-options {
        max-height: 380px;
        overflow-y: auto;
        padding-right: 2px;
        scrollbar-width: thin;
        scrollbar-color: #eee transparent;
    }

    /* ── Cart Item ── */
    .cart-item {
        background: #f8fafc;
        border: 1.5px solid #f1f5f9;
        transition: border-color 0.2s;
    }
    .cart-item:hover { border-color: #D4E971; }

    /* ── Category Pills ── */
    .cat-pill {
        white-space: nowrap;
        transition: background 0.2s, color 0.2s;
    }

    /* ── Filter Bar ── */
    .filter-select {
        background-image: none;
        cursor: pointer;
    }
</style>

<div class="max-w-[1600px] mx-auto p-4 md:p-6">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- ════════════════════════════════════════
             AREA MENU — KIRI
        ════════════════════════════════════════ --}}
        <div class="lg:col-span-8 flex flex-col gap-5">

            {{-- Toolbar: Search + Kategori --}}
            <div class="flex flex-col md:flex-row gap-3 items-center justify-between">

                {{-- Search --}}
                <form action="{{ route('cashier.orders.index') }}" method="GET" class="relative w-full md:w-72 group">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 group-focus-within:text-[#D4E971] transition-colors pointer-events-none"
                        fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari menu..."
                        class="w-full pl-11 pr-4 py-3 bg-white border border-slate-200 rounded-2xl text-xs font-bold
                               focus:ring-4 focus:ring-[#D4E971]/20 focus:border-[#D4E971] outline-none transition-all shadow-sm">
                </form>

                {{-- Kategori Pills --}}
                <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-1 w-full md:w-auto">
                    <a href="{{ route('cashier.orders.index') }}"
                        class="cat-pill px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-wider
                               {{ !request('category') ? 'bg-slate-900 text-white shadow-md' : 'bg-white text-slate-400 border border-slate-200 hover:border-slate-300' }}">
                        Semua
                    </a>
                    @foreach($categories as $cat)
                        <a href="{{ route('cashier.orders.index', ['category' => $cat->id]) }}"
                            class="cat-pill px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-wider
                                   {{ request('category') == $cat->id ? 'bg-slate-900 text-white shadow-md' : 'bg-white text-slate-400 border border-slate-200 hover:border-slate-300' }}">
                            {{ $cat->name }}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Filter Bar --}}
            <div class="flex flex-wrap items-center gap-3 bg-slate-50 px-5 py-3 rounded-2xl border border-slate-100">
                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Filter:</span>
                <select onchange="applyFilter('stock', this.value)"
                    class="filter-select text-[10px] font-bold bg-white border border-slate-200 rounded-xl py-2 pl-3 pr-8 shadow-sm outline-none focus:border-[#D4E971]">
                    <option value="">Status Stok</option>
                    <option value="available" {{ request('stock') === 'available' ? 'selected' : '' }}>Tersedia</option>
                    <option value="empty"     {{ request('stock') === 'empty'     ? 'selected' : '' }}>Habis</option>
                </select>
                <select onchange="applyFilter('sort', this.value)"
                    class="filter-select text-[10px] font-bold bg-white border border-slate-200 rounded-xl py-2 pl-3 pr-8 shadow-sm outline-none focus:border-[#D4E971]">
                    <option value="">Urutan Harga</option>
                    <option value="price_low"  {{ request('sort') === 'price_low'  ? 'selected' : '' }}>Termurah</option>
                    <option value="price_high" {{ request('sort') === 'price_high' ? 'selected' : '' }}>Termahal</option>
                </select>
            </div>

            {{-- Grid Menu --}}
            <div class="grid grid-cols-2 xl:grid-cols-4 gap-4 overflow-y-auto no-scrollbar pr-1" style="max-height:62vh;">
                @forelse($menus as $menu)
                    <div class="menu-card relative rounded-3xl overflow-hidden {{ !$menu->is_available ? 'out-of-stock' : 'cursor-pointer' }}"
                        @if($menu->is_available)
                            onclick="openModal(this)"
                            data-id="{{ $menu->id }}"
                            data-name="{{ $menu->name }}"
                            data-price="{{ $menu->price }}"
                            data-image="{{ $menu->image ? asset('images/menu/'.$menu->image) : 'https://placehold.co/300x300/f1f5f9/94a3b8?text=Menu' }}"
                            data-options='@json($menu->options)'
                        @endif>

                        @if(!$menu->is_available)
                            <div class="oos-badge">HABIS</div>
                        @endif

                        <div class="thumb h-36 overflow-hidden bg-slate-100">
                            <img src="{{ $menu->image ? asset('images/menu/'.$menu->image) : 'https://placehold.co/300x300/f1f5f9/94a3b8?text=Menu' }}"
                                class="w-full h-full object-cover" alt="{{ $menu->name }}">
                        </div>

                        <div class="p-4">
                            <h3 class="text-[10px] font-extrabold text-slate-700 uppercase leading-tight truncate mb-1">
                                {{ $menu->name }}
                            </h3>
                            <p class="text-sm font-black text-slate-900">
                                <span class="text-[9px] font-normal opacity-40">Rp</span>{{ number_format($menu->price, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-20 text-center">
                        <p class="text-[9px] font-black uppercase tracking-[0.3em] text-slate-300">Menu tidak ditemukan</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- ════════════════════════════════════════
             KERANJANG — KANAN
        ════════════════════════════════════════ --}}
        <div class="lg:col-span-4">
            <form id="pos-form" action="{{ route('cashier.orders.store') }}" method="POST">
                @csrf
                <input type="hidden" name="payment_method" id="payment_method">

                <div class="bg-white rounded-[2.5rem] p-6 border border-slate-200 shadow-xl shadow-slate-100/80
                            sticky top-6 flex flex-col" style="max-height:90vh;">

                    {{-- Header --}}
                    <div class="flex justify-between items-center mb-5 pb-4 border-b border-slate-100 shrink-0">
                        <div class="flex items-center gap-2">
                            <span class="w-1.5 h-5 bg-[#D4E971] rounded-full block"></span>
                            <h2 class="font-black text-base uppercase tracking-tight text-slate-800">Keranjang</h2>
                        </div>
                        <span id="cart-badge"
                            class="px-3 py-1 bg-slate-900 text-[#D4E971] rounded-lg font-black text-[9px] uppercase tracking-wider">
                            0 Item
                        </span>
                    </div>

                    {{-- Form Pelanggan --}}
                    <div class="space-y-2.5 mb-5 shrink-0">
                        <input type="text" name="customer_name" placeholder="Nama Pelanggan *" required
                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-[11px] font-bold
                                   focus:border-[#D4E971] focus:ring-4 focus:ring-[#D4E971]/10 outline-none transition-all">
                        <input type="email" name="email" placeholder="Email (Notifikasi Midtrans)"
                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-[11px] font-bold
                                   focus:border-[#D4E971] focus:ring-4 focus:ring-[#D4E971]/10 outline-none transition-all">
                        <input type="text" name="phone" placeholder="No. HP (Opsional)"
                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-[11px] font-bold
                                   focus:border-[#D4E971] focus:ring-4 focus:ring-[#D4E971]/10 outline-none transition-all">

                        <div id="order-type-wrapper" class="grid grid-cols-2 gap-2">
                            <select name="order_type" id="order_type" onchange="toggleTable()"
                                class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-[10px] font-black
                                       uppercase outline-none focus:border-[#D4E971] transition-all">
                                <option value="dine_in">Dine In</option>
                                <option value="takeaway">Takeaway</option>
                            </select>

                            <div id="table-wrapper">
                                <select name="table_id"
                                    class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-[10px] font-black
                                           uppercase outline-none focus:border-[#D4E971] transition-all">
                                    <option value="">Pilih Meja</option>
                                    @foreach($tables as $table)
                                        <option value="{{ $table->id }}">Meja #{{ $table->table_number }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <textarea name="notes" rows="2" placeholder="Catatan pesanan..."
                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-[11px] font-bold
                                   focus:border-[#D4E971] focus:ring-4 focus:ring-[#D4E971]/10 outline-none transition-all resize-none">
                        </textarea>
                    </div>

                    {{-- Cart Items --}}
                    <div id="cart-list" class="space-y-2 overflow-y-auto grow no-scrollbar min-h-[80px]">
                        <div class="py-10 text-center">
                            <p class="text-[9px] font-black uppercase tracking-[0.25em] text-slate-300">Keranjang Kosong</p>
                        </div>
                    </div>

                    {{-- Hidden inputs for items --}}
                    <div id="hidden-inputs"></div>

                    {{-- Total & Action --}}
                    <div class="mt-auto pt-5 border-t-2 border-dashed border-slate-100 shrink-0">
                        <div class="flex justify-between items-end mb-5">
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Total Bayar</span>
                            <h2 id="total-display" class="text-2xl font-black text-slate-900">Rp 0</h2>
                        </div>
                        <button type="button" onclick="openPaymentModal()"
                            class="w-full bg-[#D4E971] hover:bg-slate-900 hover:text-[#D4E971] text-slate-900 py-4 rounded-2xl
                                   text-[10px] font-black uppercase tracking-widest shadow-lg transition-all active:scale-95">
                            Proses Pesanan
                        </button>
                    </div>

                </div>
            </form>
        </div>

    </div>
</div>

{{-- ════════════════════════════════════════════════════════
     MODAL: DETAIL MENU & PILIHAN
════════════════════════════════════════════════════════ --}}
<div id="modal-overlay" class="modal-overlay" onclick="closeMenuModal()">
    <div class="bg-white w-full max-w-md rounded-[2.5rem] overflow-hidden shadow-2xl mx-4"
        onclick="event.stopPropagation()">

        <div class="relative h-44">
            <img id="modal-img" class="w-full h-full object-cover" alt="">
        </div>

        <div class="p-6">
            <h3 id="modal-name" class="text-lg font-black text-slate-800 uppercase tracking-tight"></h3>
            <p id="modal-base-price" class="text-[10px] font-bold text-slate-400 uppercase mt-1 mb-5"></p>
            <div id="modal-options" class="space-y-5"></div>
            <textarea id="modal-note" rows="2" placeholder="Catatan item ini..."
                class="w-full mt-4 bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-[11px] font-bold
                       focus:border-[#D4E971] outline-none transition-all resize-none">
            </textarea>
        </div>

        <div class="flex justify-between items-center px-6 py-5 bg-slate-50 border-t border-slate-100">
            <div>
                <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest block mb-0.5">Subtotal</span>
                <strong id="modal-total" class="text-xl font-black text-slate-900">Rp 0</strong>
            </div>
            <button type="button" onclick="confirmAdd()"
                class="bg-slate-900 text-[#D4E971] px-7 py-3.5 rounded-2xl text-[10px] font-black uppercase
                       tracking-widest shadow-lg active:scale-95 transition-transform">
                Tambahkan
            </button>
        </div>
    </div>
</div>

{{-- ════════════════════════════════════════════════════════
     MODAL: PEMBAYARAN
════════════════════════════════════════════════════════ --}}
<div id="payment-modal" class="modal-overlay">
    <div class="bg-white w-full max-w-sm rounded-[2.5rem] p-8 shadow-2xl border border-slate-100 mx-4 text-center">

        <h2 class="text-xl font-black text-slate-800 uppercase tracking-tight mb-1">Konfirmasi Bayar</h2>
        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-7">Pilih metode pembayaran</p>

        {{-- Step 1 --}}
        <div id="pay-step-1" class="space-y-3">
            <button onclick="showPayStep2()"
                class="w-full py-4 rounded-2xl bg-slate-900 text-[#D4E971] font-black uppercase text-[10px] tracking-widest
                       shadow-lg active:scale-95 transition-transform">
                Pilih Metode Pembayaran
            </button>
            <button onclick="cancelPayment()"
                class="w-full py-3 text-slate-400 font-black uppercase text-[9px] tracking-widest hover:text-red-400 transition-colors">
                Batalkan
            </button>
        </div>

        {{-- Step 2 --}}
        <div id="pay-step-2" class="hidden space-y-3">
            <button onclick="submitPayment('cash')"
                class="w-full flex items-center gap-4 px-6 py-4 rounded-2xl border-2 border-slate-100
                       hover:border-[#D4E971] hover:bg-[#D4E971]/5 transition-all text-left">
                <span class="text-xl">💵</span>
                <span class="font-black uppercase text-xs text-slate-700">Tunai / Cash</span>
            </button>
            <button onclick="submitPayment('qris')"
                class="w-full flex items-center gap-4 px-6 py-4 rounded-2xl border-2 border-slate-100
                       hover:border-blue-400 hover:bg-blue-50/50 transition-all text-left">
                <span class="text-xl">📱</span>
                <span class="font-black uppercase text-xs text-slate-700">QRIS / Snap</span>
            </button>
            <button onclick="cancelPayment()"
                class="w-full mt-2 text-[9px] font-black uppercase tracking-widest text-slate-300 hover:text-red-400 transition-colors">
                Kembali
            </button>
        </div>

    </div>
</div>

{{-- ════════════════════════════════════════════════════════
     MODAL: PROCESSING
════════════════════════════════════════════════════════ --}}
<div id="processing-modal" class="modal-overlay">
    <div class="bg-white rounded-3xl p-10 text-center w-72 shadow-2xl mx-4">
        <div class="w-12 h-12 border-4 border-[#D4E971] border-t-slate-900 rounded-full animate-spin mx-auto mb-5"></div>
        <h2 class="text-base font-black text-slate-800 uppercase tracking-tight">Processing...</h2>
        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">Memproses pesanan</p>
    </div>
</div>

{{-- ════════════════════════════════════════════════════════
     SCRIPTS
════════════════════════════════════════════════════════ --}}
<script>
    // ── State ─────────────────────────────────────────────────────────
    let cart            = [];
    let currentItem     = null;
    let currentSelected = {};

    // ── Helpers Modal ──────────────────────────────────────────────────
    const showModal = id => document.getElementById(id).classList.add('show');
    const hideModal = id => document.getElementById(id).classList.remove('show');

    // ── Filter URL ─────────────────────────────────────────────────────
    function applyFilter(key, value) {
        const url = new URL(window.location.href);
        value ? url.searchParams.set(key, value) : url.searchParams.delete(key);
        window.location.href = url.toString();
    }

    // ── Toggle Meja ────────────────────────────────────────────────────
    function toggleTable() {
        const type      = document.getElementById('order_type').value;
        const wrapper   = document.getElementById('table-wrapper');
        const grid      = document.getElementById('order-type-wrapper');
        const isTakeaway = type === 'takeaway';

        wrapper.classList.toggle('hidden', isTakeaway);
        grid.classList.toggle('grid-cols-1', isTakeaway);
        grid.classList.toggle('grid-cols-2', !isTakeaway);
    }

    // ── Payment Modal ──────────────────────────────────────────────────
    function openPaymentModal() {
        if (cart.length === 0) return alert('Pilih menu terlebih dahulu!');
        const type    = document.getElementById('order_type').value;
        const tableEl = document.querySelector('[name="table_id"]');
        if (type === 'dine_in' && !tableEl?.value) return alert('Pilih nomor meja!');
        showModal('payment-modal');
    }

    function showPayStep2() {
        document.getElementById('pay-step-1').classList.add('hidden');
        document.getElementById('pay-step-2').classList.remove('hidden');
    }

    function cancelPayment() {
        if (!confirm('Batalkan pembayaran? Pesanan belum tersimpan.')) return;
        hideModal('payment-modal');
        document.getElementById('pay-step-1').classList.remove('hidden');
        document.getElementById('pay-step-2').classList.add('hidden');
    }

    // ── Submit Order ───────────────────────────────────────────────────
    function submitPayment(method) {
        document.getElementById('payment_method').value = method;

        const form = document.getElementById('pos-form');
        hideModal('payment-modal');
        showModal('processing-modal');

        fetch(form.action, {
            method:  'POST',
            body:    new FormData(form),
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept':       'application/json',
            },
        })
        .then(res => res.json())
        .then(res => {
            if (res.status === 'error' || res.errors) {
                alert(res.message || 'Terjadi kesalahan validasi.');
                hideModal('processing-modal');
                return;
            }
            if (res.type === 'cash') {
                window.location.href = '/cashier/receipt/' + res.order_id;
                return;
            }
            // QRIS via Midtrans Snap
            snap.pay(res.snap_token, {
                onSuccess: () => { window.location.href = '/cashier/receipt/' + res.order_id; },
                onPending: () => { alert('Pending — pesanan masuk antrean.'); window.location.href = '/cashier/orders'; },
                onClose:   () => { alert('Pembayaran ditutup. Pesanan tersimpan di antrean.'); window.location.href = '/cashier/orders'; },
            });
        })
        .catch(() => {
            alert('Gagal memproses pesanan. Coba lagi.');
            hideModal('processing-modal');
        });
    }

    // ── Menu Modal ─────────────────────────────────────────────────────
    function openModal(card) {
        currentItem = {
            id:      card.dataset.id,
            name:    card.dataset.name,
            price:   parseInt(card.dataset.price),
            image:   card.dataset.image,
            options: JSON.parse(card.dataset.options),
        };
        currentSelected = {};

        document.getElementById('modal-img').src              = currentItem.image;
        document.getElementById('modal-name').innerText       = currentItem.name;
        document.getElementById('modal-base-price').innerText = 'Harga dasar: Rp ' + currentItem.price.toLocaleString('id');
        document.getElementById('modal-note').value           = '';
        document.getElementById('modal-options').innerHTML    = buildOptionsHTML(currentItem.options);

        updateModalTotal();
        showModal('modal-overlay');
    }

    function closeMenuModal() { hideModal('modal-overlay'); }

    function buildOptionsHTML(options) {
        return options.map(opt => {
            const isSelect = opt.type === 'select';
            const badge    = isSelect
                ? '<span class="bg-amber-100 text-amber-600 text-[8px] font-black px-2 py-0.5 rounded-lg uppercase">Pilih 1</span>'
                : '<span class="bg-blue-100 text-blue-600 text-[8px] font-black px-2 py-0.5 rounded-lg uppercase">Multi</span>';

            const itemsHTML = opt.items.map(i => {
                if (isSelect) {
                    return `
                        <button type="button" id="opt-btn-${i.id}"
                            onclick="selectOption(${i.id}, ${i.price}, '${i.name}', ${opt.id})"
                            class="opt-choice-btn py-3 px-2 rounded-2xl border-2 border-slate-100 bg-white
                                   hover:border-[#D4E971]/50 flex flex-col items-center justify-center gap-1">
                            <span class="text-[9px] font-black text-slate-700 uppercase leading-tight text-center">${i.name}</span>
                            <span class="text-[8px] font-bold text-slate-400">+Rp ${i.price.toLocaleString('id')}</span>
                        </button>`;
                }
                return `
                    <div class="flex items-center justify-between bg-slate-50 py-2.5 px-4 rounded-2xl border border-slate-100">
                        <div>
                            <span class="text-[10px] font-black text-slate-700 uppercase block">${i.name}</span>
                            <span class="text-[8px] font-bold text-slate-400">+Rp ${i.price.toLocaleString('id')}</span>
                        </div>
                        <div class="flex items-center gap-3 bg-white px-2 py-1 rounded-xl shadow-sm border border-slate-200">
                            <button type="button" onclick="stepOption(${i.id}, -1, ${i.price}, '${i.name}', ${opt.id})"
                                class="w-7 h-7 flex items-center justify-center text-slate-300 hover:text-red-500 font-black text-base transition-colors">−</button>
                            <span id="qty-opt-${i.id}" class="text-[11px] font-black text-slate-800 min-w-[16px] text-center">0</span>
                            <button type="button" onclick="stepOption(${i.id}, 1, ${i.price}, '${i.name}', ${opt.id})"
                                class="bg-slate-900 text-[#D4E971] w-7 h-7 rounded-lg flex items-center justify-center text-xs shadow-sm active:scale-90 transition-transform">+</button>
                        </div>
                    </div>`;
            }).join('');

            const gridClass = isSelect ? 'grid grid-cols-3 gap-2' : 'space-y-2';
            return `
                <div>
                    <div class="flex justify-between items-center mb-2 px-1">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">${opt.name}</span>
                        ${badge}
                    </div>
                    <div class="${gridClass}">${itemsHTML}</div>
                </div>`;
        }).join('');
    }

    // ── Option Logic ───────────────────────────────────────────────────
    function selectOption(itemId, price, name, groupId) {
        // Reset grup, aktifkan pilihan
        const group = currentItem.options.find(o => o.id == groupId);
        group.items.forEach(i => {
            document.getElementById(`opt-btn-${i.id}`)?.classList.remove('active');
            if (i.id != itemId) delete currentSelected[i.id];
        });
        currentSelected[itemId] = { id: itemId, qty: 1, price, name };
        document.getElementById(`opt-btn-${itemId}`)?.classList.add('active');
        updateModalTotal();
    }

    function stepOption(itemId, delta, price, name, groupId) {
        if (!currentSelected[itemId]) currentSelected[itemId] = { id: itemId, qty: 0, price, name };
        currentSelected[itemId].qty += delta;
        if (currentSelected[itemId].qty <= 0) delete currentSelected[itemId];
        const el = document.getElementById(`qty-opt-${itemId}`);
        if (el) el.innerText = currentSelected[itemId]?.qty ?? 0;
        updateModalTotal();
    }

    function updateModalTotal() {
        const extra = Object.values(currentSelected).reduce((s, o) => s + o.price * o.qty, 0);
        document.getElementById('modal-total').innerText = 'Rp ' + (currentItem.price + extra).toLocaleString('id');
    }

    // ── Cart ───────────────────────────────────────────────────────────
    function confirmAdd() {
        cart.push({
            ...currentItem,
            selectedOptions: { ...currentSelected },
            notes: document.getElementById('modal-note').value.trim(),
        });
        closeMenuModal();
        renderCart();
    }

    function removeItem(index) {
        cart.splice(index, 1);
        renderCart();
    }

    function renderCart() {
        const listEl   = document.getElementById('cart-list');
        const hiddenEl = document.getElementById('hidden-inputs');
        hiddenEl.innerHTML = '';

        if (cart.length === 0) {
            listEl.innerHTML = `<div class="py-10 text-center">
                <p class="text-[9px] font-black uppercase tracking-[0.25em] text-slate-300">Keranjang Kosong</p>
            </div>`;
            document.getElementById('total-display').innerText = 'Rp 0';
            document.getElementById('cart-badge').innerText    = '0 Item';
            return;
        }

        let total = 0;
        let html  = '';

        cart.forEach((item, i) => {
            const extra = Object.values(item.selectedOptions).reduce((s, o) => s + o.price * o.qty, 0);
            const sub   = item.price + extra;
            total += sub;

            const optsHTML = Object.values(item.selectedOptions)
                .map(o => `<span class="bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded text-[8px] font-bold">${o.qty}× ${o.name}</span>`)
                .join('');

            html += `
                <div class="cart-item p-4 rounded-2xl">
                    <div class="flex justify-between items-start gap-3">
                        <div class="min-w-0 flex-1">
                            <span class="text-[11px] font-black uppercase text-slate-800 block truncate">${item.name}</span>
                            <div class="flex flex-wrap gap-1 mt-1">${optsHTML}</div>
                            ${item.notes ? `<p class="text-[9px] text-blue-500 italic mt-1">"${item.notes}"</p>` : ''}
                        </div>
                        <div class="text-right shrink-0">
                            <span class="text-xs font-black text-slate-900 block">Rp${sub.toLocaleString('id')}</span>
                            <button type="button" onclick="removeItem(${i})"
                                class="text-[8px] font-black text-red-400 hover:text-red-600 uppercase mt-1.5 transition-colors">
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>`;

            // Hidden inputs
            const addInput = (name, val) => {
                const inp = document.createElement('input');
                inp.type = 'hidden'; inp.name = name; inp.value = val;
                hiddenEl.appendChild(inp);
            };

            addInput(`items[${i}][menu_id]`, item.id);
            addInput(`items[${i}][notes]`,   item.notes);

            let optIdx = 0;
            Object.values(item.selectedOptions).forEach(opt => {
                for (let q = 0; q < opt.qty; q++) {
                    addInput(`items[${i}][options][${optIdx++}]`, opt.id);
                }
            });
        });

        listEl.innerHTML = html;
        document.getElementById('total-display').innerText = 'Rp ' + total.toLocaleString('id');
        document.getElementById('cart-badge').innerText    = cart.length + ' Item';
    }

    // ── Init ───────────────────────────────────────────────────────────
    window.addEventListener('DOMContentLoaded', toggleTable);
</script>

</x-layouts.cashier>