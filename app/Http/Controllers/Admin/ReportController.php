<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Exports\OrdersExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // 🔥 AUTO-CANCEL EXPIRED PAYMENTS DULUAN
        Payment::cancelAllExpired();

        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate   = $request->get('end_date', now()->format('Y-m-d'));

        $orders = Order::with(['table', 'cashier', 'payment'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->latest()
            ->get();

        $summary = $this->buildSummary($startDate, $endDate, $orders);

        return view('admin.reports.index', compact('orders', 'startDate', 'endDate', 'summary'));
    }

    public function export(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate   = $request->get('end_date', now()->format('Y-m-d'));

        return Excel::download(
            new OrdersExport($startDate, $endDate),
            "Laporan_KopiTembalang_{$startDate}_to_{$endDate}.xlsx"
        );
    }

    public function exportPdf(Request $request)
    {
        // 🔥 AUTO-CANCEL EXPIRED PAYMENTS DULUAN
        Payment::cancelAllExpired();

        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate   = $request->get('end_date', now()->format('Y-m-d'));

        $orders = Order::with(['table', 'cashier', 'payment'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->orderBy('created_at')
            ->get();

        $summary = $this->buildSummary($startDate, $endDate, $orders);

        $pdf = Pdf::loadView('admin.reports.pdf', compact('orders', 'startDate', 'endDate', 'summary'))
            ->setPaper('a4', 'landscape');

        return $pdf->download("Laporan_KopiTembalang_{$startDate}_to_{$endDate}.pdf");
    }

    /**
     * Bangun summary laporan.
     * Hanya order dengan status 'paid' / 'done' yang dihitung sebagai omzet & nota berhasil.
     * Order 'pending' / 'preparing' / 'failed' / 'cancelled' tetap tampil di tabel,
     * tapi TIDAK dihitung ke omzet.
     */
    private function buildSummary(string $startDate, string $endDate, $orders): array
    {
        $totalCash = Payment::where('status', 'paid')
            ->where('method', 'cash')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->sum('amount');

        $totalOnline = Payment::where('status', 'paid')
            ->where('method', '!=', 'cash')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->sum('amount');

        return [
            'total_cash'    => $totalCash,
            'total_online'  => $totalOnline,
            'total_omzet'   => $totalCash + $totalOnline,
            'count_orders'  => $orders->whereIn('status', ['paid', 'done'])->count(),
            'count_pending' => $orders->whereIn('status', ['pending', 'preparing'])->count(),
            'count_failed'  => $orders->whereIn('status', ['failed', 'cancelled'])->count(),
        ];
    }
}