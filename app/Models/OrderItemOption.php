<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItemOption extends Model
{
    protected $fillable = [
        'order_item_id',
        'menu_option_item_id',
        'price'
    ];

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function optionItem()
    {
        return $this->belongsTo(MenuOptionItem::class, 'menu_option_item_id');
    }
}
