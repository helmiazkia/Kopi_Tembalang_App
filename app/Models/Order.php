<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Table;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\OrderLog;
use App\Models\Receipt;
use App\Models\User;

class Order extends Model
{
    protected $fillable = [
        'table_id',
        'cashier_id',
        'customer_name',
        'email',
        'phone',
        'notes',
        'total_price',
        'status'
    ];

    protected $casts = [
        'total_price' => 'integer'
    ];

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function logs()
    {
        return $this->hasMany(OrderLog::class);
    }

    public function receipt()
    {
        return $this->hasOne(Receipt::class);
    }
}
