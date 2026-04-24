<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = [
        'name',
        'category_id',
        'price',
        'description',
        'image',
        'is_available'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function options()
    {
        return $this->belongsToMany(MenuOption::class);
    }
}
