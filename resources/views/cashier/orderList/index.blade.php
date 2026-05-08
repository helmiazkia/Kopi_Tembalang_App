<x-layouts.cashier title="Antrean Tagihan">

{{-- Midtrans Snap --}}
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.clientKey') }}"></script>

<style>
    /* ── Order Card ── */
    .order-card {
        border: 1.5px solid #f1f5f9;
        transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
        cursor: pointer;
    }
    .order-card:hover {
        transform: translateY(-5px);
        border-color: #D4E971;
        box-shadow: 0 20px 40px -12px rgba(212,233,113,0.2);
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
</style>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

    {{-- ════════════════════════════════════════
         KIRI: SCANNER
    ════════════════════════════════════════ --}}
    <div class="lg:col-span-4 xl:col-span-3">
        <div class="sticky top-6 bg-slate-900 p-8 rounded-3xl border border-slate-800 shadow-xl relative overflow-hidden">
            {{-- Accent line --}}
            <div class="absolute top-0 left-0 right-0 h-0.5 bg-[#D4E971] shadow-[0_0_12px_#D4E971]"></div>

            <div class="flex flex-col items-center text-center">
                {{-- Icon --}}
                <div class="w-14 h-14 bg-[#D4E971] rounded-2xl mb-6 flex items-center justify-center text-black shadow-lg shadow-[#D4E971]/30">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M3 7V5a2 2 0 012-2h2m10 0h2a2 2 0 012 2v2m0 10v2a2 2 0 01-2 2h-2M7 21H5a2 2 0 01-2-2v-2"/>
                        <path d="M12 7v10m0-10l-3 3m3-3l3 3"/>
                    </svg>
                </div>

                <span class="text-[9px] font-black tracking-[0.3em] text-slate-500 uppercase mb-1">System Ready</span>
                <h1 class="text-white text-2xl font-black uppercase tracking-tight mb-8">
                    Scan <span class="text-[#D4E971]">Order.</span>
                </h1>

                <form method="POST" action="{{ route('cashier.orderList.scan') }}" id="scan-form" class="w-full">
                    @csrf
                    <input
                        type="text" name="code" id="scanner-input"
                        placeholder="WAITING..."
                        autocomplete="off" autofocus
                        class="w-full px-6 py-5 bg-white/5 border-2 border-white/10 rounded-2xl text-center
                               text-2xl font-black text-[#D4E971] outline-none
                               focus:border-[#D4E971]/50 transition-colors tracking-widest">
                </form>

                <p class="mt-5 text-[9px] text-slate-600 font-bold uppercase tracking-widest leading-relaxed">
                    Klik di mana saja jika input tidak fokus
                </p>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════
         KANAN: ANTREAN & RIWAYAT
    ════════════════════════════════════════ --}}
    <div class="lg:col-span-8 xl:col-span-9 space-y-10">

        {{-- Header Antrean --}}
        <div class="flex items-end justify-between pb-4 border-b border-slate-200">
            <div>
                <p class="text-[9px] font-black uppercase tracking-[0.25em] text-slate-400 mb-1">Pembayaran Tertunda</p>
                <h1 class="text-3xl font-black text-slate-800 tracking-tight">
                    Antrean <span class="text-[#D4E971]">Tagihan.</span>
                </h1>
            </div>
            <div class="bg-slate-900 text-[#D4E971] px-4 py-2 rounded-xl font-black text-[9px] uppercase tracking-widest">
                {{ $pendingOrders->count() }} Pending
            </div>
        </div>

        {{-- Grid Antrean --}}
        <div id="js-order-grid" class="grid md:grid-cols-2 xl:grid-cols-3 gap-4">
            @forelse($pendingOrders as $order)
                <div class="order-card bg-white rounded-3xl p-6 shadow-sm relative flex flex-col"
                    onclick="handleOrderClick(
                        '{{ $order->id }}',
                        '{{ $order->payment->method ?? 'cash' }}',
                        '{{ $order->customer_name }}',
                        '{{ number_format($order->total_price) }}'
                    )">

                    {{-- Badges --}}
                    <div class="flex justify-between items-start mb-5">
                        <span class="px-3 py-1 bg-slate-100 rounded-lg text-[9px] font-black text-slate-400 tracking-widest">
                            #{{ $order->id }}
                        </span>
                        <div class="flex items-center gap-2">
                            @if($order->status === 'done')
                                <span class="px-2 py-1 bg-green-500 text-white text-[8px] font-black rounded-lg animate-bounce shadow-sm shadow-green-200">
                                    SIAP DIANTAR
                                </span>
                            @endif
                            <div class="flex items-center gap-1.5 px-3 py-1 rounded-lg bg-orange-50 border border-orange-100">
                                <span class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse"></span>
                                <span class="text-[8px] font-black uppercase text-orange-800">
                                    UNPAID · {{ strtoupper($order->payment->method ?? 'CASH') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Info --}}
                    <div class="mb-6">
                        <h2 class="text-base font-black text-slate-800 uppercase truncate">
                            {{ $order->customer_name ?? 'Guest' }}
                        </h2>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">
                                {{ str_replace('_', ' ', $order->order_type) }}
                            </span>
                            @if($order->table)
                                <span class="w-1 h-1 bg-slate-200 rounded-full"></span>
                                <span class="text-[9px] font-black text-[#D4E971] uppercase tracking-widest">
                                    Meja {{ $order->table->table_number }}
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="mt-auto pt-4 border-t border-slate-50 flex items-center justify-between">
                        <h3 class="text-xl font-black text-slate-900">
                            <span class="text-[9px] font-normal opacity-40">Rp</span>{{ number_format($order->total_price) }}
                        </h3>
                        <div class="w-9 h-9 bg-slate-900 text-[#D4E971] rounded-xl flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-20 text-center bg-slate-50 rounded-3xl border-2 border-dashed border-slate-200">
                    <p class="text-[9px] font-black uppercase tracking-[0.3em] text-slate-400">
                        Tidak ada tagihan tertunda
                    </p>
                </div>
            @endforelse
        </div>

        {{-- Riwayat Transaksi --}}
        <div id="js-history-grid" class="bg-slate-900 rounded-3xl p-8 relative overflow-hidden">
            <div class="flex items-center gap-3 mb-6">
                <span class="w-8 h-0.5 bg-[#D4E971] rounded-full block"></span>
                <h2 class="text-white font-black uppercase tracking-[0.2em] text-xs">Riwayat Transaksi Hari Ini</h2>
            </div>

            <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-3">
                @foreach($paidOrders as $order)
                    <div class="flex justify-between items-center p-4 rounded-2xl
                                bg-white/5 border border-white/10
                                hover:bg-[#D4E971]/10 hover:border-[#D4E971]/30 transition-all cursor-pointer"
                        onclick="openHistoryDetail(
                            '{{ $order->id }}',
                            '{{ $order->customer_name }}',
                            '{{ $order->status }}',
                            '{{ number_format($order->total_price) }}',
                            '{{ $order->table->table_number ?? 'TA' }}'
                        )">
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-black text-white text-[10px] uppercase">{{ $order->customer_name }}</h3>
                                <span class="w-1.5 h-1.5 rounded-full {{ $order->status === 'done' ? 'bg-green-500 animate-pulse' : 'bg-amber-500' }}"></span>
                            </div>
                            <p class="text-[8px] text-white/30 uppercase mt-1 tracking-widest">
                                {{ $order->updated_at->format('H:i') }} ·
                                <span class="{{ $order->status === 'done' ? 'text-green-400' : 'text-amber-400' }}">
                                    {{ $order->status === 'done' ? 'SELESAI' : 'DIMASAK' }}
                                </span>
                            </p>
                        </div>
                        <span class="text-xs font-black text-[#D4E971]">Rp{{ number_format($order->total_price) }}</span>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
</div>

{{-- ════════════════════════════════════════════════════════
     MODAL: BAYAR TUNAI
════════════════════════════════════════════════════════ --}}
<div id="cash-modal" class="modal-overlay">
    <div class="bg-white rounded-3xl p-8 w-full max-w-sm shadow-2xl border border-slate-100 mx-4 text-center">
        <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-5 text-3xl">💵</div>
        <h2 class="text-xl font-black text-slate-800 uppercase tracking-tight mb-1">Bayar Tunai?</h2>
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-7">
            <span id="modal-customer-name"></span> — Rp<span id="modal-total-price"></span>
        </p>
        <div class="space-y-3">
            <button onclick="processCash()"
                class="w-full bg-slate-900 text-[#D4E971] py-4 rounded-2xl font-black uppercase text-[10px]
                       tracking-widest active:scale-95 transition-transform shadow-lg">
                Selesaikan Pembayaran
            </button>
            <button onclick="closeCashModal()"
                class="w-full py-3 text-slate-400 font-black uppercase text-[9px] tracking-widest hover:text-red-400 transition-colors">
                Batal
            </button>
        </div>
    </div>
</div>

{{-- ════════════════════════════════════════════════════════
     MODAL: DETAIL RIWAYAT
════════════════════════════════════════════════════════ --}}
<div id="history-detail-modal" class="modal-overlay">
    <div class="bg-white rounded-3xl p-8 w-full max-w-sm shadow-2xl border border-slate-100 mx-4">
        <div class="text-center mb-6">
            <div id="history-status-icon"
                class="w-14 h-14 rounded-2xl mx-auto mb-4 flex items-center justify-center text-2xl"></div>
            <h2 id="history-customer-name" class="text-xl font-black text-slate-800 uppercase tracking-tight"></h2>
            <p id="history-status-text" class="text-[9px] font-black uppercase tracking-widest mt-1"></p>
        </div>

        <div class="bg-slate-50 rounded-2xl p-5 space-y-3 mb-6">
            <div class="flex justify-between items-center">
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">No. Meja</span>
                <span id="history-table" class="text-sm font-black text-slate-800"></span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Total Bayar</span>
                <span id="history-total" class="text-sm font-black text-slate-800"></span>
            </div>
        </div>

        <div class="space-y-3">
            <button onclick="reprintReceipt()"
                class="w-full flex items-center justify-center gap-3 bg-slate-900 text-[#D4E971] py-4 rounded-2xl
                       font-black uppercase text-[10px] tracking-widest active:scale-95 transition-transform shadow-lg">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Cetak Ulang Struk
            </button>
            <button onclick="closeHistoryModal()"
                class="w-full py-3 text-slate-400 font-black uppercase text-[9px] tracking-widest hover:text-red-400 transition-colors">
                Tutup
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
    </div>
</div>

@push('scripts')
<script>
    // ── State ─────────────────────────────────────────────────────────
    let isModalOpen   = false;
    let selectedOrder = null;
    let historyOrder  = null;

    // ── Helpers Modal ──────────────────────────────────────────────────
    const showModal = id => { document.getElementById(id).classList.add('show'); isModalOpen = true; };
    const hideModal = id => { document.getElementById(id).classList.remove('show'); isModalOpen = false; };

    // ── Handle Klik Kartu Order ────────────────────────────────────────
    function handleOrderClick(id, method, name, total) {
        selectedOrder = id;
        method === 'cash' ? openCashModal(name, total) : processQRIS(id);
    }

    // ── Cash Modal ─────────────────────────────────────────────────────
    function openCashModal(name, total) {
        document.getElementById('modal-customer-name').innerText = name;
        document.getElementById('modal-total-price').innerText   = total;
        showModal('cash-modal');
    }

    function closeCashModal() { hideModal('cash-modal'); }

    function processCash() {
        if (!confirm('Yakin pesanan ini sudah dibayar tunai?')) return;
        closeCashModal();
        showModal('processing-modal');
        window.location.href = `/cashier/orderList/pay/${selectedOrder}`;
    }

    // ── QRIS ───────────────────────────────────────────────────────────
    function processQRIS(id) {
        showModal('processing-modal');
        fetch(`/cashier/orderList/snap/${id}`)
            .then(res => res.json())
            .then(res => {
                hideModal('processing-modal');
                if (res.error) { alert(res.error); isModalOpen = false; return; }
                snap.pay(res.snap_token, {
                    onSuccess: () => window.location.href = '/cashier/receipt/' + id,
                    onPending: () => { isModalOpen = false; updateUI(); },
                    onClose:   () => { isModalOpen = false; },
                });
            })
            .catch(() => { hideModal('processing-modal'); isModalOpen = false; });
    }

    // ── History Modal ──────────────────────────────────────────────────
    function openHistoryDetail(id, name, status, total, table) {
        historyOrder = id;
        const isDone = status === 'done';

        document.getElementById('history-customer-name').innerText = name;
        document.getElementById('history-total').innerText         = 'Rp' + total;
        document.getElementById('history-table').innerText         = table === 'TA' ? 'Takeaway' : 'Meja ' + table;

        const icon = document.getElementById('history-status-icon');
        const txt  = document.getElementById('history-status-text');

        icon.className = `w-14 h-14 rounded-2xl mx-auto mb-4 flex items-center justify-center text-2xl ${isDone ? 'bg-green-100 animate-bounce' : 'bg-amber-100 animate-pulse'}`;
        icon.innerText = isDone ? '✅' : '🔥';
        txt.className  = `text-[9px] font-black uppercase tracking-widest mt-1 ${isDone ? 'text-green-500' : 'text-amber-500'}`;
        txt.innerText  = isDone ? 'Siap Diantar' : 'Sedang Dimasak';

        showModal('history-detail-modal');
    }

    function closeHistoryModal() { hideModal('history-detail-modal'); }

    function reprintReceipt() {
        if (!historyOrder) return;
        window.open("{{ url('cashier/receipt') }}/" + historyOrder, '_blank');
    }

    // ── Polling: Update UI ─────────────────────────────────────────────
    function updateUI() {
        if (isModalOpen) return;
        fetch(window.location.href)
            .then(res => res.text())
            .then(html => {
                const doc   = new DOMParser().parseFromString(html, 'text/html');
                const order = document.getElementById('js-order-grid');
                const hist  = document.getElementById('js-history-grid');
                if (order) order.innerHTML = doc.getElementById('js-order-grid').innerHTML;
                if (hist)  hist.innerHTML  = doc.getElementById('js-history-grid').innerHTML;
            });
    }

    // ── Polling: Auto Print ────────────────────────────────────────────
    function checkUnprinted() {
        if (isModalOpen) return;
        fetch("{{ route('cashier.api.check.unprinted') }}")
            .then(res => res.json())
            .then(data => {
                if (!data.has_new) return;
                isModalOpen = true;
                const win = window.open("{{ url('cashier/receipt') }}/" + data.order_id, '_blank');
                setTimeout(() => {
                    fetch("{{ url('cashier/api/mark.printed') }}/" + data.order_id, {
                        method:  'POST',
                        headers: {
                            'X-CSRF-TOKEN':  '{{ csrf_token() }}',
                            'Content-Type':  'application/json',
                        },
                    }).then(() => {
                        if (win) win.close();
                        isModalOpen = false;
                        updateUI();
                    });
                }, 3000);
            });
    }

    // ── Interval Polling ───────────────────────────────────────────────
    setInterval(updateUI,       5000);
    setInterval(checkUnprinted, 5000);

    // ── Auto Focus Scanner ─────────────────────────────────────────────
    const scannerInput = document.getElementById('scanner-input');
    if (scannerInput) {
        document.addEventListener('click', () => {
            if (!isModalOpen) scannerInput.focus();
        });
        scannerInput.addEventListener('input', function () {
            if (this.value.length > 0) {
                setTimeout(() => document.getElementById('scan-form').submit(), 150);
            }
        });
    }
</script>
@endpush

</x-layouts.cashier>