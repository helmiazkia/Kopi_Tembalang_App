<?php

namespace App\Http\Controllers\Kitchen;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;

class KitchenController extends Controller
{
    /**
     * Menampilkan daftar antrean pesanan yang harus dimasak.
     */
    public function index()
    {
        // Ambil pesanan status 'paid' atau 'preparing'
        // 'paid' = Pesanan baru masuk
        // 'preparing' = Sedang dibuat barista
        $orders = Order::with(['items.menu', 'items.options.optionItem', 'table'])
            ->whereIn('status', ['paid', 'preparing'])
            ->oldest() 
            ->get();

        return view('kitchen.index', compact('orders'));
    }

    /**
     * Alias untuk markAsDone agar sinkron dengan Route 'kitchen.ready'
     */
    public function markAsReady(Order $order)
    {
        return $this->markAsDone($order);
    }

    /**
     * Mengubah status pesanan menjadi Preparing (Sedang Dimasak)
     */
    public function startCooking(Order $order)
    {
        $order->update(['status' => 'preparing']);
        return back()->with('success', 'Pesanan #' . $order->id . ' sedang diproses.');
    }

    /**
     * Mengubah status pesanan menjadi Done (Selesai/Diserahkan)
     */
    public function markAsDone(Order $order)
    {
        try {
            $order->update([
                'status' => 'done'
            ]);

            return back()->with('success', 'Pesanan #' . $order->id . ' selesai!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses pesanan: ' . $e->getMessage());
        }
    }

    /**
     * Melihat riwayat pesanan selesai hari ini.
     */
    public function history()
    {
        $history = Order::where('status', 'done')
            ->whereDate('updated_at', Carbon::today())
            ->latest('updated_at')
            ->get();

        return view('kitchen.history', compact('history'));
    }
}