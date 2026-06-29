<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Transaksi - Kopi Tembalang</title>
    <style>
        @page {
            margin: 25px 30px;
        }
        body {
            font-family: 'Helvetica', sans-serif;
            color: #334155;
            font-size: 10px;
        }
        .header {
            background-color: #1e293b;
            color: #ffffff;
            padding: 14px 18px;
            border-radius: 6px;
            margin-bottom: 14px;
        }
        .header .brand {
            font-size: 8px;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #94a3b8;
            margin: 0 0 2px 0;
        }
        .header .title {
            font-size: 16px;
            font-weight: bold;
            margin: 0 0 4px 0;
        }
        .header .period {
            font-size: 9px;
            color: #d4e971;
            margin: 0;
        }

        /* Summary cards */
        .summary-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 6px;
            margin-bottom: 14px;
        }
        .summary-table td {
            width: 20%;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 12px;
            vertical-align: top;
        }
        .summary-table .label {
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #94a3b8;
            margin: 0 0 4px 0;
        }
        .summary-table .value {
            font-size: 14px;
            font-weight: bold;
            color: #0f172a;
            margin: 0;
        }
        .summary-table .omzet-cell {
            background-color: #1e293b;
            border-color: #1e293b;
        }
        .summary-table .omzet-cell .label {
            color: #94a3b8;
        }
        .summary-table .omzet-cell .value {
            color: #d4e971;
        }
        .summary-table .warn-cell .value {
            font-size: 12px;
        }

        /* Main table */
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }
        table.data thead th {
            background-color: #334155;
            color: #ffffff;
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 7px 6px;
            text-align: left;
            border: 1px solid #334155;
        }
        table.data tbody td {
            padding: 6px 6px;
            border: 1px solid #e2e8f0;
            font-size: 9px;
        }
        table.data tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }
        table.data tbody tr.not-counted {
            color: #94a3b8;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-cash { background-color: #d1fae5; color: #047857; }
        .badge-online { background-color: #e0e7ff; color: #4338ca; }
        .badge-paid { background-color: #d1fae5; color: #047857; }
        .badge-preparing { background-color: #fef3c7; color: #92400e; }
        .badge-pending { background-color: #fefce8; color: #a16207; }
        .badge-done { background-color: #dbeafe; color: #1e40af; }
        .badge-failed { background-color: #fee2e2; color: #b91c1c; }
        .badge-cancelled { background-color: #fee2e2; color: #b91c1c; }
        .badge-default { background-color: #f1f5f9; color: #64748b; }

        tfoot td {
            background-color: #1e293b !important;
            color: #d4e971;
            font-weight: bold;
            font-size: 9px;
            padding: 8px 6px;
            border: 1px solid #1e293b;
        }

        .footer-note {
            margin-top: 14px;
            font-size: 8px;
            font-style: italic;
            color: #94a3b8;
        }
    </style>
</head>
<body>

    <div class="header">
        <p class="brand">Kopi Tembalang Intelligence</p>
        <p class="title">Laporan Transaksi</p>
        <p class="period">
            {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}
            @if($startDate != $endDate)
                — {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
            @endif
        </p>
    </div>

    <table class="summary-table">
        <tr>
            <td>
                <p class="label">Setoran Tunai</p>
                <p class="value">Rp {{ number_format($summary['total_cash']) }}</p>
            </td>
            <td>
                <p class="label">Online Payment</p>
                <p class="value">Rp {{ number_format($summary['total_online']) }}</p>
            </td>
            <td>
                <p class="label">Nota Berhasil</p>
                <p class="value">{{ $summary['count_orders'] }} Nota</p>
            </td>
            <td class="warn-cell">
                <p class="label">Pending / Gagal</p>
                <p class="value">{{ $summary['count_pending'] }} / {{ $summary['count_failed'] }}</p>
            </td>
            <td class="omzet-cell">
                <p class="label">Total Omzet</p>
                <p class="value">Rp {{ number_format($summary['total_omzet']) }}</p>
            </td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th class="text-center" style="width:3%;">No</th>
                <th style="width:8%;">Tanggal</th>
                <th style="width:6%;">Jam</th>
                <th style="width:7%;">Order ID</th>
                <th style="width:15%;">Pelanggan / Meja</th>
                <th style="width:13%;">Kasir</th>
                <th style="width:9%;">Metode</th>
                <th style="width:10%;">Status</th>
                <th class="text-right" style="width:12%;">Total (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $index => $order)
                @php
                    $isCounted = in_array($order->status, ['paid', 'done']);
                    $statusClass = match($order->status) {
                        'paid' => 'badge-paid',
                        'preparing' => 'badge-preparing',
                        'pending' => 'badge-pending',
                        'done' => 'badge-done',
                        'failed' => 'badge-failed',
                        'cancelled' => 'badge-cancelled',
                        default => 'badge-default',
                    };
                    $methodClass = $order->payment?->method === 'cash' ? 'badge-cash' : 'badge-online';
                @endphp
                <tr class="{{ !$isCounted ? 'not-counted' : '' }}">
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $order->created_at->format('d/m/Y') }}</td>
                    <td>{{ $order->created_at->format('H:i') }}</td>
                    <td>#{{ $order->id }}</td>
                    <td>
                        {{ strtoupper($order->customer_name) }}<br>
                        <span style="color:#94a3b8; font-size:7.5px;">
                            {{ $order->table?->table_number ?? 'Take Away' }}
                        </span>
                    </td>
                    <td>{{ $order->cashier?->name ?? 'Self-Order' }}</td>
                    <td>
                        @if($order->payment)
                            @if($order->payment->method === 'cash')
                                <span class="badge badge-cash">Cash</span>
                            @else
                                <span class="badge badge-online">Online Payment</span><br>
                                <span style="font-size: 7px; color: #4338ca; text-transform: uppercase; font-weight: bold; margin-top: 2px; display: block;">
                                    {{ $order->payment->channel ?? $order->payment->method }}
                                </span>
                            @endif
                        @else
                            <span class="badge badge-cancelled">Belum Bayar</span>
                        @endif
                    </td>
                    <td><span class="badge {{ $statusClass }}">{{ $order->status }}</span></td>
                    <td class="text-right">{{ number_format($order->total_price) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 20px;">
                        Tidak ada data pada rentang tanggal ini
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if($orders->count() > 0)
        <tfoot>
            <tr>
                <td colspan="4">TOTAL TRANSAKSI BERHASIL</td>
                <td colspan="4" class="text-center">{{ $summary['count_orders'] }} / {{ $orders->count() }} Nota</td>
                <td class="text-right">Rp {{ number_format($summary['total_omzet']) }}</td>
            </tr>
        </tfoot>
        @endif
    </table>

    <p class="footer-note">
        Digenerate pada: {{ now()->format('d/m/Y H:i') }} | Kopi Tembalang | Status pending/gagal/batal tidak dihitung ke omzet
    </p>

</body>
</html>