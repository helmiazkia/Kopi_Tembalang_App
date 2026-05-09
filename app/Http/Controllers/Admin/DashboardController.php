<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Payment;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TopMenusExport;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Filter Bulan & Tahun (Default ke Bulan Berjalan)
        $filterMonth = $request->get('month', now()->month);
        $filterYear  = $request->get('year', now()->year);
        $menuSort    = $request->get('menu_sort', 'top');

        // --- 1. DATA ANALITIK BULANAN ---
        $totalRevenue = Payment::where('status', 'paid')
            ->whereMonth('created_at', $filterMonth)
            ->whereYear('created_at', $filterYear)
            ->sum('amount');

        $orderKasir = Order::whereMonth('created_at', $filterMonth)
            ->whereYear('created_at', $filterYear)
            ->whereNotNull('cashier_id')
            ->count();

        $orderMandiri = Order::whereMonth('created_at', $filterMonth)
            ->whereYear('created_at', $filterYear)
            ->whereNull('cashier_id')
            ->count();

        $totalItemsSold = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereMonth('orders.created_at', $filterMonth)
            ->whereYear('orders.created_at', $filterYear)
            ->whereIn('orders.status', ['paid', 'preparing', 'done'])
            ->sum('order_items.qty');

        // --- 2. DATA GRAFIK ---
        $busyDaysRaw = Order::whereMonth('created_at', $filterMonth)
            ->whereYear('created_at', $filterYear)
            ->select(DB::raw('DAYNAME(created_at) as day'), DB::raw('count(*) as count'))
            ->groupBy('day')
            ->pluck('count', 'day')
            ->all();

        $fullDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $busyDaysValues = [];
        foreach ($fullDays as $day) {
            $busyDaysValues[] = $busyDaysRaw[$day] ?? 0;
        }
        $dayLabels = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];

        $busyHours = Order::whereMonth('created_at', $filterMonth)
            ->whereYear('created_at', $filterYear)
            ->select(DB::raw('HOUR(created_at) as hour'), DB::raw('count(*) as count'))
            ->groupBy('hour')
            ->pluck('count', 'hour')
            ->all();

        $hourValues = [];
        for ($i = 0; $i < 24; $i++) {
            $hourValues[] = $busyHours[$i] ?? 0;
        }

        // --- 3. TOP MENU ---
        $topMenus = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('menus', 'order_items.menu_id', '=', 'menus.id')
            ->whereMonth('orders.created_at', $filterMonth)
            ->whereYear('orders.created_at', $filterYear)
            ->whereIn('orders.status', ['paid', 'preparing', 'done'])
            ->select('menus.name', DB::raw('SUM(order_items.qty) as total_qty'))
            ->groupBy('menus.name')
            ->orderBy('total_qty', $menuSort === 'top' ? 'DESC' : 'ASC')
            ->limit(5)
            ->get();

        // --- 4. TOP OPTION ITEMS ---
        $topOptions = DB::table('order_item_options')
            ->join('order_items', 'order_item_options.order_item_id', '=', 'order_items.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('menu_option_items', 'order_item_options.menu_option_item_id', '=', 'menu_option_items.id')
            ->join('menu_options', 'menu_option_items.menu_option_id', '=', 'menu_options.id')
            ->whereMonth('orders.created_at', $filterMonth)
            ->whereYear('orders.created_at', $filterYear)
            ->whereIn('orders.status', ['paid', 'preparing', 'done'])
            ->select(
                'menu_options.name as group_name',
                'menu_option_items.name as option_name',
                DB::raw('COUNT(*) as total_dipilih')
            )
            ->groupBy('menu_options.name', 'menu_option_items.name')
            ->orderBy('total_dipilih', $menuSort === 'top' ? 'DESC' : 'ASC')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'filterMonth',
            'filterYear',
            'menuSort',
            'totalRevenue',
            'orderKasir',
            'orderMandiri',
            'totalItemsSold',
            'dayLabels',
            'busyDaysValues',
            'hourValues',
            'topMenus',
            'topOptions',
        ));
    }

    public function exportTopMenus(Request $request)
    {
        $filterMonth = $request->get('month', now()->month);
        $filterYear  = $request->get('year', now()->year);

        $filename = "TopMenu_{$filterYear}-{$filterMonth}.xlsx";

        return Excel::download(new TopMenusExport($filterMonth, $filterYear), $filename);
    }
}