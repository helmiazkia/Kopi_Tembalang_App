<x-layouts.customer :title="'Bayar Kasir - #' . $order->id">
    <div class="flex flex-col min-h-screen bg-white">
        
        <main class="p-8 flex-1 flex flex-col items-center justify-center space-y-8">
            
            <!-- HEADER SECTION -->
            <div class="text-center space-y-2">
                <div class="inline-block px-4 py-1.5 rounded-full bg-slate-900 text-[#D4E971] text-[10px] font-black uppercase tracking-[0.2em] shadow-lg">
                    Payment Mode: Cash
                </div>
                <h2 class="text-2xl font-black text-slate-900 italic leading-tight uppercase tracking-tighter">
                    Silakan Bayar<br><span class="text-[#D4E971]">Ke Meja Kasir.</span>
                </h2>
            </div>

            <!-- QR CODE CONTAINER -->
            <div class="relative group">
                <!-- Dekorasi kilau di belakang QR -->
                <div class="absolute -inset-4 bg-[#D4E971] blur-3xl opacity-20 rounded-full"></div>
                
                <div class="relative bg-white p-6 rounded-[3rem] border-2 border-slate-900 shadow-[0_20px_50px_rgba(0,0,0,0.1)]">
                    @if($order->payment)
                        <div class="bg-white p-2 rounded-2xl">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data={{ $order->payment->transaction_id }}&color=1a1a1a" 
                                 alt="QR Code" class="w-56 h-56 mx-auto rounded-lg">
                        </div>
                    @endif
                    
                    <div class="mt-4 text-center">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Order ID</p>
                        <p class="text-sm font-black text-slate-900 italic">#{{ $order->id }}</p>
                    </div>
                </div>
            </div>

            <!-- INSTRUCTION -->
            <div class="text-center space-y-4 max-w-[250px]">
                <p class="text-[11px] font-bold text-slate-400 leading-relaxed uppercase tracking-wider">
                    Tunjukkan Kode QR di atas kepada kasir untuk memvalidasi pesananmu.
                </p>
                
                <!-- TOTAL TAGIHAN CARD -->
                <div class="bg-slate-900 rounded-[2rem] p-6 shadow-xl border-b-4 border-[#D4E971]">
                    <p class="text-[#D4E971]/60 text-[9px] font-black uppercase tracking-[0.2em] mb-1">Total Tagihan</p>
                    <p class="text-2xl font-black text-white italic tracking-tighter">
                        Rp {{ number_format($order->total_price, 0, ',', '.') }}
                    </p>
                </div>
            </div>

            <!-- LOADING INDICATOR (Polling Status) -->
            <div class="flex items-center gap-3 pt-4">
                <div class="flex gap-1">
                    <span class="w-1.5 h-1.5 bg-[#D4E971] rounded-full animate-bounce"></span>
                    <span class="w-1.5 h-1.5 bg-[#D4E971] rounded-full animate-bounce [animation-delay:-0.15s]"></span>
                    <span class="w-1.5 h-1.5 bg-[#D4E971] rounded-full animate-bounce [animation-delay:-0.3s]"></span>
                </div>
                <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest">Menunggu Konfirmasi Kasir</p>
            </div>

        </main>

        <!-- FOOTER INFO -->
        <footer class="p-8 text-center">
            <p class="text-[9px] font-bold text-slate-300 uppercase tracking-widest">© Kopi Tembalang Digital Order</p>
        </footer>
    </div>

    @push('scripts')
    <script>
        // Fungsi untuk cek status pembayaran secara berkala
        setInterval(function() {
            fetch("{{ route('customer.payment.check', $order->id) }}")
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'paid') {
                        // Jika sudah dibayar (dikonfirmasi kasir), langsung redirect ke halaman sukses
                        window.location.href = "{{ route('customer.payment.success', $order->id) }}";
                    }
                })
                .catch(error => console.error('Error:', error));
        }, 3000); // Cek setiap 3 detik
    </script>
    @endpush
</x-layouts.customer>