<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'description',
        'image',
    ];

    public function menus()
    {
        return $this->hasMany(Menu::class);
    }
}
