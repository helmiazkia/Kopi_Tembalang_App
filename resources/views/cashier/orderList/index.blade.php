<x-layouts.cashier title="Antrean Tagihan">
    <!-- Midtrans Snap.js -->
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.clientKey') }}"></script>

    <style>
        .order-card {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            cursor: pointer;
            border: 1px solid #f1f5f9;
        }

        .order-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px -10px rgba(212, 233, 113, 0.3);
            border-color: #D4E971;
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

        .status-unpaid {
            background-color: #fff7ed;
            color: #9a3412;
            border: 1px solid #ffedd5;
        }
    </style>

    <div class="p-6 md:p-10 max-w-[1600px] mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">

            {{-- SEKSI KIRI: SCANNER & SISTEM --}}
            <div class="lg:col-span-4 xl:col-span-3">
                <div class="bg-[#1a1a1a] p-8 rounded-[2.5rem] border-4 border-black shadow-2xl relative overflow-hidden mb-8 group">
                    <div class="absolute top-0 left-0 w-full h-1 bg-[#D4E971] animate-pulse shadow-[0_0_15px_#D4E971]"></div>
                    <div class="relative flex flex-col items-center text-center">
                        <div class="w-16 h-16 bg-[#D4E971] rounded-2xl shadow-[0_0_20px_rgba(212,233,113,0.4)] mb-6 flex items-center justify-center text-black">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path d="M3 7V5a2 2 0 012-2h2m10 0h2a2 2 0 012 2v2m0 10v2a2 2 0 01-2 2h-2M7 21H5a2 2 0 01-2-2v-2M12 7v10m0-10l-3 3m3-3l3 3"></path>
                            </svg>
                        </div>
                        <h2 class="text-[#D4E971] font-black uppercase tracking-[0.2em] text-xs mb-2">System Ready</h2>
                        <h1 class="text-white text-2xl font-black uppercase italic mb-8 tracking-tighter">Scan <span class="text-[#D4E971] not-italic">Order.</span></h1>

                        <form method="POST" action="{{ route('cashier.orderList.scan') }}" id="scan-form" class="w-full">
                            @csrf
                            <input type="text" name="code" id="scanner-input" placeholder="WAITING..." class="w-full px-6 py-6 bg-white/5 border-2 border-white/10 rounded-2xl text-center text-2xl font-black text-[#D4E971] outline-none" autofocus autocomplete="off">
                        </form>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm sticky top-10">
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4 text-center">Kamera Cadangan</h3>
                    <div id="reader" class="w-full rounded-2xl overflow-hidden border-2 border-dashed border-slate-100"></div>
                </div>
            </div>

            {{-- SEKSI KANAN: LIST ANTREAN --}}
            <div class="lg:col-span-8 xl:col-span-9">
                <div class="mb-14">
                    <div class="flex items-end justify-between mb-8 pb-4 border-b border-slate-100">
                        <h1 class="text-3xl font-black text-slate-800 uppercase italic tracking-tighter">Antrean <span class="text-[#D4E971] not-italic">Tagihan.</span></h1>
                        <div class="bg-black text-[#D4E971] px-4 py-2 rounded-xl font-black text-xs uppercase">{{ $pendingOrders->count() }} Pending</div>
                    </div>

                    <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-6">
                        @forelse($pendingOrders as $order)
                        <div class="order-card bg-white rounded-[2rem] p-7 shadow-sm relative flex flex-col"
                            onclick="instantPay('{{ $order->id }}')">

                            <div class="flex justify-between items-start mb-6">
                                <span class="px-3 py-1 bg-slate-100 rounded-lg text-[10px] font-black text-slate-400 tracking-widest">#{{ $order->id }}</span>
                                <div class="flex items-center gap-2 px-3 py-1 rounded-lg status-unpaid">
                                    <span class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse"></span>
                                    <span class="text-[9px] font-black uppercase">UNPAID</span>
                                </div>
                            </div>

                            <div class="mb-8">
                                <h2 class="text-xl font-black text-slate-800 uppercase truncate">{{ $order->customer_name ?? 'Guest' }}</h2>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ str_replace('_', ' ', $order->order_type) }}</span>
                                    @if($order->table)
                                    <span class="w-1 h-1 bg-slate-200 rounded-full"></span>
                                    <span class="text-[10px] font-black text-[#D4E971] uppercase tracking-widest">Meja {{ $order->table->table_number }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-auto pt-6 border-t border-slate-50 flex items-center justify-between">
                                <h3 class="text-2xl font-black text-slate-900 italic">
                                    <span class="text-xs font-normal opacity-40 not-italic">Rp</span>{{ number_format($order->total_price) }}
                                </h3>
                                <div class="w-10 h-10 bg-slate-900 text-[#D4E971] rounded-xl flex items-center justify-center group-hover:bg-[#D4E971] group-hover:text-black transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                        <path d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-span-full py-20 text-center bg-slate-50 rounded-[3rem] border-4 border-dashed border-slate-100">
                            <p class="text-slate-400 text-[10px] font-black uppercase tracking-[0.3em] italic">Tidak ada tagihan tertunda</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                {{-- RIWAYAT TRANSAKSI --}}
                <div class="bg-slate-900 rounded-[3rem] p-10 relative overflow-hidden">
                    <h1 class="text-white font-black uppercase tracking-[0.2em] mb-8 flex items-center gap-4">
                        <span class="w-8 h-[2px] bg-[#D4E971]"></span> Riwayat Transaksi Hari Ini
                    </h1>
                    <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-4">
                        @foreach($paidOrders as $order)
                        <div class="bg-white/5 border border-white/10 p-5 rounded-2xl flex justify-between items-center group hover:bg-white/10 transition-all">
                            <div>
                                <h2 class="font-black text-white text-xs uppercase">{{ $order->customer_name }}</h2>
                                <p class="text-[9px] text-white/30 uppercase mt-1 tracking-widest">{{ $order->updated_at->format('H:i') }} • SUCCESS</p>
                            </div>
                            <h3 class="text-sm font-black text-[#D4E971] italic">Rp{{ number_format($order->total_price) }}</h3>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL PROCESSING (LOADING) --}}
    <div id="processing-modal" class="modal-overlay">
        <div class="bg-white rounded-[2.5rem] p-8 text-center w-80 shadow-2xl border border-slate-100">
            <div class="animate-spin w-12 h-12 border-4 border-[#D4E971] border-t-slate-900 rounded-full mx-auto mb-6"></div>
            <h2 class="text-xl font-black text-slate-800 uppercase italic">Processing...</h2>
            <p class="text-xs font-bold text-slate-400 mt-2 uppercase">Menghubungkan Pembayaran</p>
        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        // Flag untuk mengontrol polling agar tidak mengganggu saat modal/snap terbuka
        let isModalOpen = false;

        /**
         * 🔥 FUNGSI BAYAR INSTAN (QRIS)
         */
        function instantPay(orderId) {
            isModalOpen = true; // Kunci polling
            document.getElementById('processing-modal').classList.add('show');

            fetch(`/cashier/orderList/snap/${orderId}`)
                .then(res => res.json())
                .then(res => {
                    document.getElementById('processing-modal').classList.remove('show');

                    if (res.error) {
                        alert(res.error);
                        isModalOpen = false;
                        return;
                    }

                    snap.pay(res.snap_token, {
                        onSuccess: function(result) {
                            window.location.href = '/cashier/receipt/' + orderId;
                        },
                        onPending: function(result) {
                            alert('Pembayaran masih pending.');
                            isModalOpen = false;
                            location.reload();
                        },
                        onError: function(result) {
                            alert('Terjadi kesalahan pembayaran.');
                            isModalOpen = false;
                        },
                        onClose: function() {
                            console.log('Kasir menutup jendela pembayaran');
                            isModalOpen = false; // Buka kunci polling
                        }
                    });
                })
                .catch(err => {
                    document.getElementById('processing-modal').classList.remove('show');
                    isModalOpen = false;
                    alert('Gagal mengambil data transaksi.');
                });
        }

        // Scanner Auto-Submit (Bayar Cash via Scanner Fisik)
        const scannerInput = document.getElementById('scanner-input');
        if (scannerInput) {
            scannerInput.addEventListener('input', function() {
                if (this.value.length > 0) {
                    this.classList.add('opacity-50');
                    setTimeout(() => {
                        document.getElementById('scan-form').submit();
                    }, 150);
                }
            });
            document.addEventListener('click', () => {
                if (!isModalOpen) scannerInput.focus();
            });
        }

        /**
         * 🔥 AUTO-PRINT POLLING SYSTEM
         * Mengecek database setiap 5 detik untuk order lunas yang belum diprint
         */
        setInterval(function() {
            if (isModalOpen) return;

            fetch("{{ route('cashier.api.check.unprinted') }}")
                .then(response => response.json())
                .then(data => {
                    if (data.has_new) {
                        isModalOpen = true;

                        // 1. URL Struk Customer & Struk Dapur
                        let customerReceipt = "{{ url('cashier/receipt') }}/" + data.order_id;
                        let kitchenReceipt = "{{ url('cashier/receipt-kitchen') }}/" + data.order_id;

                        // 2. Buka dua tab sekaligus
                        let win1 = window.open(customerReceipt, '_blank');
                        let win2 = window.open(kitchenReceipt, '_blank');

                        // 3. Tandai sudah diprint di database
                        setTimeout(() => {
                            fetch("{{ url('cashier/api/mark-as-printed') }}/" + data.order_id, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json'
                                }
                            }).then(() => {
                                if (win1) win1.close();
                                if (win2) win2.close();
                                isModalOpen = false;
                                window.location.reload();
                            });
                        }, 4000); // Beri waktu lebih lama (4 detik) agar kedua dialog print muncul
                    }
                });
        }, 5000);

        /**
         * CAMERA LOGIC (QR Scanner Kamera Browser)
         */
        function onScanSuccess(decodedText) {
            html5QrcodeScanner.clear();
            window.location.href = '/cashier/receipt/' + decodedText;
        }

        let html5QrcodeScanner = new Html5QrcodeScanner("reader", {
            fps: 15,
            qrbox: {
                width: 250,
                height: 250
            },
            aspectRatio: 1.0
        });
        html5QrcodeScanner.render(onScanSuccess);
    </script>
</x-layouts.cashier>