<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Exports\OrdersExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate   = $request->get('end_date', now()->format('Y-m-d'));

        $orders = Order::with(['table', 'cashier', 'payment'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->latest()
            ->get();

        $totalCash = Payment::where('status', 'paid')
            ->where('method', 'cash')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->sum('amount');

        $totalOnline = Payment::where('status', 'paid')
            ->where('method', '!=', 'cash')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->sum('amount');

        $summary = [
            'total_cash'    => $totalCash,
            'total_online'  => $totalOnline,
            'total_omzet'   => $totalCash + $totalOnline, // ✅ ganti void
            'count_orders'  => $orders->where('status', '!=', 'cancelled')->count(),
        ];

        return view('admin.reports.index', compact('orders', 'startDate', 'endDate', 'summary'));
    }

    public function export(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate   = $request->get('end_date', now()->format('Y-m-d'));

        return Excel::download(
            new OrdersExport($startDate, $endDate),
            "Laporan_LodoKopi_{$startDate}_to_{$endDate}.xlsx"
        );
    }
}