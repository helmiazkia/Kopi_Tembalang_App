<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $filterDate = $request->get('date') ? Carbon::parse($request->get('date')) : Carbon::today();
        $cashierId  = auth()->id();

        // ══════════════════════════════════════════════════════════════
        // BAGIAN A: DATA PERSONAL KASIR YANG LOGIN
        // ══════════════════════════════════════════════════════════════

        /**
         * PENDAPATAN ANDA
         * = order yang dia input (cash + QRIS)
         * + self-order customer yang bayar CASH (uang masuk laci dia)
         * 
         * Self-order QRIS tidak ikut karena uangnya masuk rekening toko, bukan laci kasir.
         */
        $totalIncome = Payment::join('orders', 'payments.order_id', '=', 'orders.id')
            ->whereDate('payments.created_at', $filterDate)
            ->where('payments.status', 'paid')
            ->where(function ($q) use ($cashierId) {
                $q->where('orders.cashier_id', $cashierId)    // order kasir ini (cash + QRIS)
                    ->orWhere(function ($q2) {
                        $q2->whereNull('orders.cashier_id')   // self-order customer
                            ->where('payments.method', 'cash'); // hanya yang cash (masuk laci)
                    });
            })
            ->sum('payments.amount');

        /**
         * UANG TUNAI (kartu hijau)
         * = cash dari order kasir ini
         * + cash dari self-order customer (tetap masuk laci fisik kasir)
         */
        $totalCash = Payment::join('orders', 'payments.order_id', '=', 'orders.id')
            ->whereDate('payments.created_at', $filterDate)
            ->where('payments.status', 'paid')
            ->where('payments.method', 'cash')
            ->where(function ($q) use ($cashierId) {
                $q->where('orders.cashier_id', $cashierId)
                    ->orWhereNull('orders.cashier_id');
            })
            ->sum('payments.amount');

        /**
         * ONLINE PAYMENT (kartu indigo)
         * = QRIS/online dari order yang kasir ini input saja
         * (self-order QRIS masuk ke $selfOrderIncome, bukan milik kasir)
         */
        $totalOnline = Payment::join('orders', 'payments.order_id', '=', 'orders.id')
            ->whereDate('payments.created_at', $filterDate)
            ->where('payments.status', 'paid')
            ->where('payments.method', '!=', 'cash')
            ->where('orders.cashier_id', $cashierId)
            ->sum('payments.amount');

        /**
         * MONITOR AKTIVITAS — personal kasir yang login
         * Self-order (cashier_id null) tidak dihitung karena kasir tidak terlibat
         */
        $orderPending = Order::whereDate('created_at', $filterDate)
            ->where('cashier_id', $cashierId)
            ->where('status', 'pending')
            ->count();

        $orderSelesai = Order::whereDate('created_at', $filterDate)
            ->where('cashier_id', $cashierId)
            ->whereIn('status', ['paid', 'preparing', 'done'])
            ->count();

        /**
         * TREN PELAYANAN PER JAM — personal kasir yang login
         * Pakai updated_at karena status berubah ke 'paid' saat kasir approve
         */
        $hourlyData = Order::where('cashier_id', $cashierId)
            ->whereDate('updated_at', $filterDate)
            ->whereIn('status', ['paid', 'preparing', 'done'])
            ->select(DB::raw('HOUR(updated_at) as hour'), DB::raw('count(*) as count'))
            ->groupBy('hour')
            ->get()
            ->pluck('count', 'hour')
            ->all();

        // ══════════════════════════════════════════════════════════════
        // BAGIAN B: DATA GLOBAL TOKO (AUDIT SETORAN UNIT)
        // ══════════════════════════════════════════════════════════════

        /**
         * SELF-ORDER ONLINE (rekening toko)
         * = QRIS dari customer yang self-order (cashier_id null)
         * Uang ini masuk rekening toko, bukan laci kasir manapun
         */
        $selfOrderIncome = Payment::join('orders', 'payments.order_id', '=', 'orders.id')
            ->whereDate('payments.created_at', $filterDate)
            ->where('payments.status', 'paid')
            ->where('payments.method', '!=', 'cash')
            ->whereNull('orders.cashier_id')
            ->sum('payments.amount');

        /**
         * ONLINE PAYMENT TOTAL (rekening toko)
         * = semua transaksi QRIS/online dari seluruh toko
         * (kasir A + kasir B + self-order)
         */
        $grandTotalOnline = Payment::where('status', 'paid')
            ->where('method', '!=', 'cash')
            ->whereDate('created_at', $filterDate)
            ->sum('amount');

        /**
         * TOTAL TUNAI FISIK (laci gabungan semua kasir)
         * = semua transaksi cash dari seluruh toko
         * (kasir A + kasir B + self-order yang bayar cash)
         * Cocokkan dengan laci fisik saat tutup shift
         */
        $grandTotalCash = Payment::where('status', 'paid')
            ->where('method', 'cash')
            ->whereDate('created_at', $filterDate)
            ->sum('amount');

        /**
         * GRAND TOTAL TOKO
         * = semua transaksi paid dari seluruh toko (cash + online)
         */
        $grandTotalIncome = Payment::where('status', 'paid')
            ->whereDate('created_at', $filterDate)
            ->sum('amount');

        // ══════════════════════════════════════════════════════════════
        // CHART DATA
        // ══════════════════════════════════════════════════════════════
        $chartLabels = [];
        $chartValues = [];
        for ($i = 0; $i < 24; $i++) {
            $chartLabels[] = sprintf('%02d:00', $i);
            $chartValues[] = $hourlyData[$i] ?? 0;
        }

        return view('cashier.dashboard', compact(
            // Personal kasir
            'totalIncome',
            'totalCash',
            'totalOnline',
            'orderPending',
            'orderSelesai',
            'chartLabels',
            'chartValues',
            // Global toko
            'selfOrderIncome',
            'grandTotalOnline',
            'grandTotalCash',
            'grandTotalIncome',
            // Filter
            'filterDate',
        ));
    }
}