<x-layouts.admin title="Monitoring Orders">

    <div class="container mx-auto p-10">

        <h1 class="text-3xl font-semibold mb-6">
            Monitoring Orders
        </h1>

        <div class="overflow-x-auto bg-white p-5 shadow rounded">

            <table class="table">

                <thead>
                    <tr>
                        <th>No</th>
                        <th>Order</th>
                        <th>Meja</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Channel</th>
                        <th>Status</th>
                        <th>Kasir</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach($orders as $index => $order)

                    <tr>

                        <td>{{ $index+1 }}</td>

                        <td>#{{ $order->id }}</td>

                        <td>{{ $order->table->table_number }}</td>

                        <td>{{ $order->customer_name }}</td>

                        <td>Rp {{ number_format($order->total_price) }}</td>

                        <td>

                            @if($order->payment)

                            @if($order->payment->method == 'cash')
                            <span class="badge badge-success">Cash</span>

                            @elseif($order->payment->method == 'qris')
                            <span class="badge badge-info">QRIS</span>

                            @elseif($order->payment->method == 'ewallet')
                            <span class="badge badge-primary">E-Wallet</span>

                            @elseif($order->payment->method == 'va')
                            <span class="badge badge-warning">VA</span>

                            @elseif($order->payment->method == 'card')
                            <span class="badge badge-secondary">Card</span>
                            @endif

                            @else
                            <span class="badge badge-error">Belum Bayar</span>
                            @endif

                        </td>

                        <td>

                            @if($order->payment && $order->payment->channel)
                            {{ $order->payment->channel }}
                            @else
                            -
                            @endif

                        </td>

                        <td>

                            @if($order->status == 'pending')
                            <span class="badge badge-warning">Pending</span>

                            @elseif($order->status == 'paid')
                            <span class="badge badge-info">Paid</span>

                            @elseif($order->status == 'preparing')
                            <span class="badge badge-primary">Preparing</span>

                            @elseif($order->status == 'ready')
                            <span class="badge badge-success">Ready</span>

                            @elseif($order->status == 'done')
                            <span class="badge badge-success">Done</span>

                            @else
                            <span class="badge badge-error">Cancelled</span>
                            @endif

                        </td>

                        <td>
                            {{ $order->cashier->name ?? '-' }}
                        </td>

                        <td>

                            <button
                                class="btn btn-sm btn-info"
                                onclick="openDetailModal(this)"
                                data-id="{{ $order->id }}"
                                data-table="{{ $order->table->table_number }}"
                                data-customer="{{ $order->customer_name }}"
                                data-total="{{ number_format($order->total_price) }}"
                                data-status="{{ $order->status }}"
                                data-payment="{{ $order->payment->method ?? 'Belum Bayar' }}"
                                data-channel="{{ $order->payment->channel ?? '-' }}"
                                data-items='@json($order->items)'>
                                Detail
                            </button>

                        </td>

                    </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    </div>


    {{-- DETAIL MODAL --}}
    <dialog id="detail_modal" class="modal">

        <div class="modal-box max-w-2xl">

            <h3 class="font-bold text-lg mb-4">
                Detail Order
            </h3>

            <p><b>Order ID:</b> <span id="detail_id"></span></p>
            <p><b>Meja:</b> <span id="detail_table"></span></p>
            <p><b>Customer:</b> <span id="detail_customer"></span></p>
            <p><b>Status:</b> <span id="detail_status"></span></p>
            <p><b>Payment:</b> <span id="detail_payment"></span></p>
            <p><b>Channel:</b> <span id="detail_channel"></span></p>
            <p><b>Total:</b> Rp <span id="detail_total"></span></p>

            <hr class="my-4">

            <h4 class="font-semibold mb-2">
                Menu
            </h4>

            <ul id="detail_items"></ul>

            <div class="modal-action">
                <button class="btn" onclick="detail_modal.close()">
                    Tutup
                </button>
            </div>

        </div>

    </dialog>


    <script>
        function openDetailModal(btn) {

            const id = btn.dataset.id
            const table = btn.dataset.table
            const customer = btn.dataset.customer
            const total = btn.dataset.total
            const status = btn.dataset.status
            const payment = btn.dataset.payment
            const channel = btn.dataset.channel
            const items = JSON.parse(btn.dataset.items)

            document.getElementById('detail_id').innerText = id
            document.getElementById('detail_table').innerText = table
            document.getElementById('detail_customer').innerText = customer
            document.getElementById('detail_total').innerText = total
            document.getElementById('detail_status').innerText = status
            document.getElementById('detail_payment').innerText = payment
            document.getElementById('detail_channel').innerText = channel

            let list = ''

            items.forEach(item => {

                list += `<li class="mb-2">
${item.qty}x ${item.menu.name}
`

                if (item.options && item.options.length > 0) {

                    list += `<ul class="ml-4 text-sm text-gray-500">`

                    item.options.forEach(opt => {

                        if (opt.option_item) {

                            let optionName = opt.option_item.name
                            let optionType = opt.option_item.menu_option?.name ?? ''

                            list += `<li>
${optionType} : ${optionName}
${opt.price > 0 ? `( +Rp ${opt.price} )` : ``}
</li>`

                        }

                    })

                    list += `</ul>`

                }

                list += `</li>`

            })

            document.getElementById('detail_items').innerHTML = list

            detail_modal.showModal()

        }
    </script>

</x-layouts.admin>  