<x-layouts.customer title="Proses Pembayaran">
    <div class="p-10 text-center">
        <div class="animate-spin inline-block w-8 h-8 border-[3px] border-current border-t-transparent text-[#D4E971] rounded-full mb-4"></div>
        <p class="font-black uppercase text-xs tracking-widest text-slate-400">Menyiapkan Pembayaran...</p>
    </div>

    {{-- MIDTRANS SNAP JS --}}
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.clientKey') }}"></script>
    
    <script type="text/javascript">
        window.onload = function() {
            // Langsung panggil popup Midtrans
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
                    alert('Anda menutup popup sebelum menyelesaikan pembayaran.');
                }
            });
        };
    </script>
</x-layouts.customer>