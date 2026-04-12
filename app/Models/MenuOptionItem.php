<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuOptionItem extends Model
{
    protected $fillable = [
        'menu_option_id',
        'name',
        'price'
    ];

    public function option()
    {
        return $this->belongsTo(MenuOption::class, 'menu_option_id');
    }

    public function orderItemOptions()
    {
        return $this->hasMany(OrderItemOption::class);
    }
}
