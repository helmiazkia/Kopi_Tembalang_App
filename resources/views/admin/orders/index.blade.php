<x-layouts.admin title="Monitoring Orders">
    <div class="p-6 md:p-10 max-w-[1600px] mx-auto">
        
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-4">
            <div>
                <h1 class="text-3xl font-black text-slate-800 uppercase italic tracking-tighter">
                    Monitoring <span class="text-[#D4E971] not-italic">Orders.</span>
                </h1>
                <p class="text-[10px] font-black uppercase text-slate-400 tracking-[0.2em] mt-1">Live Transaction Dashboard</p>
            </div>
            <div class="flex gap-2">
                <div class="bg-black text-[#D4E971] px-4 py-2 rounded-xl font-black text-xs uppercase shadow-lg">
                    Total: {{ $orders->count() }} Orders
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr class="text-slate-400 text-[10px] font-black uppercase tracking-widest">
                            <th class="py-5 pl-8">No</th>
                            <th>Order ID</th>
                            <th>Meja</th>
                            <th>Customer</th>
                            <th>Total Amount</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Admin/Kasir</th>
                            <th class="text-right pr-8">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-slate-600 font-medium">
                        @foreach($orders as $index => $order)
                        <tr class="hover:bg-slate-50/80 transition-colors border-b border-slate-50">
                            <td class="py-4 pl-8 text-[10px] font-bold">{{ $index+1 }}</td>
                            <td>
                                <span class="px-2 py-1 bg-slate-100 rounded text-[10px] font-black text-slate-500">#{{ $order->id }}</span>
                            </td>
                            {{-- PERBAIKAN: Gunakan Null Safe Operator agar tidak error property of null --}}
                            <td class="font-black text-slate-800 uppercase italic">
                                {{ $order->table?->table_number ?? 'T.A' }}
                            </td>
                            <td>
                                <div class="flex flex-col">
                                    <span class="font-black text-slate-800 text-sm uppercase">{{ $order->customer_name }}</span>
                                    <span class="text-[9px] uppercase tracking-tighter text-slate-400">{{ str_replace('_', ' ', $order->order_type) }}</span>
                                </div>
                            </td>
                            <td class="font-black text-slate-900">
                                Rp {{ number_format($order->total_price) }}
                            </td>
                            <td>
                                @if($order->payment)
                                    <div class="flex flex-col gap-1">
                                        <span class="badge badge-sm font-black text-[9px] uppercase 
                                            {{ $order->payment->status == 'paid' ? 'bg-green-100 text-green-700 border-green-200' : 'bg-amber-100 text-amber-700 border-amber-200' }}">
                                            {{ $order->payment->method }}
                                        </span>
                                        <span class="text-[8px] uppercase opacity-50">{{ $order->payment->channel ?? '-' }}</span>
                                    </div>
                                @else
                                    <span class="badge badge-error badge-sm font-black text-[9px] uppercase text-white">UNPAID</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusClasses = [
                                        'pending' => 'bg-amber-500 text-white',
                                        'paid' => 'bg-blue-500 text-white',
                                        'ready' => 'bg-[#D4E971] text-black',
                                        'done' => 'bg-green-600 text-white',
                                        'cancelled' => 'bg-red-500 text-white',
                                    ];
                                @endphp
                                <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest {{ $statusClasses[$order->status] ?? 'bg-slate-200' }}">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td class="text-xs uppercase font-bold">
                                {{ $order->cashier?->name ?? 'System' }}
                            </td>
                            <td class="text-right pr-8">
                                <button
                                    class="btn btn-sm bg-slate-900 text-white hover:bg-[#D4E971] hover:text-black border-none rounded-xl font-black text-[10px] uppercase tracking-widest px-4"
                                    onclick="openDetailModal(this)"
                                    data-id="{{ $order->id }}"
                                    data-table="{{ $order->table?->table_number ?? 'Take Away' }}"
                                    data-customer="{{ $order->customer_name }}"
                                    data-total="{{ number_format($order->total_price) }}"
                                    data-status="{{ strtoupper($order->status) }}"
                                    data-payment="{{ $order->payment->method ?? 'None' }}"
                                    data-items='@json($order->items->load("menu"))'>
                                    View Detail
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MODAL DETAIL --}}
    <dialog id="detail_modal" class="modal">
        <div class="modal-box max-w-lg bg-white rounded-[2.5rem] p-0 overflow-hidden shadow-2xl">
            <div class="bg-slate-900 p-8 text-white">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="font-black text-2xl uppercase italic tracking-tighter">Order <span class="text-[#D4E971]">Detail.</span></h3>
                        <p class="text-[10px] font-black text-white/40 uppercase tracking-[0.2em] mt-1" id="detail_id_display"></p>
                    </div>
                    <button class="btn btn-sm btn-circle bg-white/10 border-none text-white" onclick="detail_modal.close()">✕</button>
                </div>
            </div>
            
            <div class="p-8">
                <div class="grid grid-cols-2 gap-4 mb-8">
                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Customer</p>
                        <p class="text-sm font-black text-slate-800 uppercase" id="detail_customer"></p>
                    </div>
                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Table</p>
                        <p class="text-sm font-black text-slate-800 uppercase" id="detail_table"></p>
                    </div>
                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Status</p>
                        <p class="text-sm font-black text-[#D4E971] uppercase" id="detail_status"></p>
                    </div>
                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Payment</p>
                        <p class="text-sm font-black text-slate-800 uppercase" id="detail_payment"></p>
                    </div>
                </div>

                <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4 flex items-center gap-2">
                    <span class="w-4 h-[1px] bg-slate-200"></span> Menu Items
                </h4>

                <ul id="detail_items" class="space-y-3 mb-8"></ul>

                <div class="bg-[#D4E971] p-5 rounded-2xl flex justify-between items-center shadow-lg shadow-[#D4E971]/20">
                    <span class="text-[10px] font-black text-black uppercase tracking-widest">Total Transaction</span>
                    <span class="text-xl font-black text-black italic">Rp <span id="detail_total"></span></span>
                </div>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop bg-slate-900/40 backdrop-blur-sm">
            <button>close</button>
        </form>
    </dialog>

    <script>
        function openDetailModal(btn) {
            const data = btn.dataset;
            const items = JSON.parse(data.items);

            document.getElementById('detail_id_display').innerText = 'Order ID #' + data.id;
            document.getElementById('detail_table').innerText = data.table;
            document.getElementById('detail_customer').innerText = data.customer;
            document.getElementById('detail_total').innerText = data.total;
            document.getElementById('detail_status').innerText = data.status;
            document.getElementById('detail_payment').innerText = data.payment;

            let list = '';
            items.forEach(item => {
                list += `
                <li class="flex justify-between items-center border-b border-slate-50 pb-2">
                    <div class="flex flex-col">
                        <span class="text-xs font-black text-slate-800 uppercase italic">${item.menu.name}</span>
                        <span class="text-[10px] font-bold text-slate-400">${item.qty}x @ Rp ${new Intl.NumberFormat().format(item.price)}</span>
                    </div>
                    <span class="text-xs font-black text-slate-900">Rp ${new Intl.NumberFormat().format(item.subtotal)}</span>
                </li>`;
            });

            document.getElementById('detail_items').innerHTML = list;
            detail_modal.showModal();
        }

        // Live Update every 30 seconds (Opsional)
        // setInterval(() => { window.location.reload(); }, 30000);
    </script>
</x-layouts.admin>