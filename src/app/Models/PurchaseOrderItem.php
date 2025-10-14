<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $fillable = ['purchase_order_id', 'product_id', 'unit_id', 'qty_ordered', 'qty_received', 'unit_cost', 'line_total'];

    protected $casts = [
        'purchase_order_id' => 'integer',
        'product_id'        => 'integer',
        'unit_id'           => 'integer',
        'qty_ordered'       => 'decimal:2',
        'qty_received'      => 'decimal:2',
        'unit_cost'         => 'decimal:4',
        'line_total'        => 'decimal:2',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
