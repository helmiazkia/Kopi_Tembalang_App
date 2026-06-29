<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Order;

class Payment extends Model
{

    protected $fillable = [
        'order_id',
        'transaction_id',
        'method',
        'channel',
        'amount',
        'status',
        'snap_token',
        'expired_at',
        'paid_at'
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'expired_at' => 'datetime',
        'amount' => 'integer'
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /*
    |--------------------------------------------------------------------------
    | METHODS
    |--------------------------------------------------------------------------
    */

    public function isExpired()
    {
        if (!$this->expired_at) {
            return false;
        }
        return now()->isAfter($this->expired_at);
    }

    // 🔥 STATIC METHOD - Auto cancel semua expired pending payments
    public static function cancelAllExpired()
    {
        $expiredPayments = self::where('status', 'pending')
            ->whereNotNull('expired_at')
            ->where('expired_at', '<', now())
            ->get();

        foreach ($expiredPayments as $payment) {
            $payment->update(['status' => 'expired']);
            $payment->order->update(['status' => 'cancelled']);
        }

        return count($expiredPayments);
    }

}