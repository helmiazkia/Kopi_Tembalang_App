<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyReport extends Model
{
    protected $fillable = [
        'date',
        'total_orders',
        'total_income',
        'total_cash',
        'total_qris'
    ];
}
