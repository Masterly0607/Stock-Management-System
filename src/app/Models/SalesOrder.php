<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SalesOrder extends Model
{
    public function items()
    {
        return $this->hasMany(SalesOrderItem::class);
    }
    // If your service uses $order->lines, add:
    public function lines()
    {
        return $this->items();
    }
}
