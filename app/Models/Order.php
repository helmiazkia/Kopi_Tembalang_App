<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'table_id',
        'cashier_id',
        'customer_name',
        'phone',
        'notes',
        'total_price',
        'status'
    ];

    public function table()
    {
        return $this->belongsTo(Table::class);
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
