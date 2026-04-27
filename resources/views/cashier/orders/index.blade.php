<x-layouts.cashier title="Kasir POS">
    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('midtrans.clientKey') }}"></script>

    <style>
        .menu-card {
            transition: border-color 0.15s, transform 0.1s;
        }

        .menu-card:not(.out-of-stock):hover {
            border-color: #7F77DD;
            transform: translateY(-2px);
        }

        .menu-card img {
            transition: transform 0.2s;
        }

        .menu-card:not(.out-of-stock):hover img {
            transform: scale(1.05);
        }

        /* Efek Menu Habis */
        .menu-card.out-of-stock {
            filter: grayscale(1);
            opacity: 0.6;
            cursor: not-allowed;
        }

        .out-of-stock-label {
            position: absolute;
            top: 8px;
            right: 8px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            z-index: 10;
        }

        .opt-btn.active {
            background: #EEEDFE;
            border-color: #7F77DD;
            color: #534AB7;
        }

        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.4);
            z-index: 999;
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.show {
            display: flex;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>

    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

            {{-- ================= MENU SECTION (KIRI) ================= --}}
            <div class="md:col-span-2">

                {{-- Baris Pencarian (FORM TERPISAH) --}}
                <div class="mb-4">
                    <form action="{{ route('cashier.orders.index') }}" method="GET" id="search-form" class="relative">
                        @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                        @endif
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" name="search" id="search-input" value="{{ request('search') }}"
                            class="block w-full pl-10 pr-10 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-violet-200 focus:border-violet-500 transition-all shadow-sm"
                            placeholder="Cari menu..." autocomplete="off">

                        @if(request('search'))
                        <a href="{{ route('cashier.orders.index', ['category' => request('category')]) }}"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                        </a>
                        @endif
                    </form>
                </div>

                {{-- Filter Kategori --}}
                <div class="flex items-center gap-2 mb-6 overflow-x-auto pb-2 no-scrollbar">
                    <a href="{{ route('cashier.orders.index', ['search' => request('search')]) }}"
                        class="px-5 py-2 rounded-full text-xs font-semibold transition-all whitespace-nowrap {{ !request('category') ? 'bg-violet-600 text-white shadow-md' : 'bg-white text-gray-500 border border-gray-200 hover:bg-gray-50' }}">
                        Semua
                    </a>
                    @foreach($categories as $cat)
                    <a href="{{ route('cashier.orders.index', ['category' => $cat->id, 'search' => request('search')]) }}"
                        class="px-5 py-2 rounded-full text-xs font-semibold transition-all whitespace-nowrap {{ request('category') == $cat->id ? 'bg-violet-600 text-white shadow-md' : 'bg-white text-gray-500 border border-gray-200 hover:bg-gray-50' }}">
                        {{ $cat->name }}
                    </a>
                    @endforeach
                </div>
                <div class="flex flex-wrap items-center gap-4 mb-6 px-1">
                    {{-- Dropdown Stok --}}
                    <div class="flex items-center gap-2">
                        <label class="text-[10px] font-bold text-gray-400 uppercase">Stok:</label>
                        <select onchange="applyExtraFilter('stock', this.value)"
                            class="text-xs border-none bg-gray-100 rounded-lg focus:ring-2 focus:ring-violet-200 py-1 pr-8">
                            <option value="">Semua</option>
                            <option value="available" {{ request('stock') == 'available' ? 'selected' : '' }}>Tersedia</option>
                            <option value="empty" {{ request('stock') == 'empty' ? 'selected' : '' }}>Habis</option>
                        </select>
                    </div>

                    {{-- Dropdown Urutan --}}
                    <div class="flex items-center gap-2">
                        <label class="text-[10px] font-bold text-gray-400 uppercase">Urutkan:</label>
                        <select onchange="applyExtraFilter('sort', this.value)"
                            class="text-xs border-none bg-gray-100 rounded-lg focus:ring-2 focus:ring-violet-200 py-1 pr-8">
                            <option value="">Default</option>
                            <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Harga Termurah</option>
                            <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Harga Termahal</option>
                        </select>
                    </div>

                    {{-- Reset Filter --}}
                    @if(request('stock') || request('sort') || request('search') || request('category'))
                    <a href="{{ route('cashier.orders.index') }}" class="text-[10px] font-bold text-red-500 hover:text-red-700 uppercase underline decoration-2 underline-offset-4">
                        Bersihkan Filter
                    </a>
                    @endif
                </div>

                {{-- Label Status --}}
                <div class="flex items-center justify-between mb-4 px-1">
                    <h2 class="text-sm font-bold text-gray-800 uppercase tracking-wide">
                        @if(request('search'))
                        Hasil Pencarian: "{{ request('search') }}"
                        @else
                        {{ request('category') ? $categories->find(request('category'))->name : 'Daftar Menu' }}
                        @endif
                    </h2>
                    <span class="text-xs bg-gray-100 text-gray-600 font-medium px-3 py-1 rounded-full">
                        {{ $menus->count() }} Item
                    </span>
                </div>

                {{-- Grid Menu --}}
                <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse($menus as $menu)
                    @php $isAvailable = $menu->is_available; @endphp
                    <div class="menu-card relative bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm {{ !$isAvailable ? 'out-of-stock' : 'cursor-pointer' }}"
                        @if($isAvailable)
                        data-id="{{ $menu->id }}"
                        data-name="{{ $menu->name }}"
                        data-price="{{ $menu->price }}"
                        data-image="{{ $menu->image ? asset('images/menu/'.$menu->image) : 'https://via.placeholder.com/300x200' }}"
                        data-options='@json($menu->options)'
                        onclick="openModal(this)"
                        @endif>

                        @if(!$isAvailable)
                        <div class="out-of-stock-label">HABIS</div>
                        @endif

                        <div class="relative h-32 overflow-hidden">
                            <img src="{{ $menu->image ? asset('images/menu/'.$menu->image) : 'https://via.placeholder.com/300x200' }}"
                                class="w-full h-full object-cover">
                        </div>

                        <div class="p-3">
                            <p class="text-sm font-semibold text-gray-800 truncate">{{ $menu->name }}</p>
                            <p class="text-sm text-violet-600 font-bold mt-1">
                                Rp {{ number_format($menu->price) }}
                            </p>
                            <button type="button"
                                {{ !$isAvailable ? 'disabled' : '' }}
                                class="w-full mt-3 text-xs py-2 rounded-xl font-medium transition-colors {{ $isAvailable ? 'bg-violet-50 text-violet-700 hover:bg-violet-600 hover:text-white' : 'bg-gray-100 text-gray-400' }}"
                                onclick="event.stopPropagation(); {{ $isAvailable ? 'openModal(this.closest(\'.menu-card\'))' : '' }}">
                                {{ $isAvailable ? '+ Tambahkan' : 'Stok Habis' }}
                            </button>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full py-20 text-center">
                        <p class="text-gray-400 text-sm">Menu tidak ditemukan.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- ================= CART SECTION (KANAN) ================= --}}
            <div class="relative">
                <form method="POST" action="{{ route('cashier.orders.store') }}" id="pos-form">

                    @csrf
                    <div class="bg-white rounded-2xl border border-gray-100 p-5 sticky top-5 shadow-lg">
                        <div class="flex justify-between items-center mb-5">
                            <h2 class="font-bold text-gray-800">Keranjang</h2>
                            <span id="cart-badge" class="text-xs bg-violet-600 text-white px-2.5 py-1 rounded-lg font-bold">
                                0
                            </span>
                        </div>
                        <div id="pending-payment-box" class="hidden bg-yellow-50 border border-yellow-200 p-3 rounded-xl mt-3">
                            <p class="text-xs text-gray-500">Pembayaran Pending</p>
                            <p id="pending-info" class="text-sm font-bold text-gray-800"></p>

                            <button type="button" onclick="resumePayment()"
                                class="w-full mt-2 bg-orange-500 text-white py-2 rounded-xl text-sm font-bold">
                                🔁 Lanjutkan Pembayaran
                            </button>
                        </div>

                        <input type="hidden" name="payment_method" id="payment_method" required>

                        <div class="space-y-3">
                            <input type="text" name="customer_name" placeholder="Nama pelanggan" required
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-violet-200 focus:border-violet-500">

                            <input type="text" name="phone" placeholder="No. HP (Opsional)"
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-violet-200 focus:border-violet-500">

                            {{-- Container untuk Order Type & Table --}}
                            <div id="order-selection-container" class="grid grid-cols-2 gap-2 transition-all duration-300">
                                <select name="order_type" id="order_type"
                                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-violet-200 focus:border-violet-500 transition-all duration-300">
                                    <option value="dine_in">Dine In</option>
                                    <option value="takeaway">Takeaway</option>
                                </select>

                                <div id="table-wrapper">
                                    <select name="table_id"
                                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-violet-200 focus:border-violet-500">
                                        <option value="">Meja</option>
                                        @foreach($tables as $table)
                                        <option value="{{ $table->id }}">#{{ $table->table_number }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <textarea name="notes" rows="2"
                                class="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-violet-200 focus:border-violet-500"
                                placeholder="Catatan global..."></textarea>
                        </div>

                        <div id="cart-list" class="mt-6 space-y-3 max-h-[40vh] overflow-y-auto pr-1 no-scrollbar">
                            <div class="text-center py-10">
                                <p class="text-xs text-gray-400">Keranjang masih kosong</p>
                            </div>
                        </div>

                        <div id="hidden-inputs"></div>

                        <div class="mt-6 pt-5 border-t border-dashed border-gray-200">
                            <div class="flex justify-between items-center mb-4">
                                <span class="text-sm text-gray-500 font-medium">Total Pembayaran</span>
                                <strong id="total-display" class="text-xl text-gray-900">Rp 0</strong>
                            </div>

                            <button type="button" id="submit-btn" onclick="openPaymentModal()"
                                class="w-full bg-violet-600 hover:bg-violet-700 text-white py-3.5 rounded-xl text-sm font-bold shadow-md transition-all active:scale-[0.98]">
                                BUAT PESANAN
                            </button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
        {{-- ================= MODAL DETAIL & OPTION ================= --}}
        <div id="modal-overlay" class="modal-overlay" onclick="closeModalOutside(event)">
            <div class="bg-white w-full max-w-sm rounded-2xl overflow-hidden shadow-2xl mx-4" onclick="event.stopPropagation()">
                <div class="relative">
                    <img id="modal-img" class="w-full h-48 object-cover">
                    <button type="button" onclick="closeModalOutside()" class="absolute top-3 right-3 bg-white/80 rounded-full p-1 shadow-sm">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="p-5">
                    <div class="mb-4">
                        <h3 id="modal-name" class="text-lg font-bold text-gray-800"></h3>
                        <p id="modal-base-price" class="text-sm text-violet-600 font-semibold"></p>
                    </div>

                    <div id="modal-options" class="space-y-4 mb-4">
                        {{-- Opsi menu akan muncul di sini via JS --}}
                    </div>

                    <div class="mb-4">
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Catatan Item</label>
                        <textarea id="modal-note" rows="2"
                            class="w-full mt-2 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-violet-200 focus:border-violet-500"
                            placeholder="Contoh: Less ice, pedas sedang..."></textarea>
                    </div>
                </div>

                <div class="flex justify-between items-center p-5 bg-gray-50 border-t">
                    <div>
                        <p class="text-[10px] text-gray-400 uppercase font-bold">Subtotal</p>
                        <strong id="modal-total" class="text-lg text-gray-800">Rp 0</strong>
                    </div>
                    <button type="button" onclick="confirmAdd()"
                        class="bg-violet-600 hover:bg-violet-700 text-white px-8 py-3 rounded-xl text-sm font-bold shadow-sm transition-all">
                        SIMPAN
                    </button>
                </div>
            </div>
        </div>

        {{-- ================= MODAL PAYMENT ================= --}}
        <div id="payment-modal" class="modal-overlay">
            <div class="bg-white w-full max-w-sm rounded-2xl p-5 shadow-xl">

                <h2 class="text-lg font-bold mb-4 text-center">
                    Konfirmasi Pesanan
                </h2>

                <p class="text-sm text-gray-500 text-center mb-5">
                    Pilih metode pembayaran
                </p>

                {{-- STEP 1 --}}
                <div id="payment-step-1" class="flex gap-3">
                    <button type="button" onclick="closePaymentModal()"
                        class="w-1/2 py-3 rounded-xl bg-gray-100 font-semibold">
                        Batal
                    </button>

                    <button type="button" onclick="showPaymentMethods()"
                        class="w-1/2 py-3 rounded-xl bg-violet-600 text-white font-semibold">
                        Lanjut
                    </button>
                </div>

                {{-- STEP 2 --}}
                <div id="payment-step-2" class="hidden space-y-3 mt-3">

                    <button type="button" onclick="submitPayment('cash')"
                        class="w-full py-3 rounded-xl bg-green-600 text-white font-bold">
                        💵 Cash
                    </button>
                    <button type="button" onclick="submitPayment('qris')"
                        class="w-full py-3 rounded-xl bg-blue-600 text-white font-bold">
                        📱 QRIS
                    </button>

                </div>

            </div>
        </div>

        {{-- ================= SUCCESS MODAL ================= --}}
        <div id="success-modal" class="modal-overlay">
            <div class="bg-white rounded-2xl p-6 text-center w-80">

                <h2 class="text-lg font-bold text-green-600 mb-2">
                    ✅ Pembayaran Berhasil
                </h2>

                <p class="text-sm text-gray-500 mb-4">
                    Sedang memproses pesanan...
                </p>

                <div class="animate-spin w-8 h-8 border-4 border-violet-500 border-t-transparent rounded-full mx-auto"></div>

            </div>
        </div>
    </div>




    <script>
        function applyExtraFilter(key, value) {
            // Ambil URL saat ini
            let url = new URL(window.location.href);

            // Set parameter baru
            if (value) {
                url.searchParams.set(key, value);
            } else {
                url.searchParams.delete(key);
            }

            // Redirect ke URL baru
            window.location.href = url.toString();
        }

        // Tambahkan juga ke form search agar parameter extra tidak hilang saat mencari
        document.getElementById('search-form').addEventListener('submit', function(e) {
            const urlParams = new URLSearchParams(window.location.search);

            // Jika ada filter stock/sort, masukkan sebagai hidden input sebelum submit
            ['stock', 'sort'].forEach(param => {
                if (urlParams.has(param)) {
                    let input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = param;
                    input.value = urlParams.get(param);
                    this.appendChild(input);
                }
            });
        });
        // --- Search Auto-Submit Logic ---
        const searchInput = document.getElementById('search-input');
        const searchForm = document.getElementById('search-form');

        if (searchInput) {
            let typingTimer;
            searchInput.addEventListener('input', () => {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(() => {
                    searchForm.submit();
                }, 500); // Cari otomatis setelah 0.5 detik berhenti mengetik
            });

            // Fokuskan kursor ke akhir teks setelah reload
            searchInput.focus();
            const val = searchInput.value;
            searchInput.value = '';
            searchInput.value = val;
        }

        // --- Order Type Toggle ---
        const orderType = document.getElementById('order_type');
        const tableWrapper = document.getElementById('table-wrapper');
        const orderContainer = document.getElementById('order-selection-container');

        function toggleTable() {
            if (orderType.value === 'takeaway') {
                // Sembunyikan meja
                tableWrapper.style.display = 'none';
                // Ubah grid menjadi 1 kolom agar select memanjang full
                orderContainer.classList.remove('grid-cols-2');
                orderContainer.classList.add('grid-cols-1');
            } else {
                // Tampilkan meja
                tableWrapper.style.display = 'block';
                // Kembalikan ke 2 kolom
                orderContainer.classList.remove('grid-cols-1');
                orderContainer.classList.add('grid-cols-2');
            }
        }


        orderType.addEventListener('change', toggleTable);
        // Jalankan saat halaman pertama kali dimuat
        toggleTable();

        // --- Global State ---
        let cart = []
        let currentItem = null
        let currentSelected = {}

        // --- Modal Logic ---
        function openModal(card) {
            currentItem = {
                id: card.dataset.id,
                name: card.dataset.name,
                price: parseInt(card.dataset.price),
                image: card.dataset.image,
                options: JSON.parse(card.dataset.options)
            }
            currentSelected = {}

            document.getElementById('modal-img').src = currentItem.image
            document.getElementById('modal-name').innerText = currentItem.name
            document.getElementById('modal-base-price').innerText = 'Harga: Rp ' + currentItem.price.toLocaleString('id')
            document.getElementById('modal-note').value = ''

            let html = ''
            currentItem.options.forEach(opt => {
                html += `<div>
                    <p class="text-xs font-bold text-gray-400 uppercase mb-2">${opt.name}</p>
                    <div class="flex flex-wrap gap-2">`
                opt.items.forEach(i => {
                    html += `<button type="button" 
                        class="opt-btn border border-gray-200 px-3 py-1.5 text-xs rounded-lg font-medium transition-all"
                        onclick="selectOpt(${opt.id}, ${i.id}, ${i.price}, '${i.name}', this)">
                        ${i.name} (+${i.price.toLocaleString('id')})
                    </button>`
                })
                html += `</div></div>`
            })
            document.getElementById('modal-options').innerHTML = html
            updateModalTotal()
            document.getElementById('modal-overlay').classList.add('show')
        }

        function closeModalOutside() {
            document.getElementById('modal-overlay').classList.remove('show')
        }

        function selectOpt(optId, itemId, price, name, btn) {
            btn.parentElement.querySelectorAll('.opt-btn').forEach(b => b.classList.remove('active', 'bg-violet-50', 'border-violet-500', 'text-violet-700'))
            currentSelected[optId] = {
                id: itemId,
                price,
                name
            }
            btn.classList.add('active', 'bg-violet-50', 'border-violet-500', 'text-violet-700')
            updateModalTotal()
        }

        function updateModalTotal() {
            let extra = Object.values(currentSelected).reduce((s, o) => s + o.price, 0)
            document.getElementById('modal-total').innerText = 'Rp ' + (currentItem.price + extra).toLocaleString('id')
        }

        function confirmAdd() {
            cart.push({
                ...currentItem,
                selectedOptions: {
                    ...currentSelected
                },
                notes: document.getElementById('modal-note').value.trim()
            })
            closeModalOutside()
            renderCart()
        }

        // --- Cart Logic ---
        function renderCart() {
            const list = document.getElementById('cart-list')
            const hiddenInputs = document.getElementById('hidden-inputs')
            let total = 0
            let html = ''
            hiddenInputs.innerHTML = ''

            if (cart.length === 0) {
                list.innerHTML = '<div class="text-center py-10"><p class="text-xs text-gray-400">Keranjang masih kosong</p></div>'
                document.getElementById('total-display').innerText = 'Rp 0'
                document.getElementById('cart-badge').innerText = '0'
                return
            }

            cart.forEach((item, i) => {
                let extra = Object.values(item.selectedOptions).reduce((s, o) => s + o.price, 0)
                let sub = item.price + extra
                total += sub

                html += `<div class="bg-gray-50 border border-gray-100 p-3 rounded-xl shadow-sm relative">
                    <div class="flex justify-between items-start">
                        <span class="text-sm font-bold text-gray-800">${item.name}</span>
                        <span class="text-sm font-bold text-violet-600 uppercase">Rp ${sub.toLocaleString('id')}</span>
                    </div>`

                const opts = Object.values(item.selectedOptions).map(o => o.name).join(', ')
                if (opts) html += `<p class="text-[11px] text-gray-500 mt-1">Opsi: ${opts}</p>`
                if (item.notes) html += `<p class="text-[11px] text-blue-500 italic mt-1 font-medium">Note: ${item.notes}</p>`

                html += `<button type="button" onclick="removeItem(${i})" class="text-[10px] font-bold text-red-400 hover:text-red-600 uppercase mt-2 inline-block">Hapus Item</button>
                </div>`

                const addInput = (name, val) => {
                    let inp = document.createElement('input');
                    inp.type = 'hidden';
                    inp.name = name;
                    inp.value = val;
                    hiddenInputs.appendChild(inp)
                }
                addInput(`items[${i}][menu_id]`, item.id)
                addInput(`items[${i}][notes]`, item.notes || '')
                Object.entries(item.selectedOptions).forEach(([optId, opt]) => {
                    addInput(`items[${i}][options][${optId}]`, opt.id)
                })
            })

            list.innerHTML = html
            document.getElementById('total-display').innerText = 'Rp ' + total.toLocaleString('id')
            document.getElementById('cart-badge').innerText = cart.length
        }

        function removeItem(i) {
            cart.splice(i, 1);
            renderCart()
        }

        document.getElementById('pos-form').addEventListener('submit', function(e) {

            const orderType = document.getElementById('order_type').value
            const tableSelect = document.querySelector('select[name="table_id"]')

            if (cart.length === 0) {
                e.preventDefault()
                alert('Pilih menu dulu!')
                return
            }

            if (orderType === 'dine_in' && !tableSelect.value) {
                e.preventDefault()
                alert('Pilih nomor meja!')
                return
            }

            const btn = document.getElementById('submit-btn')
            btn.disabled = true
            btn.innerText = 'MEMPROSES...'
        })


        function showPaymentMethods() {
            document.getElementById('payment-step-1').classList.add('hidden')
            document.getElementById('payment-step-2').classList.remove('hidden')
        }

        function openPaymentModal() {

            const orderType = document.getElementById('order_type').value
            const tableSelect = document.querySelector('select[name="table_id"]')

            if (cart.length === 0) {
                alert('Pilih menu dulu!')
                return
            }

            if (orderType === 'dine_in' && !tableSelect.value) {
                alert('Pilih nomor meja!')
                return
            }

            // reset step
            document.getElementById('payment-step-1').classList.remove('hidden')
            document.getElementById('payment-step-2').classList.add('hidden')

            document.getElementById('payment-modal').classList.add('show')
        }
        // ================= PAYMENT MODAL =================

        function openPaymentModal() {

            const orderType = document.getElementById('order_type').value
            const tableSelect = document.querySelector('select[name="table_id"]')

            if (cart.length === 0) {
                alert('Pilih menu dulu!')
                return
            }

            if (orderType === 'dine_in' && !tableSelect.value) {
                alert('Pilih nomor meja!')
                return
            }

            document.getElementById('payment-modal').classList.add('show')
        }

        function closePaymentModal() {
            document.getElementById('payment-modal').classList.remove('show')

            document.getElementById('payment-step-1').classList.remove('hidden')
            document.getElementById('payment-step-2').classList.add('hidden')
        }

        // ================= SUCCESS =================
        function showSuccess(orderId) {
            document.getElementById('success-modal').classList.add('show')

            let interval = setInterval(() => {

                fetch('/api/check-payment/' + orderId)
                    .then(res => res.json())
                    .then(res => {

                        if (res.status === 'paid') {
                            clearInterval(interval)
                            localStorage.removeItem('pending_payment')
                            window.location.href = '/cashier/receipt/' + res.order_id
                        }

                        if (res.status === 'expired' || res.status === 'failed') {
                            clearInterval(interval)
                            localStorage.removeItem('pending_payment')
                            alert("Pembayaran gagal / expired")
                        }

                    })

            }, 2000)
        }

        // ================= WAITING =================
        function showWaiting(orderId) {

            alert('Menunggu pembayaran...')

            let interval = setInterval(() => {

                fetch('/api/check-payment/' + orderId)
                    .then(res => res.json())
                    .then(res => {

                        if (res.status === 'paid') {
                            clearInterval(interval)
                            localStorage.removeItem('pending_payment')
                            window.location.href = '/cashier/receipt/' + res.order_id
                        }

                        if (res.status === 'expired' || res.status === 'failed') {
                            clearInterval(interval)
                            localStorage.removeItem('pending_payment')
                            alert("Pembayaran gagal / expired")
                        }

                    })

            }, 2000)
        }

        // ================= SUBMIT PAYMENT =================
        function submitPayment(method) {

            document.getElementById('payment_method').value = method

            const form = document.getElementById('pos-form')
            const formData = new FormData(form)

            fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(res => {

                    // CASH
                    if (res.type === 'cash') {
                        window.location.href = '/cashier/receipt/' + res.order_id
                        return
                    }

                    const snapToken = res.snap_token
                    const orderId = res.order_id

                    const total = document.getElementById('total-display').innerText;
                    const customer = document.querySelector('input[name="customer_name"]').value;

                    localStorage.setItem('pending_payment', JSON.stringify({
                        order_id: orderId,
                        snap_token: snapToken,
                        total: total,
                        customer: customer,
                        expired_at: Date.now() + (10 * 60 * 1000)
                    }));

                    snap.pay(snapToken, {

                        onSuccess: function(result) {
                            showSuccess(orderId)
                            
                        },

                        onPending: function(result) {
                            showWaiting(orderId)
                        },

                        onError: function(result) {
                            alert("Pembayaran gagal")
                        },

                        // 🔥 FIX UTAMA DI SINI
                        onClose: function() {
                            alert("Pembayaran ditutup, bisa dilanjutkan sebelum waktu habis");
                        }
                    });

                })
                .catch(error => {
                    console.error('submitPayment error:', error)
                    alert('Terjadi error saat memproses pembayaran.')
                })
        }

        // ================= RESUME PAYMENT =================
        function resumePayment() {

            const data = JSON.parse(localStorage.getItem('pending_payment'));

            if (!data) {
                alert('Tidak ada pembayaran pending');
                return;
            }

            // cek expired
            if (Date.now() > data.expired_at) {
                localStorage.removeItem('pending_payment');
                alert('Waktu pembayaran sudah habis');
                return;
            }

            snap.pay(data.snap_token, {

                onSuccess: function(result) {
                    showSuccess(data.order_id)
                },

                onPending: function(result) {
                    showWaiting(data.order_id)
                },

                onError: function(result) {
                    alert("Pembayaran gagal");
                },

                onClose: function() {
                    alert("Masih bisa dilanjutkan");
                }
            });
        }

        // ================= CHECK AUTO =================
        function checkPendingPayment() {

            const data = JSON.parse(localStorage.getItem('pending_payment'));

            if (!data) return;

            fetch('/api/check-payment/' + data.order_id)
                .then(res => res.json())
                .then(res => {

                    if (res.status === 'expired' || res.status === 'failed') {
                        localStorage.removeItem('pending_payment');
                        return;
                    }

                    if (Date.now() > data.expired_at) {
                        localStorage.removeItem('pending_payment');
                    }

                });
        }

        window.onload = function() {
            checkPendingPayment();
            showPendingUI();
        }

        function showPendingUI() {

            const data = JSON.parse(localStorage.getItem('pending_payment'));
            if (!data) return;

            const box = document.getElementById('pending-payment-box');
            const info = document.getElementById('pending-info');

            box.classList.remove('hidden');

            info.innerText = `Order #${data.order_id} • ${data.customer} • ${data.total}`;
        }
    </script>
</x-layouts.cashier>