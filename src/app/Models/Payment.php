<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = ['sales_order_id', 'amount', 'currency', 'method', 'paid_at', 'received_by'];

    protected $casts = [
        'sales_order_id' => 'integer',
        'amount'         => 'decimal:2',
        'paid_at'        => 'datetime',
        'received_by'    => 'integer',
    ];

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }
    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
