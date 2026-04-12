<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class MenuOption extends Model
{
    protected $fillable = [
        'menu_id',
        'name',
        'type'
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function items()
    {
        return $this->hasMany(MenuOptionItem::class);
    }
}
