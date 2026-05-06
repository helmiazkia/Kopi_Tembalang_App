<x-layouts.customer :title="'Memproses Pembayaran...'">
    <div class="flex flex-col min-h-screen bg-white items-center justify-center p-10">
        
        <!-- ANIMATED LOADER -->
        <div class="relative w-24 h-24 mb-8">
            <!-- Lingkaran Luar Berputar -->
            <div class="absolute inset-0 border-4 border-slate-100 rounded-full"></div>
            <div class="absolute inset-0 border-4 border-[#D4E971] rounded-full border-t-transparent animate-spin"></div>
            
            <!-- Ikon Tengah -->
            <div class="absolute inset-0 flex items-center justify-center text-2xl animate-pulse">
                💳
            </div>
        </div>

        <div class="text-center space-y-3">
            <h2 class="text-xl font-black uppercase italic tracking-tighter text-slate-900">
                Menghubungkan<br><span class="text-[#D4E971]">Ke Pembayaran</span>
            </h2>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] max-w-[200px] mx-auto leading-relaxed">
                Mohon jangan tutup atau refresh halaman ini.
            </p>
        </div>

        <!-- TOMBOL DARURAT (Muncul jika popup terblokir) -->
        <div id="retry-container" class="hidden mt-10">
            <button onclick="triggerSnap()" class="px-6 py-3 bg-slate-900 text-[#D4E971] rounded-full font-black uppercase text-[10px] tracking-widest shadow-xl">
                Buka Jendela Bayar
            </button>
        </div>
    </div>

    {{-- MIDTRANS SNAP JS --}}
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.clientKey') }}"></script>
    
    @push('scripts')
    <script type="text/javascript">
        function triggerSnap() {
            window.snap.pay('{{ $order->payment->snap_token }}', {
                onSuccess: function(result) {
                    window.location.href = "{{ route('customer.payment.success', $order->id) }}";
                },
                onPending: function(result) {
                    window.location.href = "{{ route('customer.payment.success', $order->id) }}";
                },
                onError: function(result) {
                    alert("Pembayaran Gagal!");
                    window.location.href = "{{ route('customer.checkout.index', ['table' => $order->table_id]) }}";
                },
                onClose: function() {
                    // Jika user menutup popup, tampilkan tombol retry agar tidak stuck di loading
                    document.getElementById('retry-container').classList.remove('hidden');
                }
            });
        }

        window.onload = function() {
            // Delay sedikit agar transisi halaman halus baru muncul popup
            setTimeout(triggerSnap, 1000);
        };
    </script>
    @endpush
</x-layouts.customer>